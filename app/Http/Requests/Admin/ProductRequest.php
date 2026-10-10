<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Support\ImageUpload;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(Product::class)->ignore($productId),
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Product::class)->ignore($productId),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(ProductStatus::class)],
            'publish_date' => ['nullable', 'date'],
            'publish_time' => ['nullable', 'date_format:H:i'],
            'published_at' => ['nullable', 'date'],
            'images' => ['nullable', 'array'],
            'images.*' => [
                'required',
                ImageUpload::typesRule(),
            ],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.heel_length' => ['nullable', 'string', 'max:50'],
            // Optional for restaurants that cook to order. Empty values default to cook-to-order stock.
            'variants.*.quantity' => ['nullable', 'integer', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Default portion count when staff leave quantity blank (cook-to-order menus).
     */
    public const DEFAULT_COOK_TO_ORDER_QTY = 999;

    public const DEFAULT_PORTION = 'Regular';

    public const DEFAULT_OPTION = 'Standard';

    protected function prepareForValidation(): void
    {
        $date = $this->input('publish_date');
        $time = $this->input('publish_time');

        if (filled($date) && filled($time)) {
            $this->merge([
                'published_at' => Carbon::parse("{$date} {$time}", config('app.timezone'))->toDateTimeString(),
            ]);
        } elseif (filled($date)) {
            $this->merge([
                'published_at' => Carbon::parse("{$date} 00:00", config('app.timezone'))->toDateTimeString(),
            ]);
        } else {
            $this->merge(['published_at' => null]);
        }

        $variants = collect(Arr::wrap($this->input('variants', [])))
            ->filter(fn ($variant) => is_array($variant))
            ->map(function ($variant) {
                $variant['size'] = filled($variant['size'] ?? null)
                    ? trim((string) $variant['size'])
                    : self::DEFAULT_PORTION;

                $variant['color'] = filled($variant['color'] ?? null)
                    ? trim((string) $variant['color'])
                    : self::DEFAULT_OPTION;

                if (! array_key_exists('quantity', $variant) || $variant['quantity'] === '' || $variant['quantity'] === null) {
                    $variant['quantity'] = self::DEFAULT_COOK_TO_ORDER_QTY;
                }

                return $variant;
            })
            ->values()
            ->all();

        if ($variants === []) {
            $variants = [[
                'size' => self::DEFAULT_PORTION,
                'color' => self::DEFAULT_OPTION,
                'heel_length' => null,
                'quantity' => self::DEFAULT_COOK_TO_ORDER_QTY,
                'sku' => null,
                'is_active' => true,
            ]];
        }

        $this->merge(['variants' => $variants]);

        if (! $this->hasFile('images')) {
            $this->request->remove('images');

            return;
        }

        $images = collect(Arr::wrap($this->file('images')))
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->isValid())
            ->values()
            ->all();

        if ($images === []) {
            $this->files->remove('images');
            $this->request->remove('images');

            return;
        }

        $this->files->set('images', $images);
    }

    /**
     * @return list<UploadedFile>
     */
    public function uploadedImages(): array
    {
        return collect(Arr::wrap($this->file('images', [])))
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->isValid())
            ->values()
            ->all();
    }

    public function attributes(): array
    {
        return [
            'name' => 'dish name',
            'sku' => 'item code',
            'category_id' => 'menu category',
            'discount_price' => 'special price',
            'status' => 'menu status',
            'publish_date' => 'available from date',
            'publish_time' => 'available from time',
            'variants' => 'portions and options',
            'variants.*.size' => 'portion',
            'variants.*.color' => 'option',
            'variants.*.heel_length' => 'extra',
            'variants.*.quantity' => 'prep count',
            'variants.*.sku' => 'item code',
            'images' => 'food photos',
            'images.*' => 'food photo',
        ];
    }

    public function messages(): array
    {
        return [
            'images.*.required' => 'Each selected file must be a valid image upload.',
            'images.*.extensions' => 'Food photos must be JPG, PNG, GIF, WebP, or HEIC.',
            'images.*.max' => 'Each food photo must not be larger than 20 MB before compression.',
        ];
    }
}
