<?php

namespace App\Http\Requests\Admin;

use App\Enums\DeliveryAssignmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(DeliveryAssignmentStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
