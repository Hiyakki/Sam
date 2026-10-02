@extends('layouts.app')

@section('title', 'Katalog Produk - SAM Orthotic Prosthetic')

@section('content')
<div class="bg-gradient-to-b from-[#EBF2E8]/40 to-transparent py-12 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Katalog Lengkap</span>
            <h1 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Katalog Alat Ortotik & Prostetik
            </h1>
            <p class="text-sm sm:text-base text-[#4B5549] mt-3">
                Jelajahi koleksi alat bantu gerak tubuh kustom kami. Setiap produk dirancang khusus untuk kenyamanan dan fungsi optimal Anda.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filter Placeholder -->
        <aside class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-[#E3E3E1] shadow-xs space-y-4">
                <h3 class="font-bold font-['Playfair_Display'] text-lg text-[#242E24]">Kategori Produk</h3>
                <ul class="space-y-2 text-sm text-[#4B5549]">
                    <li class="font-semibold text-[#869480]">
                        <a href="{{ route('catalog.index') }}">Semua Kategori</a>
                    </li>
                    <li class="pl-2 border-l-2 border-[#A5BF98] space-y-1">
                        <div class="font-medium text-[#242E24]">Orthosis</div>
                        <ul class="pl-3 text-xs space-y-1 text-[#4B5549]">
                            <li>Upper Limb Orthosis</li>
                            <li>Lower Limb Orthosis</li>
                            <li>Spinal Orthosis</li>
                        </ul>
                    </li>
                    <li class="pl-2 border-l-2 border-[#E3E3E1] space-y-1">
                        <div class="font-medium text-[#242E24]">Prosthesis</div>
                        <ul class="pl-3 text-xs space-y-1 text-[#4B5549]">
                            <li>Upper Limb Prosthesis</li>
                            <li>Lower Limb Prosthesis</li>
                        </ul>
                    </li>
                    <li class="pl-2 border-l-2 border-[#E3E3E1] space-y-1">
                        <div class="font-medium text-[#242E24]">Komponen</div>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Product Grid Placeholder -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl p-4 border border-[#E3E3E1] flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <input type="text"
                           placeholder="Cari nama produk atau indikasi..."
                           class="w-full text-sm rounded-xl border border-[#E3E3E1] py-2 pl-9 pr-4 focus:outline-hidden focus:border-[#869480] bg-[#F7F7F6]">
                    <svg class="w-4 h-4 text-[#869480] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div class="text-xs text-[#4B5549]">
                    Katalog lengkap akan dimuat dari database pada Fase 2 & Fase 4
                </div>
            </div>

            <div class="bg-[#F7F7F6] border border-dashed border-[#A5BF98] rounded-2xl p-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-[#EBF2E8] text-[#869480] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h4 class="font-bold text-lg text-[#242E24] font-['Playfair_Display']">Data Katalog Siap Dihubungkan</h4>
                <p class="text-sm text-[#4B5549] max-w-md mx-auto mt-1">
                    Lebih dari 50 produk dari Lampiran A akan diimpor melalui database seeder pada Fase 2 dan ditampilkan lengkap pada Fase 4.
                </p>
                <div class="mt-4">
                    <a href="{{ route('appointment') }}" class="inline-flex items-center text-xs font-semibold text-[#E8343B] hover:underline">
                        <span>Konsultasi Produk Tertentu</span>
                        <span class="ml-1">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
