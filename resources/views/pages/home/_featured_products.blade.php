@php
    $whatsapp = $settings['contact.whatsapp'] ?? '6281234567890';
@endphp

@if ($featuredProducts->isNotEmpty())
<x-section bg="white" id="produk-unggulan">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-14 gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-surface-soft border border-primary/15 text-primary text-xs font-semibold tracking-wide uppercase mb-3">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                <span>Koleksi Pilihan</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] text-primary tracking-tight">
                Produk Kustom Pilihan
            </h2>
            <p class="text-base text-text-muted mt-2">
                Beberapa pilihan alat ortotik dan prostetik yang paling sering dipesan oleh pasien kami.
            </p>
        </div>

        <a href="{{ route('catalog.index') }}"
           class="inline-flex items-center text-sm font-bold text-primary hover:text-accent transition-colors shrink-0">
            <span>Lihat Seluruh Katalog ({{ $stats['products_count'] }}+)</span>
            <span class="ml-1.5">&rarr;</span>
        </a>
    </div>

    <!-- Responsive Grid / Scroll-Snap Carousel on Mobile -->
    <div class="flex md:grid md:grid-cols-3 lg:grid-cols-4 gap-6 overflow-x-auto md:overflow-visible pb-4 md:pb-0 snap-x snap-mandatory scrollbar-none -mx-4 px-4 sm:mx-0 sm:px-0">
        @foreach ($featuredProducts as $item)
            <div class="min-w-[280px] sm:min-w-[300px] md:min-w-0 snap-start flex-1 flex">
                <x-product-card :product="$item" :whatsapp="$whatsapp" class="w-full" />
            </div>
        @endforeach
    </div>
</x-section>
@endif
