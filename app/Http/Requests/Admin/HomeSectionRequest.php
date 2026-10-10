<?php

namespace App\Http\Requests\Admin;

use App\Support\ImageUpload;
use Illuminate\Foundation\Http\FormRequest;

class HomeSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'title_highlight' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'primary_label' => ['nullable', 'string', 'max:255'],
            'primary_url' => ['nullable', 'string', 'max:255'],
            'secondary_label' => ['nullable', 'string', 'max:255'],
            'secondary_url' => ['nullable', 'string', 'max:255'],
            'image' => ImageUpload::rules(4096),
            'carousel' => ['nullable', 'array'],
            'carousel.*' => ImageUpload::rules(4096),
            'replace_carousel' => ['nullable', 'array'],
            'replace_carousel.*' => ImageUpload::rules(4096),
            'remove_carousel' => ['nullable', 'array'],
            'remove_carousel.*' => ['integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
