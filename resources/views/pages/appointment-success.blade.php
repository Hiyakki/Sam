@extends('layouts.app')

@section('title', 'Pendaftaran Janji Temu Berhasil | SAM Orthotic Prosthetic')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-gray-200 shadow-xs text-center space-y-8">
        <!-- Icon Success -->
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto ring-8 ring-emerald-50/60">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        @if($appointment)
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-primary">Pendaftaran Diterima</span>
                <h1 class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary mt-2">
                    Janji Temu Anda Telah Terdaftar
                </h1>
                <p class="text-sm text-text-muted max-w-md mx-auto mt-2 leading-relaxed">
                    Terima kasih, <strong>{{ $appointment->patient_name }}</strong>. Tim SAM Orthotic Prosthetic telah menerima pendaftaran janji temu Anda dan akan segera menghubungi Anda untuk konfirmasi jadwal.
                </p>
            </div>

            <!-- Ringkasan Kunjungan Minimal (Tanpa Kode, Tanpa Telepon Penuh, Tanpa Keluhan, Tanpa Resep) -->
            <div class="bg-surface-soft/40 rounded-2xl border border-gray-200 p-6 text-left max-w-lg mx-auto space-y-3.5 text-xs sm:text-sm">
                <h2 class="font-bold font-['Playfair_Display'] text-base text-primary border-b border-gray-200 pb-2">
                    Ringkasan Kunjungan
                </h2>

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
                        {{ $appointment->appointment_date?->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>

                <div class="flex justify-between items-start gap-4">
                    <span class="text-text-muted font-medium">Waktu Kedatangan:</span>
                    <span class="font-bold text-primary text-right">
                        {{ substr((string) $appointment->start_time, 0, 5) }} - {{ substr((string) $appointment->end_time, 0, 5) }} WIB
                    </span>
                </div>

                <div class="flex justify-between items-start gap-4 pt-2 border-t border-gray-200">
                    <span class="text-text-muted font-medium">Status:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        Menunggu Konfirmasi Admin
                    </span>
                </div>
            </div>

            <!-- Kontak Perubahan / Pembatalan -->
            @php
                $branchPhone = $appointment->branch?->phone_wa ?? $settings['contact.whatsapp'] ?? $settings['company.whatsapp'] ?? '081234567890';
                $cleanBranchWa = preg_replace('/[^0-9]/', '', $branchPhone);
                if (str_starts_with($cleanBranchWa, '0')) {
                    $cleanBranchWa = '62' . substr($cleanBranchWa, 1);
                }
            @endphp
            <div class="pt-4 border-t border-gray-200 text-center space-y-1.5">
                <p class="text-sm text-text-muted">
                    Untuk perubahan jadwal atau pembatalan, silakan hubungi admin kami:
                </p>
                <p class="text-sm font-semibold text-primary">
                    WhatsApp Cabang:
                    <a href="https://wa.me/{{ $cleanBranchWa }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="text-emerald-700 hover:text-emerald-800 underline">
                        {{ $branchPhone }}
                    </a>
                </p>
            </div>
        @else
            <!-- Tampilan Generik (Bila session tidak ada atau kedaluwarsa) -->
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-primary">Pendaftaran Selesai</span>
                <h1 class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary mt-2">
                    Terima Kasih atas Kepercayaan Anda
                </h1>
                <p class="text-sm text-text-muted max-w-md mx-auto mt-2 leading-relaxed">
                    Pendaftaran janji temu konsultasi Anda di SAM Orthotic Prosthetic telah diproses. Tim kami akan menghubungi Anda melalui WhatsApp atau email untuk konfirmasi jadwal kunjungan.
                </p>
            </div>

            @php
                $defaultPhone = $settings['contact.whatsapp'] ?? $settings['company.whatsapp'] ?? '081234567890';
                $cleanDefaultWa = preg_replace('/[^0-9]/', '', $defaultPhone);
                if (str_starts_with($cleanDefaultWa, '0')) {
                    $cleanDefaultWa = '62' . substr($cleanDefaultWa, 1);
                }
            @endphp
            <div class="bg-surface-soft/40 rounded-2xl border border-gray-200 p-6 text-center max-w-lg mx-auto space-y-2">
                <p class="text-xs sm:text-sm text-text-muted">
                    Butuh bantuan atau ingin menanyakan jadwal Anda?
                </p>
                <p class="text-sm font-semibold text-primary">
                    Customer Care WhatsApp:
                    <a href="https://wa.me/{{ $cleanDefaultWa }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="text-emerald-700 hover:text-emerald-800 underline">
                        {{ $defaultPhone }}
                    </a>
                </p>
            </div>
        @endif

        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url('/') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-xs text-white bg-primary hover:bg-primary-light transition-colors shadow-xs">
                Kembali ke Beranda
            </a>
            <a href="{{ url('/katalog') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-xs text-primary bg-surface-soft hover:bg-gray-200 transition-colors border border-gray-200">
                Lihat Katalog Produk
            </a>
        </div>
    </div>
</div>
@endsection
