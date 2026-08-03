<?php

namespace App\Http\Requests\Admin;

use App\Enums\ReservationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationRequest extends FormRequest
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
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:50'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'party_size' => ['required', 'integer', 'min:1', 'max:99'],
            'reserved_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
            'status' => ['required', Rule::enum(ReservationStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
