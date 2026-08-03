<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingRequest;
use App\Models\Page;
use App\Models\StoreSetting;
use App\Services\StoreSettingService;
use App\Support\ImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreSettingController extends Controller
{
    public function edit(StoreSettingService $settings): View
    {
        $settings = $settings->current();
        $aboutPage = Page::query()->where('slug', Page::SLUG_ABOUT)->first();
        $contactPage = Page::query()->where('slug', Page::SLUG_CONTACT)->first();

        return view('admin.store-settings.edit', compact('settings', 'aboutPage', 'contactPage'));
    }

    public function update(StoreSettingRequest $request, StoreSettingService $settingsService): RedirectResponse
    {
        $settings = StoreSetting::current();
        $data = $request->safe()->except(['about_image']);

        $data['contact_phone_alt'] = $request->input('contact_phone_alt');
        $data['contact_website'] = $request->input('contact_website');
        $data['contact_page_phone'] = $request->input('contact_page_phone');
        $data['contact_page_phone_alt'] = $request->input('contact_page_phone_alt');
        $data['contact_page_address'] = $request->input('contact_page_address');
        $data['kitchen_sms_enabled'] = $request->boolean('kitchen_sms_enabled');
        $data['kitchen_whatsapp_enabled'] = $request->boolean('kitchen_whatsapp_enabled');
        $data['online_ordering_enabled'] = $request->boolean('online_ordering_enabled');
        $data['low_stock_threshold'] = $request->filled('low_stock_threshold')
            ? $request->integer('low_stock_threshold')
            : 10;
        $data['upsell_category_slugs'] = $request->input('upsell_category_slugs', []);
        $data['business_hours'] = $request->input('business_hours');

        unset($data['business_hours_note']);

        if ($request->hasFile('about_image')) {
            if ($settings->about_image_path && ! str_starts_with($settings->about_image_path, 'images/')) {
                Storage::disk('public')->delete($settings->about_image_path);
            }

            $data['about_image_path'] = ImageUpload::store($request->file('about_image'), 'about', 5120, 2400);
        }

        $settings->update($data);
        $settingsService->applyToConfig();

        return redirect()
            ->route('admin.store-settings.edit')
            ->with('success', 'Store settings updated successfully.');
    }
}
