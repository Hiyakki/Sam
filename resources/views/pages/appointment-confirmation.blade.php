@extends('layouts.app')

@section('title', 'Konfirmasi Janji Temu - SAM Orthotic Prosthetic')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16"
     x-data="{ copied: false, copyCode() { navigator.clipboard.writeText('{{ $appointment->code }}'); this.copied = true; setTimeout(() => this.copied = false, 2500); } }">

    <!-- Card Sukses -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-gray-200 shadow-xs text-center space-y-8">
        <!-- Icon Success -->
        <div class="w-16 h-16 rounded-full bg-surface-soft text-primary flex items-center justify-center mx-auto ring-8 ring-surface-soft/60">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-primary">Reservasi Berhasil</span>
            <h1 class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary mt-2">
                Janji Temu Anda Telah Terdaftar
            </h1>
            <p class="text-sm text-text-muted max-w-md mx-auto mt-2 leading-relaxed">
                Terima kasih, <strong>{{ $appointment->patient_name }}</strong>. Tim kami telah menerima data reservasi Anda dan siap melayani pada waktu yang telah ditentukan.
            </p>
        </div>



        <!-- Ringkasan Kunjungan -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 text-left max-w-lg mx-auto space-y-3.5 text-xs sm:text-sm">
            <h3 class="font-bold font-['Playfair_Display'] text-base text-primary border-b border-gray-200 pb-2">
                Ringkasan Kunjungan
            </h3>

            <div class="flex justify-between items-start gap-4">
                <span class="text-text-muted font-medium">Kantor Cabang:</span>
                <span class="font-semibold text-primary text-right">{{ $appointment->branch?->name }}</span>
            </div>

            <div class="flex justify-between items-start gap-4">
                <span class="text-text-muted font-medium">Alamat:</span>
                <span class="text-text-muted text-right max-w-xs">{{ $appointment->branch?->address }}</span>
            </div>

            <div class="flex justify-between items-start gap-4">
                <span class="text-text-muted font-medium">Layanan:</span>
                <span class="font-semibold text-primary text-right">{{ $appointment->service?->name }}</span>
            </div>

            <div class="flex justify-between items-start gap-4">
                <span class="text-text-muted font-medium">Tanggal Kunjungan:</span>
                <span class="font-semibold text-primary text-right">
                    {{ $appointment->appointment_date->isoFormat('dddd, D MMMM Y') }}
                </span>
            </div>

            <div class="flex justify-between items-start gap-4">
                <span class="text-text-muted font-medium">Waktu Kedatangan:</span>
                <span class="font-bold text-primary text-right">
                    {{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }} WIB
                </span>
            </div>

            <div class="flex justify-between items-start gap-4 pt-2 border-t border-gray-200">
                <span class="text-text-muted font-medium">Status Reservasi:</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    Menunggu Konfirmasi Admin
                </span>
            </div>
        </div>

        <!-- Tombol Tindakan -->
        @php
            $branchWa = preg_replace('/[^0-9]/', '', $appointment->branch?->phone_wa ?? $settings['company.whatsapp'] ?? '6281234567890');
            $waMessage = "Halo {$appointment->branch?->name}, saya telah membuat janji temu atas nama {$appointment->patient_name} pada tanggal {$appointment->appointment_date->isoFormat('D MMMM Y')}. Mohon konfirmasinya. Terima kasih.";
        @endphp
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3.5">

            <a href="https://wa.me/{{ $branchWa }}?text={{ urlencode($waMessage) }}"
               target="_blank"
               class="w-full sm:w-auto px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs flex items-center justify-center gap-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                </svg>
                <span>Konfirmasi via WhatsApp</span>
            </a>
        </div>
    </div>
</div>
@endsection
