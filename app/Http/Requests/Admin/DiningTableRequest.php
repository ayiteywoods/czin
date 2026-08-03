<?php

namespace App\Http\Requests\Admin;

use App\Enums\TableSize;
use App\Enums\TableStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiningTableRequest extends FormRequest
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
        $tableId = $this->route('table')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('dining_tables', 'code')->ignore($tableId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:99'],
            'size' => ['required', Rule::enum(TableSize::class)],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(TableStatus::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
