@extends('layouts.app')

@section('title', 'Detail Produk - SAM Orthotic Prosthetic')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex text-xs text-[#869480] mb-6">
        <a href="{{ route('home') }}" class="hover:underline">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('catalog.index') }}" class="hover:underline">Katalog</a>
        <span class="mx-2">/</span>
        <span class="text-[#242E24] font-medium">{{ $slug }}</span>
    </nav>

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-[#E3E3E1] shadow-xs">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-5 bg-[#F7F7F6] rounded-2xl aspect-square flex items-center justify-center border border-[#E3E3E1]">
                <span class="text-sm text-[#869480] font-medium">Foto Produk: {{ $slug }}</span>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#869480]">Alat Bantu Gerak</span>
                    <h1 class="text-3xl font-bold font-['Playfair_Display'] text-[#242E24] mt-1 capitalize">
                        {{ str_replace('-', ' ', $slug) }}
                    </h1>
                </div>

                <div class="space-y-3 border-y border-[#E3E3E1] py-4 text-sm">
                    <div>
                        <span class="font-semibold text-[#242E24]">Fungsi:</span>
                        <span class="text-[#4B5549] ml-2">Fiksasi dan Koreksi Gerak</span>
                    </div>
                    <div>
                        <span class="font-semibold text-[#242E24]">Indikasi:</span>
                        <span class="text-[#4B5549] ml-2">Disesuaikan dengan hasil pemeriksaan klinis</span>
                    </div>
                </div>

                <p class="text-sm text-[#4B5549] leading-relaxed">
                    Setiap produk dibuat kustom sesuai dengan ukuran anatomi pasien. Hubungi kami untuk konsultasi pemilihan jenis alat dan jadwal pengukuran.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <a href="https://wa.me/6281234567890?text=Halo%20SAM,%20saya%20mau%20tanya%20tentang%20{{ urlencode($slug) }}"
                       target="_blank"
                       class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs">
                        Tanya via WhatsApp
                    </a>
                    <a href="{{ route('appointment') }}"
                       class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-colors shadow-xs">
                        Buat Janji Konsultasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
