@if (!empty($galleryPhotos) && count($galleryPhotos) >= 3)
<x-section bg="white" id="galeri-suasana">
    <x-section-heading
        badge="Dokumentasi Fasilitas"
        title="Suasana Workshop & Pelayanan Nyata"
        subtitle="Melihat lebih dekat fasilitas mandiri dan interaksi langsung kami dengan pasien."
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6 items-center">
        <!-- Photo 1: Big landscape (7 cols) -->
        <div class="lg:col-span-7 rounded-3xl overflow-hidden shadow-md border-4 border-white bg-surface-soft group relative aspect-16/10">
            <img src="{{ $galleryPhotos[0]['path'] }}"
                 alt="{{ $galleryPhotos[0]['alt'] }}"
                 width="800"
                 height="500"
                 loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                <span class="text-white text-sm font-semibold tracking-wide">{{ $galleryPhotos[0]['title'] }}</span>
            </div>
        </div>

        <!-- Right stacked photos (5 cols) -->
        <div class="lg:col-span-5 grid grid-cols-1 gap-6">
            @if (isset($galleryPhotos[1]))
                <div class="rounded-3xl overflow-hidden shadow-md border-4 border-white bg-surface-soft group relative aspect-16/9">
                    <img src="{{ $galleryPhotos[1]['path'] }}"
                         alt="{{ $galleryPhotos[1]['alt'] }}"
                         width="600"
                         height="340"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
                        <span class="text-white text-xs font-semibold tracking-wide">{{ $galleryPhotos[1]['title'] }}</span>
                    </div>
                </div>
            @endif

            @if (isset($galleryPhotos[2]))
                <div class="rounded-3xl overflow-hidden shadow-md border-4 border-white bg-surface-soft group relative aspect-16/9">
                    <img src="{{ $galleryPhotos[2]['path'] }}"
                         alt="{{ $galleryPhotos[2]['alt'] }}"
                         width="600"
                         height="340"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
                        <span class="text-white text-xs font-semibold tracking-wide">{{ $galleryPhotos[2]['title'] }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-section>
@endif
