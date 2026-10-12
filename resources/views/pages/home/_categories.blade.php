<x-section bg="white" id="kategori">
    <x-section-heading
        badge="Katalog Alat Bantu"
        title="Kategori Produk Utama"
        subtitle="Solusi alat ortopedi dan pengganti anggota gerak yang dirancang kustom dengan standar presisi tinggi."
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 max-w-4xl mx-auto gap-8">
        @foreach ($categories as $index => $cat)
            @php
                $photoUrl = $cat->representative_photo ? Storage::disk('public')->url($cat->representative_photo) : null;
                $count = $cat->total_products_count ?? 0;
            @endphp
            <div class="group bg-surface-soft/60 hover:bg-surface-soft border border-gray-200/90 hover:border-primary/40 rounded-3xl p-7 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl">
                <div>
                    <!-- Category Representative Image Frame -->
                    <div class="aspect-16/10 w-full bg-white rounded-2xl overflow-hidden mb-6 relative border border-gray-100 flex items-center justify-center p-4">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}"
                                 alt="Kategori {{ $cat->name }}"
                                 width="400"
                                 height="250"
                                 loading="lazy"
                                 class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-108 drop-shadow-sm">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-surface-soft text-primary flex items-center justify-center text-xl font-bold">
                                0{{ $index + 1 }}
                            </div>
                        @endif

                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-primary text-white text-[11px] font-bold tracking-wider">
                            0{{ $index + 1 }}
                        </span>
                    </div>

                    <h3 class="text-2xl font-bold font-['Playfair_Display'] text-primary mb-3">
                        {{ $cat->name }}
                    </h3>

                    <p class="text-xs sm:text-sm text-text-muted leading-relaxed mb-6">
                        @if ($cat->name === 'Orthosis')
                            Alat bantu penyangga tubuh untuk koreksi, fiksasi, dan mobilisasi: Spinal Brace, Upper Limb, dan Lower Limb (AFO, KAFO, Insole).
                        @elseif ($cat->name === 'Prosthesis')
                            Pengganti anggota gerak tubuh pasca amputasi: Kaki palsu bawah/atas lutut (Transtibial/Transfemoral) dan tangan palsu.
                        @else
                            Koleksi alat bantu ortotik dan prostetik sesuai kebutuhan klinis Anda.
                        @endif
                    </p>
                </div>

                <div class="pt-5 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-xs font-semibold text-primary/80 bg-white px-3 py-1 rounded-full border border-gray-200">
                        {{ $count }} Produk Aktif
                    </span>
                    <a href="{{ route('catalog.index', ['kategori' => $cat->slug]) }}"
                       class="inline-flex items-center text-sm font-bold text-primary group-hover:text-accent transition-colors">
                        <span>Lihat Katalog</span>
                        <span class="ml-1.5">&rarr;</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</x-section>
