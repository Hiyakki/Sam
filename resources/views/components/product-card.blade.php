@props([
    'product',
    'whatsapp' => '6281234567890',
    'class' => '',
])

@php
    $photoUrl = $product->photo ? Storage::disk('public')->url($product->photo) : null;
    $waMessage = 'Halo SAM, saya ingin bertanya tentang produk ' . $product->name . ' (' . route('catalog.show', $product->slug) . ')';
@endphp

<div class="group bg-white rounded-3xl border border-gray-200/90 overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-primary/40 {{ $class }}">
    <!-- Image Frame (object-contain over soft blue background) -->
    <div class="aspect-4/3 w-full bg-surface-soft relative flex items-center justify-center p-6 overflow-hidden">
        @if ($photoUrl)
            <img src="{{ $photoUrl }}"
                 alt="{{ $product->name }}"
                 width="400"
                 height="300"
                 loading="lazy"
                 class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-108 drop-shadow-sm">
        @else
            <!-- Stylized Medical Device Icon Placeholder (Bukan kotak abu kosong) -->
            <div class="w-20 h-20 rounded-2xl bg-white/80 border border-primary/10 flex flex-col items-center justify-center text-primary shadow-xs">
                <svg class="w-10 h-10 stroke-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span class="text-[10px] font-bold mt-1 text-primary tracking-wider uppercase">SAM</span>
            </div>
        @endif

        @if ($product->category)
            <span class="absolute top-3.5 left-3.5 px-3 py-1 rounded-full bg-white/95 backdrop-blur-xs text-[11px] font-semibold text-primary shadow-2xs border border-gray-200/60">
                {{ $product->category->name }}
            </span>
        @endif
    </div>

    <!-- Content Details -->
    <div class="p-6 flex-1 flex flex-col justify-between">
        <div>
            <h3 class="text-lg font-bold font-['Playfair_Display'] text-primary mb-1.5 group-hover:text-accent transition-colors line-clamp-1">
                <a href="{{ route('catalog.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            @if ($product->function)
                <p class="text-xs text-primary/90 font-medium mb-1 line-clamp-1">
                    <span class="text-text-muted">Fungsi:</span> {{ $product->function }}
                </p>
            @endif

            @if ($product->indication)
                <p class="text-xs text-text-muted line-clamp-2 leading-relaxed">
                    {{ $product->indication }}
                </p>
            @endif
        </div>

        <!-- Action Links -->
        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
            <a href="{{ route('catalog.show', $product->slug) }}"
               class="text-xs font-semibold text-primary hover:text-accent inline-flex items-center gap-1 transition-colors">
                <span>Detail Produk</span>
                <span>&rarr;</span>
            </a>

            <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode($waMessage) }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#128C7E] bg-[#25D366]/10 hover:bg-[#25D366]/20 transition-colors">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/>
                </svg>
                <span>Tanya WA</span>
            </a>
        </div>
    </div>
</div>
