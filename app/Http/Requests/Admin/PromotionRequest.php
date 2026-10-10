<?php

namespace App\Http\Requests\Admin;

use App\Enums\PromotionType;
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
            'category_id' => ['nullable', 'exists:categories,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'is_active' => ['nullable', 'boolean'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'min:0', 'max:6'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'category_id' => $this->filled('category_id') ? $this->input('category_id') : null,
            'product_id' => $this->filled('product_id') ? $this->input('product_id') : null,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('category_id') && ! $this->filled('product_id')) {
                $validator->errors()->add(
                    'category_id',
                    'Select a category or a product. Leave both blank is not allowed — promotions must target something.',
                );
            }
        });
    }
}
