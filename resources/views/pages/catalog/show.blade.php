@extends('layouts.app')

@php
    $seoData = \App\Support\Seo::make($settings)->forProduct($product);
@endphp

@section('title', $seoData->getTitle())
@section('meta_description', $seoData->getDescription())
@section('canonical_url', $seoData->getCanonical())
@section('og_image', $seoData->getOgImage())
@section('og_type', 'product')

@section('content')
<!-- Breadcrumbs -->
<div class="bg-surface-soft border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="$seoData->getBreadcrumbs()" />
    </div>
</div>

<!-- Product Detail Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <div class="bg-white rounded-3xl p-6 sm:p-10 lg:p-12 border border-gray-200 shadow-xs">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start"
             x-data="{
                activeImage: '{{ $product->photo ? Storage::disk('public')->url($product->photo) : '' }}',
                allImages: [
                    @if($product->photo) '{{ Storage::disk('public')->url($product->photo) }}', @endif
                    @foreach($product->images as $img)
                        '{{ Storage::disk('public')->url($img->path) }}',
                    @endforeach
                ]
             }">

            <!-- Kolom Gambar & Galeri -->
            <div class="lg:col-span-5 space-y-4">
                <!-- Preview Gambar Utama -->
                <div class="relative aspect-square rounded-2xl bg-surface-soft border border-gray-200 overflow-hidden flex items-center justify-center shadow-inner">
                    <template x-if="activeImage">
                        <img :src="activeImage"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover transition-opacity duration-300">
                    </template>
                    <template x-if="!activeImage">
                        <div class="flex flex-col items-center justify-center p-8 text-center text-gray-400">
                            <svg class="w-16 h-16 stroke-current mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span class="text-xs font-medium text-primary">SAM Custom Fabrication</span>
                        </div>
                    </template>

                    @if($product->category)
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-white/95 backdrop-blur-xs text-primary shadow-xs border border-gray-200/60">
                            {{ $product->category->name }}
                        </span>
                    @endif
                </div>

                <!-- Thumbnail Carousel/List -->
                @php
                    $allImageUrls = [];
                    if ($product->photo) {
                        $allImageUrls[] = Storage::disk('public')->url($product->photo);
                    }
                    foreach ($product->images as $img) {
                        $allImageUrls[] = Storage::disk('public')->url($img->path);
                    }
                @endphp

                @if(count($allImageUrls) > 1)
                    <div class="flex items-center gap-3 overflow-x-auto pb-2">
                        @foreach($allImageUrls as $url)
                            <button type="button"
                                    @click="activeImage = '{{ $url }}'"
                                    :class="activeImage === '{{ $url }}' ? 'ring-2 ring-primary border-transparent' : 'border-gray-200 hover:border-primary/50'"
                                    class="w-16 h-16 rounded-xl overflow-hidden border bg-surface-soft shrink-0 transition-all focus:outline-hidden">
                                <img src="{{ $url }}" alt="Thumbnail" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Jaminan Kualitas Badge -->
                <div class="bg-surface-soft rounded-xl p-4 border border-surface-soft space-y-2">
                    <div class="flex items-center gap-2.5 text-xs font-bold text-primary">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Standar Fabrikasi Presisi</span>
                    </div>
                    <p class="text-[11px] text-text-muted leading-relaxed">
                        Dirancang dan difabrikasi khusus oleh tenaga Ortotis Prostetis bersertifikat dengan bahan klinis pilihan dan penyesuaian individual.
                    </p>
                </div>
            </div>

            <!-- Kolom Detail & Tindakan -->
            <div class="lg:col-span-7 space-y-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">
                        {{ $product->category?->parent?->name ?? 'Alat Ortotik & Prostetik' }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold font-['Playfair_Display'] text-primary mt-1 tracking-tight">
                        {{ $product->name }}
                    </h1>
                </div>

                <!-- Info Medis Singkat (Fungsi & Indikasi) -->
                <div class="bg-surface-soft rounded-2xl p-5 border border-gray-200 space-y-3.5">
                    @if($product->function)
                        <div class="text-xs sm:text-sm">
                            <span class="font-bold text-primary block sm:inline sm:w-24">Fungsi Utama:</span>
                            <span class="text-text-muted sm:ml-2">{{ $product->function }}</span>
                        </div>
                    @endif

                    @if($product->indication)
                        <div class="text-xs sm:text-sm pt-3 border-t border-gray-200">
                            <span class="font-bold text-primary block sm:inline sm:w-24">Indikasi Klinis:</span>
                            <span class="text-text-muted sm:ml-2">{{ $product->indication }}</span>
                        </div>
                    @endif
                </div>

                <!-- Deskripsi Lengkap -->
                @if($product->description)
                    <div class="space-y-2">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-primary">Deskripsi Alat</h3>
                        <div class="prose prose-sm max-w-none text-text-muted leading-relaxed">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif

                <!-- Notice Harga Kustom -->
                <div class="p-4 rounded-xl border border-dashed border-primary/30 bg-surface-soft text-xs text-text-muted leading-relaxed">
                    <p class="font-semibold text-primary">Informasi Pemesanan & Biaya:</p>
                    <p class="mt-0.5">
                        Biaya pembuatan alat bervariasi bergantung pada ukuran anatomi tubuh, pemilihan material soket, sendi mekanis/hidrolik, serta kebutuhan biomekanik Anda. Silakan konsultasikan dengan klinisi kami untuk estimasi dan rekomendasi terbaik.
                    </p>
                </div>

                <!-- Tombol CTA -->
                @php
                    $waNumber = preg_replace('/[^0-9]/', '', $settings['company.whatsapp'] ?? '6281234567890');
                    $waMessage = 'Halo SAM Orthotic Prosthetic, saya tertarik dan ingin konsultasi mengenai alat: ' . $product->name . ' (' . url()->current() . ')';
                @endphp
                <div class="pt-2 flex flex-col sm:flex-row gap-3.5">
                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($waMessage) }}"
                       target="_blank"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-sm text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                        </svg>
                        <span>Tanya Spesifikasi via WhatsApp</span>
                    </a>

                    <a href="{{ route('appointment') }}"
                       class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-semibold text-sm text-white bg-accent hover:bg-accent-hover transition-colors shadow-xs">
                        <span>Buat Janji Temu</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bagian Testimoni Terkait Produk -->
        @if($product->testimonials && $product->testimonials->isNotEmpty())
            <div class="mt-16 pt-10 border-t border-gray-200">
                <h3 class="font-bold text-xl font-['Playfair_Display'] text-primary mb-6">
                    Pengalaman Pasien dengan {{ $product->name }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($product->testimonials as $testimonial)
                        <div class="bg-surface-soft rounded-2xl p-6 border border-gray-200 flex flex-col justify-between">
                            <p class="text-xs sm:text-sm text-text-muted italic leading-relaxed">
                                "{{ $testimonial->content }}"
                            </p>
                            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-xs text-primary">{{ $testimonial->name }}</div>
                                    @if($testimonial->rating)
                                        <div class="flex text-accent text-xs mt-0.5">
                                            @for($i = 0; $i < $testimonial->rating; $i++) ★ @endfor
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[10px] text-primary bg-white px-2 py-0.5 rounded-md border border-gray-200">
                                    Pasien Terverifikasi
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Bagian Produk Terkait -->
        @if($relatedProducts && $relatedProducts->isNotEmpty())
            <div class="mt-16 pt-10 border-t border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-xl font-['Playfair_Display'] text-primary">
                        Alat Lain dalam Kategori Ini
                    </h3>
                    @if($product->category)
                        <a href="{{ route('catalog.index', ['kategori' => $product->category->slug]) }}"
                           class="text-xs font-semibold text-primary hover:text-accent transition-colors">
                            Lihat Semua &rarr;
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('catalog.show', $related->slug) }}"
                           class="group bg-surface-soft rounded-2xl p-4 border border-gray-200 hover:border-primary/50 hover:bg-white transition-all duration-300 block">
                            <div class="aspect-4/3 rounded-xl overflow-hidden bg-white mb-3 flex items-center justify-center border border-gray-200">
                                @if($related->photo)
                                    <img src="{{ Storage::disk('public')->url($related->photo) }}"
                                         alt="{{ $related->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <span class="text-xs text-primary font-medium">SAM Orthotic</span>
                                @endif
                            </div>
                            <h4 class="font-bold text-sm text-primary group-hover:text-accent transition-colors line-clamp-1">
                                {{ $related->name }}
                            </h4>
                            <p class="text-[11px] text-text-muted mt-1 line-clamp-2">
                                {{ $related->function ?: Str::limit(strip_tags($related->description), 60) }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Schema.org JSON-LD Product & Breadcrumbs via StructuredData Service --}}
@push('schema')
    @php
        $sd = \App\Support\StructuredData::make($settings);
        $schemas = [
            $sd->product($product),
            $sd->breadcrumbs($seoData->getBreadcrumbs()),
        ];
    @endphp
    {!! $sd->render($schemas) !!}
@endpush
@endsection
