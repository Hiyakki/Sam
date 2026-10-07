<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_date.required' => 'Tanggal kunjungan baru wajib dipilih.',
            'appointment_date.date_format' => 'Format tanggal tidak sesuai (YYYY-MM-DD).',
            'start_time.required' => 'Slot jam kunjungan baru wajib dipilih.',
        ];
    }
}
