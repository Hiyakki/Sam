@extends('layouts.app')

@php
    $heroTitle = $settings['home.hero_title'] ?? 'Kembalikan Langkah & Mobilitas Anda dengan Presisi';
    $heroSubtitle = $settings['home.hero_subtitle'] ?? 'SAM Orthotic Prosthetic menghadirkan solusi pembuatan alat bantu gerak tubuh berkualitas tinggi sejak 2014.';
    $heroCta = $settings['home.cta_text'] ?? 'Buat Janji Konsultasi';
    $heroImage = $settings['home.hero_image'] ?? null;
    $advantages = $settings['home.advantages'] ?? [];
    $whatsapp = $settings['contact.whatsapp'] ?? '6281234567890';
@endphp

@section('title', ($settings['general.site_name'] ?? 'SAM Orthotic Prosthetic') . ' - Solusi Kaki Palsu & Penyangga Tubuh Terpercaya')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-[#EBF2E8]/60 via-white to-white py-16 sm:py-24 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#EBF2E8] border border-[#A5BF98]/40 text-[#444E41] text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-[#E8343B]"></span>
                    <span>Melayani Pembuatan Alat Ortotik & Prostetik Sejak 2014</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-['Playfair_Display'] text-[#242E24] tracking-tight leading-[1.15]">
                    {!! nl2br(e($heroTitle)) !!}
                </h1>

                <p class="text-base sm:text-lg text-[#4B5549] max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    {{ $heroSubtitle }}
                </p>

                <!-- Action CTAs -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('appointment') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-base font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $heroCta }}
                    </a>

                    <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Halo SAM Orthotic Prosthetic, saya ingin berkonsultasi mengenai alat bantu gerak.') }}"
                       target="_blank"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-base font-semibold text-[#242E24] bg-white border border-[#E3E3E1] hover:bg-[#F7F7F6] hover:border-[#869480] transition-all shadow-xs">
                        <svg class="w-5 h-5 mr-2 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/>
                        </svg>
                        Chat WhatsApp
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="pt-6 grid grid-cols-3 gap-4 border-t border-[#E3E3E1]/70 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <div class="text-2xl font-bold font-['Playfair_Display'] text-[#869480]">10+ Thn</div>
                        <div class="text-xs text-[#4B5549] mt-0.5">Pengalaman Sejak 2014</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold font-['Playfair_Display'] text-[#869480]">100%</div>
                        <div class="text-xs text-[#4B5549] mt-0.5">Custom & Fitting Akurat</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold font-['Playfair_Display'] text-[#869480]">Garansi</div>
                        <div class="text-xs text-[#4B5549] mt-0.5">Perbaikan & Evaluasi</div>
                    </div>
                </div>
            </div>

            <!-- Right Visual Graphic -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    @if ($heroImage)
                        <div class="w-full aspect-4/3 rounded-3xl overflow-hidden shadow-lg border border-[#E3E3E1]">
                            <img src="{{ Storage::url($heroImage) }}" alt="SAM Orthotic Prosthetic" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-full aspect-4/3 rounded-3xl bg-gradient-to-tr from-[#A5BF98]/40 to-[#869480]/20 p-8 border border-[#A5BF98]/30 flex flex-col justify-between shadow-sm relative overflow-hidden">
                            <div class="space-y-3">
                                <span class="inline-block px-3 py-1 rounded-full bg-white/80 text-xs font-semibold text-[#444E41] tracking-wider uppercase">
                                    Workshop Mandiri
                                </span>
                                <h3 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24]">
                                    Presisi Ergonomis untuk Kebutuhan Tiap Pasien
                                </h3>
                                <p class="text-sm text-[#4B5549] leading-relaxed">
                                    Setiap alat dibuat melalui pengukuran detail, uji coba fitting, dan penyesuaian berkelanjutan agar pasien dapat kembali mandiri dan percaya diri.
                                </p>
                            </div>

                            <div class="bg-white/90 backdrop-blur-xs rounded-2xl p-4 border border-[#E3E3E1] shadow-xs flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-[#869480] font-semibold uppercase tracking-wider">Layanan Konsultasi</div>
                                    <div class="text-sm font-bold text-[#242E24]">Reservasi Jadwal Fleksibel</div>
                                </div>
                                <a href="{{ route('appointment') }}" class="text-xs font-bold text-[#E8343B] hover:underline flex items-center gap-1">
                                    <span>Pilih Slot</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Kategori Utama -->
<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Katalog Alat Bantu</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Kategori Produk Utama
            </h2>
            <p class="text-sm sm:text-base text-[#4B5549] mt-3">
                Kami merancang dan memproduksi tiga kelompok utama alat bantu ortopedi dan rehabilitasi fisik dengan standar mutu terbaik.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse ($categories as $index => $cat)
                <div class="group bg-[#F7F7F6] hover:bg-[#EBF2E8]/40 border border-[#E3E3E1] hover:border-[#A5BF98] rounded-2xl p-8 transition-all duration-300 hover:shadow-md flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl {{ $index === 0 ? 'bg-[#A5BF98]' : ($index === 1 ? 'bg-[#869480]' : 'bg-[#242E24]') }} text-white flex items-center justify-center font-bold text-xl mb-6 shadow-xs">
                            0{{ $index + 1 }}
                        </div>
                        <h3 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24] mb-3">
                            {{ $cat->name }}
                        </h3>
                        <p class="text-sm text-[#4B5549] leading-relaxed mb-6">
                            @if ($cat->name === 'Orthosis')
                                Alat bantu penyangga tubuh untuk fiksasi, koreksi, dan mobilisasi: Spinal Orthosis, Upper Limb, dan Lower Limb (AFO, KAFO, Insole).
                            @elseif ($cat->name === 'Prosthesis')
                                Pengganti anggota gerak tubuh: Kaki palsu bawah/atas lutut (Transtibial/Transfemoral), tangan tiruan, dan jari palsu.
                            @elseif ($cat->name === 'Komponen')
                                Komponen suku cadang berkualitas: Socket, Knee Joint (Mechanical/Hydraulic/C-Leg), Foot Component, Adaptor Socket, Tube, dan Klem.
                            @else
                                Koleksi alat bantu gerak berkualitas kustom sesuai kebutuhan klinis Anda.
                            @endif
                        </p>
                    </div>
                    <div class="pt-4 border-t border-[#E3E3E1] flex items-center justify-between">
                        <span class="text-xs text-[#869480] font-medium">{{ $cat->products_count }} Produk</span>
                        <a href="{{ route('catalog.index', ['kategori' => $cat->slug]) }}" class="inline-flex items-center text-sm font-semibold text-[#869480] group-hover:text-[#444E41] transition-colors">
                            Lihat Produk &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <!-- Fallback Static Cards -->
                <div class="bg-[#F7F7F6] rounded-2xl p-8 border border-[#E3E3E1]">
                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">Orthosis</h3>
                    <p class="text-sm text-[#4B5549] mt-2">Penyangga tubuh, brace skoliosis, dan koreksi ekstremitas.</p>
                    <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-[#869480] mt-4 inline-block">Lihat Katalog &rarr;</a>
                </div>
                <div class="bg-[#F7F7F6] rounded-2xl p-8 border border-[#E3E3E1]">
                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">Prosthesis</h3>
                    <p class="text-sm text-[#4B5549] mt-2">Kaki palsu dan tangan palsu kustom.</p>
                    <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-[#869480] mt-4 inline-block">Lihat Katalog &rarr;</a>
                </div>
                <div class="bg-[#F7F7F6] rounded-2xl p-8 border border-[#E3E3E1]">
                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">Komponen</h3>
                    <p class="text-sm text-[#4B5549] mt-2">Suku cadang dan komponen kaki/tangan palsu.</p>
                    <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-[#869480] mt-4 inline-block">Lihat Katalog &rarr;</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Keunggulan SAM -->
@if (!empty($advantages) && is_array($advantages))
<section class="py-16 sm:py-20 bg-[#F7F7F6] border-y border-[#E3E3E1]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Nilai & Komitmen</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Mengapa Memilih SAM Orthotic Prosthetic?
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($advantages as $adv)
                <div class="bg-white rounded-2xl p-6 border border-[#E3E3E1] shadow-xs hover:border-[#A5BF98] transition-all">
                    <div class="w-12 h-12 rounded-xl bg-[#EBF2E8] text-[#869480] flex items-center justify-center font-bold text-xl mb-4">
                        @if (($adv['icon'] ?? '') === 'users')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        @elseif (($adv['icon'] ?? '') === 'heart')
                            <svg class="w-6 h-6 text-[#E8343B]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        @elseif (($adv['icon'] ?? '') === 'shield')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        @else
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        @endif
                    </div>
                    <h3 class="font-bold text-lg text-[#242E24] font-['Playfair_Display'] mb-2">
                        {{ $adv['title'] ?? '' }}
                    </h3>
                    <p class="text-sm text-[#4B5549] leading-relaxed">
                        {{ $adv['desc'] ?? '' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Produk Unggulan -->
@if ($featuredProducts->isNotEmpty())
<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Produk Pilihan</span>
                <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                    Koleksi Unggulan SAM
                </h2>
            </div>
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center text-sm font-semibold text-[#869480] hover:text-[#444E41]">
                <span>Lihat Seluruh Katalog</span>
                <span class="ml-1">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($featuredProducts as $item)
                <div class="bg-[#F7F7F6] rounded-2xl overflow-hidden border border-[#E3E3E1] hover:border-[#A5BF98] transition-all hover:shadow-md flex flex-col justify-between group">
                    <div class="aspect-4/3 bg-[#E3E3E1]/50 overflow-hidden relative">
                        @if ($item->photo)
                            <img src="{{ Storage::disk('public')->url($item->photo) }}"
                                 alt="{{ $item->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#869480]/60">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        @if ($item->category)
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 backdrop-blur-xs text-[11px] font-semibold text-[#444E41] shadow-2xs">
                                {{ $item->category->name }}
                            </span>
                        @endif
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24] mb-2 group-hover:text-[#869480] transition-colors">
                                <a href="{{ route('catalog.show', $item->slug) }}">
                                    {{ $item->name }}
                                </a>
                            </h3>
                            @if ($item->function)
                                <p class="text-xs text-[#869480] font-semibold mb-2">Fungsi: {{ $item->function }}</p>
                            @endif
                            @if ($item->indication)
                                <p class="text-xs text-[#4B5549] line-clamp-2">{{ $item->indication }}</p>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-[#E3E3E1] flex items-center justify-between">
                            <a href="{{ route('catalog.show', $item->slug) }}" class="text-xs font-semibold text-[#869480] hover:text-[#242E24]">
                                Detail Produk &rarr;
                            </a>
                            <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Halo SAM, saya ingin bertanya tentang produk ' . $item->name) }}"
                               target="_blank"
                               class="text-xs font-semibold text-[#25D366] hover:underline flex items-center gap-1">
                                <span>Tanya WA</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Testimoni Pasien (Hanya tampil bila ada testimoni aktif yang terkonfirmasi izin) -->
@if ($testimonials->isNotEmpty())
<section class="py-16 sm:py-20 bg-[#EBF2E8]/40 border-y border-[#A5BF98]/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Kisah Pemulihan</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Apa Kata Pasien Kami?
            </h2>
            <p class="text-sm sm:text-base text-[#4B5549] mt-3">
                Kenyamanan dan kepuasan pasien dalam bergerak mandiri adalah prioritas utama kami.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($testimonials as $t)
                <div class="bg-white rounded-2xl p-8 border border-[#E3E3E1] shadow-xs flex flex-col justify-between">
                    <div>
                        @if ($t->rating)
                            <div class="flex items-center gap-1 text-[#E8343B] mb-4">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $t->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        @endif

                        <blockquote class="text-sm text-[#4B5549] leading-relaxed italic mb-6">
                            "{{ $t->content }}"
                        </blockquote>
                    </div>

                    <div class="pt-4 border-t border-[#E3E3E1] flex items-center gap-3">
                        @if ($t->photo)
                            <img src="{{ Storage::disk('public')->url($t->photo) }}"
                                 alt="{{ $t->name }}"
                                 class="w-10 h-10 rounded-full object-cover border border-[#A5BF98]">
                        @else
                            <div class="w-10 h-10 rounded-full bg-[#EBF2E8] text-[#869480] flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($t->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-bold text-sm text-[#242E24]">{{ $t->name }}</div>
                            @if ($t->role_or_info)
                                <div class="text-xs text-[#869480]">{{ $t->role_or_info }}</div>
                            @endif
                            @if ($t->product)
                                <a href="{{ route('catalog.show', $t->product->slug) }}" class="text-[11px] text-[#4B5549] hover:underline block mt-0.5">
                                    Alat: {{ $t->product->name }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Cuplikan FAQ di Beranda (Hanya tampil bila ada FAQ yang ditandai show_on_home) -->
@if ($homeFaqs->isNotEmpty())
<section class="py-16 sm:py-20 bg-white" x-data="{ activeFaq: null }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Tanya Jawab</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Pertanyaan yang Sering Diajukan
            </h2>
        </div>

        <div class="space-y-4">
            @foreach ($homeFaqs as $index => $faq)
                <div class="bg-[#F7F7F6] rounded-2xl border border-[#E3E3E1] p-6 transition-all">
                    <button @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})"
                            type="button"
                            class="w-full flex items-center justify-between text-left focus:outline-hidden">
                        <span class="font-semibold text-base text-[#242E24] pr-4">{{ $faq->question }}</span>
                        <span class="text-[#869480] text-xl font-bold shrink-0" x-text="activeFaq === {{ $faq->id }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === {{ $faq->id }}"
                         x-cloak
                         x-collapse
                         class="mt-4 pt-4 border-t border-[#E3E3E1] text-sm text-[#4B5549] leading-relaxed prose prose-sm max-w-none">
                        {!! $faq->answer !!}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('faq') }}" class="inline-flex items-center text-sm font-semibold text-[#869480] hover:text-[#444E41]">
                <span>Lihat Semua Pertanyaan & Jawaban</span>
                <span class="ml-1">&rarr;</span>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Call to Action Banner -->
<section class="py-16 bg-[#242E24] text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] max-w-2xl mx-auto">
            Butuh Konsultasi Mengenai Alat Bantu yang Tepat?
        </h2>
        <p class="text-sm sm:text-base text-[#E3E3E1]/80 max-w-xl mx-auto mt-4">
            Tim kami siap membantu Anda memilih solusi alat ortotik atau prostetik yang paling tepat melalui pemeriksaan langsung.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('appointment') }}" class="px-8 py-3.5 rounded-xl font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-all shadow-md">
                Jadwalkan Janji Temu Sekarang
            </a>
            <a href="{{ route('faq') }}" class="px-8 py-3.5 rounded-xl font-semibold text-white/90 bg-white/10 hover:bg-white/20 transition-all border border-white/20">
                Baca Pertanyaan Umum (FAQ)
            </a>
        </div>
    </div>
</section>

<!-- Schema.org JSON-LD Structured Data -->
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'MedicalBusiness',
    'name' => $settings['company.name'] ?? 'SAM Orthotic Prosthetic',
    'url' => url('/'),
    'description' => 'Layanan klinis dan fabrikasi alat bantu gerak tubuh ortotik prostetik kustom presisi tinggi sejak 2014.',
    'telephone' => $settings['company.whatsapp'] ?? '+6281234567890',
    'email' => $settings['company.email'] ?? 'info@sam-orthotic.com',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $settings['company.address'] ?? 'Indonesia',
        'addressCountry' => 'ID',
    ],
    'medicalSpecialty' => [
        'Orthotics',
        'Prosthetics',
    ],
    'availableService' => [
        [
            '@type' => 'MedicalProcedure',
            'name' => 'Pembuatan Kaki Palsu & Tangan Palsu Kustom',
        ],
        [
            '@type' => 'MedicalProcedure',
            'name' => 'Pembuatan Brace & Penyangga Tubuh (Orthosis)',
        ],
        [
            '@type' => 'MedicalProcedure',
            'name' => 'Pemeriksaan Biomekanik & Sesi Fitting Alat',
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection
