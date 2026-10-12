<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Rules\TurnstileRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name' => ['required', 'string', 'max:120'],
            'patient_email' => ['required', 'string', 'email:rfc', 'max:150'],
            'patient_phone' => ['required', 'string', 'max:20'],
            'patient_age' => ['required', 'integer', 'between:0,120'],
            'complaint' => ['required', 'string', 'max:1000'],
            'branch_id' => ['required', 'integer', Rule::exists('branches', 'id')->where('is_active', true)],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            'appointment_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'string'],
            'prescription' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120'],
            'privacy_consent' => ['required', 'accepted'],
            'website_hp' => ['nullable', 'string', 'max:0'], // Honeypot field (wajib kosong)
            'cf-turnstile-response' => ['nullable', new TurnstileRule],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_name.required' => 'Nama lengkap wajib diisi.',
            'patient_name.max' => 'Nama lengkap maksimal 120 karakter.',
            'patient_email.required' => 'Alamat email wajib diisi.',
            'patient_email.email' => 'Format alamat email tidak valid.',
            'patient_phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'patient_age.required' => 'Usia pasien wajib diisi.',
            'patient_age.between' => 'Usia pasien harus di antara 0 hingga 120 tahun.',
            'complaint.required' => 'Keluhan atau kebutuhan alat wajib diisi.',
            'complaint.max' => 'Keluhan maksimal 1000 karakter.',
            'branch_id.required' => 'Silakan pilih kantor cabang pemeriksaan.',
            'branch_id.exists' => 'Kantor cabang yang dipilih tidak aktif atau tidak ditemukan.',
            'service_id.required' => 'Silakan pilih jenis layanan ortotik / prostetik.',
            'service_id.exists' => 'Layanan yang dipilih tidak tersedia.',
            'appointment_date.required' => 'Silakan tentukan tanggal kedatangan.',
            'appointment_date.date_format' => 'Format tanggal tidak sesuai (YYYY-MM-DD).',
            'start_time.required' => 'Silakan pilih slot waktu kedatangan.',
            'prescription.mimes' => 'File resep dokter harus berformat PDF, JPG, JPEG, atau PNG.',
            'prescription.max' => 'Ukuran file resep dokter maksimal 5 MB.',
            'privacy_consent.accepted' => 'Anda harus menyetujui kebijakan privasi sebelum melanjutkan reservasi.',
            'website_hp.max' => 'Terjadi kesalahan validasi anti-spam.',
        ];
    }

    /**
     * Memvalidasi batas janji aktif per nomor telepon.
     */
    protected function passedValidation(): void
    {
        // Cek honeypot spam
        if (filled($this->input('website_hp'))) {
            throw ValidationException::withMessages([
                'patient_name' => 'Permintaan Anda terdeteksi sebagai spam.',
            ]);
        }

        $rawPhone = (string) $this->input('patient_phone');
        $cleaned = preg_replace('/[^0-9]/', '', $rawPhone) ?? '';
        $normalizedPhone = str_starts_with($cleaned, '0')
            ? '62'.substr($cleaned, 1)
            : (str_starts_with($cleaned, '62') ? $cleaned : '62'.$cleaned);

        $activeCount = Appointment::where('patient_phone', $normalizedPhone)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeCount >= 2) {
            throw ValidationException::withMessages([
                'patient_phone' => 'Nomor WhatsApp Anda sudah memiliki 2 janji temu aktif. Silakan selesaikan atau batalkan janji sebelumnya terlebih dahulu.',
            ]);
        }
    }
}
