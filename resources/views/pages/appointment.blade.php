@extends('layouts.app')

@section('title', 'Reservasi Janji Temu - SAM Orthotic Prosthetic')

@section('content')
<div class="bg-gradient-to-b from-[#EBF2E8]/40 to-transparent py-12 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
        <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Reservasi Online</span>
        <h1 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
            Buat Janji Temu & Konsultasi
        </h1>
        <p class="text-sm sm:text-base text-[#4B5549] mt-3">
            Pilih cabang, layanan, tanggal kedatangan, dan slot waktu yang tersedia. Tim kami akan menyiapkan sesi pemeriksaan dan fitting terbaik untuk Anda.
        </p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-[#E3E3E1] shadow-xs space-y-8">
        <div class="border-b border-[#E3E3E1] pb-4">
            <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                Formulir Pendaftaran Pasien
            </h2>
            <p class="text-xs text-[#4B5549] mt-1">
                Seluruh data pribadi Anda dijaga kerahasiaannya sesuai UU Perlindungan Data Pribadi (UU PDP).
            </p>
        </div>

        <div class="bg-[#F7F7F6] border border-[#A5BF98]/40 rounded-2xl p-6 text-center space-y-3">
            <div class="w-10 h-10 rounded-full bg-[#EBF2E8] text-[#869480] flex items-center justify-center mx-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="font-bold text-base text-[#242E24]">Alur Form Pemesanan Interaktif</h3>
            <p class="text-xs sm:text-sm text-[#4B5549] max-w-md mx-auto leading-relaxed">
                Logika slot ketersediaan (kantor &rarr; layanan &rarr; tanggal &rarr; jam), anti-double booking, dan upload resep privat akan diaktifkan secara menyeluruh pada <strong>Fase 5 (SlotService)</strong> dan <strong>Fase 6 (Form Frontend)</strong>.
            </p>
            <div class="pt-2 text-xs text-[#869480] font-medium">
                Operasional Normal: Senin - Sabtu (08:00 - 17:00 WIB)
            </div>
        </div>

        <div class="text-center pt-2">
            <a href="https://wa.me/6281234567890?text=Halo%20SAM,%20saya%20ingin%20membuat%20janji%20temu%20konsultasi"
               target="_blank"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs">
                Hubungi via WhatsApp Sementara
            </a>
        </div>
    </div>
</div>
@endsection
