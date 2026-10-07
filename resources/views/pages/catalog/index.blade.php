@extends('layouts.app')

@section('title', ($currentCategory ? $currentCategory->name . ' - ' : '') . 'Katalog Alat Ortotik & Prostetik - SAM')
@section('meta_description', 'Jelajahi katalog alat ortotik dan prostetik kustom SAM Orthotic Prosthetic. Dibuat presisi sesuai anatomi dan kebutuhan mobilitas Anda.')

@section('content')
<!-- Header Banner -->
<div class="bg-gradient-to-b from-[#EBF2E8]/60 via-[#F7F7F6] to-[#F7F7F6] py-10 sm:py-14 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480] bg-white/80 backdrop-blur-xs px-3 py-1 rounded-full border border-[#869480]/20 inline-block">
                Katalog Produk Medis
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] text-[#242E24] mt-3 tracking-tight">
                @if($currentCategory)
                    {{ $currentCategory->name }}
                @else
                    Katalog Alat Ortotik & Prostetik
                @endif
            </h1>
            <p class="text-sm sm:text-base text-[#4B5549] mt-3 leading-relaxed">
                @if($currentCategory && $currentCategory->description)
                    {{ $currentCategory->description }}
                @else
                    Setiap alat bantu gerak dirancang dan difabrikasi secara kustom sesuai asesmen klinis serta ukuran anatomi tubuh pasien untuk kenyamanan dan mobilitas maksimal.
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filter Kategori -->
        <aside class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-[#E3E3E1] shadow-xs sticky top-24">
                <div class="flex items-center justify-between pb-4 border-b border-[#E3E3E1]">
                    <h3 class="font-bold font-['Playfair_Display'] text-lg text-[#242E24]">Kategori Produk</h3>
                    @if($currentCategory || $search)
                        <a href="{{ route('catalog.index') }}" class="text-xs text-[#E8343B] font-semibold hover:underline">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <nav class="mt-4 space-y-2">
                    <!-- Semua Kategori -->
                    <a href="{{ route('catalog.index', $search ? ['search' => $search] : []) }}"
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ empty($currentCategory) ? 'bg-[#EBF2E8] text-[#242E24] font-semibold' : 'text-[#4B5549] hover:bg-[#F7F7F6]' }}">
                        <span>Semua Kategori</span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ empty($currentCategory) ? 'bg-white text-[#242E24]' : 'bg-[#F7F7F6] text-[#869480]' }}">
                            {{ $products->total() }}
                        </span>
                    </a>

                    <!-- Tree Kategori -->
                    @foreach($categoryTree as $parent)
                        @php
                            $isParentActive = $currentCategory && $currentCategory->id === $parent->id;
                            $hasActiveChild = $currentCategory && $parent->children->contains('id', $currentCategory->id);
                        @endphp
                        <div class="pt-2">
                            <a href="{{ route('catalog.index', array_merge(['kategori' => $parent->slug], $search ? ['search' => $search] : [])) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold transition-colors {{ ($isParentActive || $hasActiveChild) ? 'text-[#242E24] bg-[#EBF2E8]/60' : 'text-[#242E24] hover:bg-[#F7F7F6]' }}">
                                <span>{{ $parent->name }}</span>
                                <span class="text-xs text-[#869480]">
                                    ({{ $parent->products_count + $parent->children->sum('products_count') }})
                                </span>
                            </a>

                            @if($parent->children->isNotEmpty())
                                <div class="ml-3 pl-3 border-l-2 border-[#E3E3E1] mt-1 space-y-1">
                                    @foreach($parent->children as $child)
                                        @php
                                            $isChildActive = $currentCategory && $currentCategory->id === $child->id;
                                        @endphp
                                        <a href="{{ route('catalog.index', array_merge(['kategori' => $child->slug], $search ? ['search' => $search] : [])) }}"
                                           class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs transition-colors {{ $isChildActive ? 'bg-[#242E24] text-white font-semibold' : 'text-[#4B5549] hover:text-[#242E24] hover:bg-[#F7F7F6]' }}">
                                            <span>{{ $child->name }}</span>
                                            <span class="opacity-75">({{ $child->products_count }})</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>

                <!-- Box Konsultasi Mini di Sidebar -->
                <div class="mt-8 pt-6 border-t border-[#E3E3E1]">
                    <div class="bg-[#F7F7F6] rounded-xl p-4 text-center">
                        <div class="w-10 h-10 rounded-full bg-[#EBF2E8] text-[#869480] flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold text-[#242E24]">Butuh Bantuan Memilih?</h4>
                        <p class="text-[11px] text-[#4B5549] mt-1 leading-relaxed">
                            Diskusikan kondisi Anda dengan klinisi ortotis prostetis kami secara gratis via WhatsApp.
                        </p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['company.whatsapp'] ?? '6281234567890') }}?text={{ urlencode('Halo SAM, saya ingin konsultasi mengenai alat yang cocok untuk kondisi saya.') }}"
                           target="_blank"
                           class="mt-3 inline-flex items-center justify-center w-full px-3 py-2 text-xs font-semibold rounded-lg text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs">
                            Hubungi Tim Klinis
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Area Produk Utama -->
        <main class="lg:col-span-3 space-y-6">
            <!-- Filter Bar & Search -->
            <div class="bg-white rounded-2xl p-4 border border-[#E3E3E1] shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <form action="{{ route('catalog.index') }}" method="GET" class="relative flex-1">
                    @if($currentCategory)
                        <input type="hidden" name="kategori" value="{{ $currentCategory->slug }}">
                    @endif
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari alat (cth: AFO, Kaki Palsu, KAFO, Skoliosis)..."
                           class="w-full text-sm rounded-xl border border-[#E3E3E1] py-2.5 pl-10 pr-10 focus:outline-hidden focus:border-[#869480] bg-[#F7F7F6]">
                    <svg class="w-4 h-4 text-[#869480] absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if($search)
                        <a href="{{ route('catalog.index', $currentCategory ? ['kategori' => $currentCategory->slug] : []) }}"
                           class="absolute right-3 top-2.5 text-xs text-[#869480] hover:text-[#242E24] p-1">
                            &times;
                        </a>
                    @endif
                </form>

                <div class="text-xs text-[#4B5549] whitespace-nowrap self-center">
                    Menampilkan <span class="font-bold text-[#242E24]">{{ $products->count() }}</span> dari <span class="font-bold text-[#242E24]">{{ $products->total() }}</span> alat
                </div>
            </div>

            <!-- Active Filter Badges -->
            @if($currentCategory || $search)
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-[#869480]">Filter aktif:</span>
                    @if($currentCategory)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-[#EBF2E8] text-[#242E24]">
                            Kategori: {{ $currentCategory->name }}
                            <a href="{{ route('catalog.index', $search ? ['search' => $search] : []) }}" class="text-[#869480] hover:text-[#E8343B] font-bold ml-1">&times;</a>
                        </span>
                    @endif
                    @if($search)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-[#EBF2E8] text-[#242E24]">
                            Pencarian: "{{ $search }}"
                            <a href="{{ route('catalog.index', $currentCategory ? ['kategori' => $currentCategory->slug] : []) }}" class="text-[#869480] hover:text-[#E8343B] font-bold ml-1">&times;</a>
                        </span>
                    @endif
                </div>
            @endif

            <!-- Grid Produk -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <article class="group bg-white rounded-2xl border border-[#E3E3E1] overflow-hidden shadow-xs hover:shadow-md hover:border-[#869480]/50 transition-all duration-300 flex flex-col">
                            <!-- Foto Produk -->
                            <a href="{{ route('catalog.show', $product->slug) }}" class="relative aspect-4/3 bg-[#F7F7F6] overflow-hidden block">
                                @if($product->photo)
                                    <img src="{{ Storage::disk('public')->url($product->photo) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center text-[#869480]/60 bg-gradient-to-br from-[#F7F7F6] to-[#EBF2E8]/40">
                                        <svg class="w-12 h-12 stroke-current mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-[#869480]">SAM Custom Orthotic</span>
                                    </div>
                                @endif

                                @if($product->category)
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-white/90 backdrop-blur-xs text-[#242E24] shadow-xs">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                            </a>

                            <!-- Konten Produk -->
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="font-bold font-['Playfair_Display'] text-base text-[#242E24] group-hover:text-[#869480] transition-colors leading-snug">
                                    <a href="{{ route('catalog.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>

                                @if($product->indication)
                                    <p class="text-xs text-[#869480] mt-1.5 font-medium line-clamp-1">
                                        Indikasi: {{ $product->indication }}
                                    </p>
                                @endif

                                @if($product->function)
                                    <p class="text-xs text-[#4B5549] mt-2 line-clamp-2 leading-relaxed">
                                        {{ $product->function }}
                                    </p>
                                @elseif($product->description)
                                    <p class="text-xs text-[#4B5549] mt-2 line-clamp-2 leading-relaxed">
                                        {{ Str::limit(strip_tags($product->description), 100) }}
                                    </p>
                                @endif

                                <!-- Footer Card -->
                                <div class="mt-auto pt-4 border-t border-[#E3E3E1]/60 flex items-center justify-between gap-2">
                                    <a href="{{ route('catalog.show', $product->slug) }}"
                                       class="inline-flex items-center text-xs font-semibold text-[#242E24] hover:text-[#869480] transition-colors">
                                        <span>Lihat Detail</span>
                                        <span class="ml-1 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                                    </a>

                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['company.whatsapp'] ?? '6281234567890') }}?text={{ urlencode('Halo SAM, saya ingin tanya mengenai produk ' . $product->name . '.') }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                        </svg>
                                        <span>Tanya WA</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-6">
                    {{ $products->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-3xl p-12 text-center border border-[#E3E3E1] shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-[#EBF2E8] text-[#869480] flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-xl text-[#242E24] font-['Playfair_Display']">Produk Tidak Ditemukan</h3>
                    <p class="text-sm text-[#4B5549] max-w-md mx-auto mt-2 leading-relaxed">
                        @if($search)
                            Tidak ada alat ortotik atau prostetik yang cocok dengan kata kunci "{{ $search }}".
                        @else
                            Belum ada produk aktif dalam kategori ini.
                        @endif
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('catalog.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#242E24] hover:bg-[#323D32] transition-colors">
                            Lihat Semua Produk
                        </a>
                        <a href="{{ route('appointment') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-colors">
                            Konsultasikan Kebutuhan Kustom
                        </a>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
