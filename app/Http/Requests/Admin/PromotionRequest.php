<?php

namespace App\Http\Requests\Admin;

use App\Enums\PromotionType;
use App\Models\Promotion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(PromotionType::class)],
            'value' => ['required', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'is_active' => ['nullable', 'boolean'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'min:0', 'max:6'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $categoryIds = $this->input('category_ids', []);
        $productIds = $this->input('product_ids', []);

        if (! is_array($categoryIds)) {
            $categoryIds = $categoryIds !== null && $categoryIds !== '' ? [$categoryIds] : [];
        }

        if (! is_array($productIds)) {
            $productIds = $productIds !== null && $productIds !== '' ? [$productIds] : [];
        }

        $targets = Promotion::normalizeTargets($categoryIds, $productIds);

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'category_ids' => $targets['category_ids'],
            'product_ids' => $targets['product_ids'],
            'category_id' => $targets['category_id'],
            'product_id' => $targets['product_id'],
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasCategories = is_array($this->input('category_ids')) && $this->input('category_ids') !== [];
            $hasProducts = is_array($this->input('product_ids')) && $this->input('product_ids') !== [];

            if (! $hasCategories && ! $hasProducts) {
                $validator->errors()->add(
                    'category_ids',
                    'Select at least one category or one product for this promotion.',
                );
            }
        });
    }
}
