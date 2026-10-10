<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingRequest;
use App\Models\Page;
use App\Models\StoreSetting;
use App\Services\StoreSettingService;
use App\Support\ImageUpload;
use App\Support\StoreSettingSchema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class StoreSettingController extends Controller
{
    /**
     * @var array<string, array{column: string, directory: string, maxKb: int, maxDimension: int}>
     */
    private const IMAGE_UPLOADS = [
        'about_image' => [
            'column' => 'about_image_path',
            'directory' => 'about',
            'maxKb' => 5120,
            'maxDimension' => 2400,
        ],
        'logo' => [
            'column' => 'logo_path',
            'directory' => 'brand',
            'maxKb' => 2048,
            'maxDimension' => 1200,
        ],
        'logo_text' => [
            'column' => 'logo_text_path',
            'directory' => 'brand',
            'maxKb' => 2048,
            'maxDimension' => 1600,
        ],
        'logo_text_on_light' => [
            'column' => 'logo_text_on_light_path',
            'directory' => 'brand',
            'maxKb' => 2048,
            'maxDimension' => 1600,
        ],
        'footer_logo' => [
            'column' => 'footer_logo_path',
            'directory' => 'brand',
            'maxKb' => 2048,
            'maxDimension' => 1600,
        ],
    ];

    public function edit(StoreSettingService $settings): View
    {
        StoreSettingSchema::ensureTaxColumns();

        $settings = $settings->current();
        $aboutPage = Page::query()->where('slug', Page::SLUG_ABOUT)->first();
        $contactPage = Page::query()->where('slug', Page::SLUG_CONTACT)->first();

        return view('admin.store-settings.edit', compact('settings', 'aboutPage', 'contactPage'));
    }

    public function update(StoreSettingRequest $request, StoreSettingService $settingsService): RedirectResponse
    {
        if (! StoreSettingSchema::ensureTaxColumns()) {
            return back()
                ->withInput()
                ->with('error', 'Could not create tax database columns. On the server run: php artisan migrate --force — or in phpMyAdmin: ALTER TABLE store_settings ADD COLUMN tax_enabled TINYINT(1) NOT NULL DEFAULT 0, ADD COLUMN tax_rate DECIMAL(8,4) NOT NULL DEFAULT 0, ADD COLUMN tax_label VARCHAR(255) NULL;');
        }

        $settings = StoreSetting::current();
        $data = $request->safe()->except(array_keys(self::IMAGE_UPLOADS));

        $data['contact_phone_alt'] = $request->input('contact_phone_alt');
        $data['contact_website'] = $request->input('contact_website');
        $data['contact_page_phone'] = $request->input('contact_page_phone');
        $data['contact_page_phone_alt'] = $request->input('contact_page_phone_alt');
        $data['contact_page_address'] = $request->input('contact_page_address');
        $data['kitchen_sms_enabled'] = $request->boolean('kitchen_sms_enabled');
        $data['kitchen_whatsapp_enabled'] = $request->boolean('kitchen_whatsapp_enabled');
        $data['online_ordering_enabled'] = $request->boolean('online_ordering_enabled');
        $data['tax_enabled'] = $request->boolean('tax_enabled');
        $rawRate = (float) $request->input('tax_rate', 0);
        // Persist as a fraction (0.15). If a percent snuck through, normalize it.
        if ($rawRate > 1) {
            $rawRate = $rawRate / 100;
        }
        $data['tax_rate'] = round(max(0, min(1, $rawRate)), 4);
        $data['tax_label'] = filled($request->input('tax_label'))
            ? trim((string) $request->input('tax_label'))
            : 'Tax';
        $data['low_stock_threshold'] = $request->filled('low_stock_threshold')
            ? $request->integer('low_stock_threshold')
            : 10;
        $data['upsell_category_slugs'] = $request->input('upsell_category_slugs', []);
        $data['business_hours'] = $request->input('business_hours');

        unset($data['business_hours_note'], $data['tax_rate_percent']);

        foreach (self::IMAGE_UPLOADS as $input => $meta) {
            if (! $request->hasFile($input)) {
                continue;
            }

            $file = $request->file($input);

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $column = $meta['column'];
            $existing = $settings->{$column};

            if ($existing && ! str_starts_with($existing, 'images/')) {
                Storage::disk('public')->delete($existing);
            }

            $data[$column] = ImageUpload::store(
                $file,
                $meta['directory'],
                $meta['maxKb'],
                $meta['maxDimension'],
            );
        }

        if ($request->boolean('remove_footer_logo') && ! $request->hasFile('footer_logo')) {
            if ($settings->footer_logo_path && ! str_starts_with($settings->footer_logo_path, 'images/')) {
                Storage::disk('public')->delete($settings->footer_logo_path);
            }
            $data['footer_logo_path'] = null;
        }

        try {
            $settings->update($data);
            $settingsService->applyToConfig();
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Could not save store settings. On the server run: php artisan migrate --force && php artisan optimize:clear');
        }

        return redirect()
            ->route('admin.store-settings.edit')
            ->with('success', 'Store settings updated successfully.');
    }
}
