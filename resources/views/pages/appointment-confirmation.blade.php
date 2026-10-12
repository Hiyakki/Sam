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

        <!-- Kode Janji -->
        <div class="inline-block bg-surface-soft border border-gray-200 rounded-2xl px-6 py-4 mx-auto">
            <span class="text-xs uppercase font-semibold text-text-muted tracking-wider block">Kode Janji Temu</span>
            <div class="flex items-center justify-center gap-3 mt-1">
                <span class="text-2xl sm:text-3xl font-mono font-bold text-primary tracking-wide">{{ $appointment->code }}</span>
                <button type="button" @click="copyCode" class="text-xs text-text-muted hover:text-primary transition-colors flex items-center gap-1 border border-gray-200 rounded-lg px-2.5 py-1 bg-white">
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" class="text-emerald-600 font-semibold">Tersalin!</span>
                </button>
            </div>
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

        <!-- Kontak Admin Cabang -->
        @php
            $branchPhone = $appointment->branch?->phone_wa ?? $settings['contact.whatsapp'] ?? $settings['company.whatsapp'] ?? '081234567890';
            $branchWa = preg_replace('/[^0-9]/', '', $branchPhone);
            if (str_starts_with($branchWa, '0')) {
                $branchWa = '62' . substr($branchWa, 1);
            }
        @endphp
        <div class="pt-4 border-t border-gray-200 text-center space-y-1.5">
            <p class="text-sm text-text-muted">
                Untuk perubahan atau pembatalan, silakan hubungi admin kami:
            </p>
            <p class="text-sm font-semibold text-primary">
                WhatsApp Cabang:
                <a href="https://wa.me/{{ $branchWa }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="text-emerald-700 hover:text-emerald-800 underline">
                    {{ $branchPhone }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
