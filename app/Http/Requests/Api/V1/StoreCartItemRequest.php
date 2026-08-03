<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_size' => ['required', 'string', 'max:120'],
            'variant_color' => ['required', 'string', 'max:120'],
            'variant_heel' => ['nullable', 'string', 'max:120'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'special_request' => ['nullable', 'string', 'max:500'],
        ];
    }
}
