@extends('layouts.app')

@section('title', 'Pertanyaan Umum (FAQ) - SAM Orthotic Prosthetic')

@section('content')
<div class="bg-gradient-to-b from-[#EBF2E8]/40 to-transparent py-12 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
        <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Pusat Informasi</span>
        <h1 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
            Pertanyaan yang Sering Diajukan
        </h1>
        <p class="text-sm sm:text-base text-[#4B5549] mt-3">
            Temukan jawaban cepat seputar pembuatan alat, proses fitting, jadwal konsultasi, dan perawatan.
        </p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <!-- Search Bar -->
    <div class="relative">
        <input type="text"
               placeholder="Cari topik atau kata kunci pertanyaan..."
               class="w-full text-sm rounded-2xl border border-[#E3E3E1] py-3.5 pl-11 pr-4 focus:outline-hidden focus:border-[#869480] bg-white shadow-xs">
        <svg class="w-5 h-5 text-[#869480] absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </div>

    <!-- Accordion Placeholder list -->
    <div class="space-y-4" x-data="{ activeAccordion: 1 }">
        <div class="bg-white rounded-2xl border border-[#E3E3E1] p-6 shadow-xs">
            <button @click="activeAccordion = (activeAccordion === 1 ? null : 1)" class="w-full flex items-center justify-between text-left focus:outline-hidden">
                <span class="font-semibold text-base text-[#242E24]">Bagaimana alur pembuatan kaki atau tangan palsu di SAM?</span>
                <span class="text-[#869480] text-xl font-bold" x-text="activeAccordion === 1 ? '−' : '+'"></span>
            </button>
            <div x-show="activeAccordion === 1" class="mt-4 pt-4 border-t border-[#E3E3E1]/60 text-sm text-[#4B5549] leading-relaxed">
                Alur dimulai dengan konsultasi dan asesmen kondisi fisik pasien, dilanjutkan dengan pengukuran dan pencetakan stump/tungkai, pembuatan cetakan kustom, sesi fitting pertama, penyesuaian akhir (alignment), dan latihan mobilitas.
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-[#E3E3E1] p-6 shadow-xs">
            <button @click="activeAccordion = (activeAccordion === 2 ? null : 2)" class="w-full flex items-center justify-between text-left focus:outline-hidden">
                <span class="font-semibold text-base text-[#242E24]">Apakah wajib memiliki resep atau rujukan dokter?</span>
                <span class="text-[#869480] text-xl font-bold" x-text="activeAccordion === 2 ? '−' : '+'"></span>
            </button>
            <div x-show="activeAccordion === 2" x-cloak class="mt-4 pt-4 border-t border-[#E3E3E1]/60 text-sm text-[#4B5549] leading-relaxed">
                Resep dokter sangat disarankan terutama untuk kasus skoliosis, pasca-operasi, atau kelainan bawaan. Namun, Anda juga dapat berkonsultasi langsung dengan tim ortotis kami untuk asesmen awal.
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-[#E3E3E1] p-6 shadow-xs">
            <button @click="activeAccordion = (activeAccordion === 3 ? null : 3)" class="w-full flex items-center justify-between text-left focus:outline-hidden">
                <span class="font-semibold text-base text-[#242E24]">Berapa lama proses pembuatan alat?</span>
                <span class="text-[#869480] text-xl font-bold" x-text="activeAccordion === 3 ? '−' : '+'"></span>
            </button>
            <div x-show="activeAccordion === 3" x-cloak class="mt-4 pt-4 border-t border-[#E3E3E1]/60 text-sm text-[#4B5549] leading-relaxed">
                Waktu pengerjaan bervariasi antara 5 hingga 14 hari kerja tergantung tingkat kompleksitas alat, komponen yang digunakan, dan jadwal sesi fitting pasien.
            </div>
        </div>
    </div>

    <!-- Help Banner -->
    <div class="bg-[#EBF2E8] border border-[#A5BF98]/40 rounded-2xl p-8 text-center space-y-3">
        <h3 class="font-bold text-lg font-['Playfair_Display'] text-[#242E24]">Belum Menemukan Jawaban?</h3>
        <p class="text-sm text-[#4B5549] max-w-md mx-auto">
            Tim kami siap menjawab pertanyaan spesifik mengenai kebutuhan alat bantu atau kondisi Anda.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="https://wa.me/6281234567890" target="_blank" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#25D366] hover:bg-[#20b858] transition-colors">
                Chat WhatsApp
            </a>
            <a href="{{ route('appointment') }}" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-colors">
                Buat Janji Konsultasi
            </a>
        </div>
    </div>
</div>
@endsection
