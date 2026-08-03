<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ModifierGroupRequest extends FormRequest
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
            'min_selections' => ['nullable', 'integer', 'min:0'],
            'max_selections' => ['nullable', 'integer', 'min:1'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'modifiers' => ['nullable', 'array'],
            'modifiers.*.id' => ['nullable', 'integer'],
            'modifiers.*.name' => ['required_with:modifiers', 'string', 'max:255'],
            'modifiers.*.price' => ['nullable', 'numeric', 'min:0'],
            'modifiers.*.is_active' => ['nullable', 'boolean'],
            'modifiers.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_required' => $this->boolean('is_required'),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
