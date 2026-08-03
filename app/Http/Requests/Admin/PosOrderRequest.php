<?php

namespace App\Http\Requests\Admin;

use App\Enums\FulfillmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PosOrderRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'fulfillment_type' => ['required', Rule::enum(FulfillmentType::class)],
            'dining_table_id' => [
                Rule::requiredIf(fn () => $this->input('fulfillment_type') === FulfillmentType::DineIn->value),
                'nullable',
                'integer',
                'exists:dining_tables,id',
            ],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'customer_comment' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'momo'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one item to the order.',
            'items.min' => 'Add at least one item to the order.',
            'dining_table_id.required' => 'Select a table for dine-in orders.',
        ];
    }
}
