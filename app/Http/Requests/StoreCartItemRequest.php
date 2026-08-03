<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCustom = strcasecmp((string) $this->input('variant_color'), 'Custom') === 0;

        return [
            'product_id' => ['required', 'exists:products,id'],
            'variant_size' => ['required', 'string', 'max:50'],
            'variant_color' => ['required', 'string', 'max:50'],
            'variant_heel' => ['nullable', 'string', 'max:50'],
            'special_request' => [$isCustom ? 'required' : 'nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'variant_size.required' => 'Please select a portion and option.',
            'variant_color.required' => 'Please select a portion and option.',
            'special_request.required' => 'Please type your custom prep or spice request.',
        ];
    }
}
