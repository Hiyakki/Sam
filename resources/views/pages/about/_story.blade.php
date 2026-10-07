@php
    $story = $settings['about.story'] ?? 'SAM Orthotic Prosthetic berdiri pada tahun 2014 dengan semangat membantu masyarakat yang membutuhkan alat bantu gerak berkualitas.';
@endphp

<x-section bg="white" id="cerita">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        <!-- Left: Story Text & Pull-Quote (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <x-section-heading
                badge="Latar Belakang"
                title="Perjalanan Dedikasi Sejak 2014"
                align="left"
                class="!mb-6"
            />

            <!-- Main Story Prose -->
            <div class="text-text-muted text-base leading-relaxed space-y-4 font-normal prose max-w-none">
                {!! $story !!}
            </div>

            <!-- Stylized Pull-Quote -->
            <div class="p-6 sm:p-7 rounded-2xl bg-surface-soft border-l-4 border-accent text-primary relative">
                <p class="text-base sm:text-lg font-serif italic text-primary font-semibold leading-snug">
                    &ldquo;Kenyamanan alat bukan sekadar ukuran fisik, melainkan jembatan bagi pasien untuk mengembalikan kebebasan melangkah dan berkarya.&rdquo;
                </p>
                <div class="text-xs text-text-muted mt-2 font-medium">
                    &mdash; Prinsip Pelayanan SAM Orthotic Prosthetic
                </div>
            </div>
        </div>

        <!-- Right: Stacked Collage of 2-3 Real Rounded Photos (5 cols) -->
        <div class="lg:col-span-5 relative mt-6 lg:mt-0">
            <!-- Organic decorative shape behind collage -->
            <div class="absolute -top-6 -right-6 w-64 h-64 rounded-full bg-primary/10 blur-xl -z-10"></div>
            <div class="absolute -bottom-8 -left-8 w-60 h-60 rounded-3xl bg-surface-soft border border-primary/10 -rotate-6 -z-10"></div>

            <div class="relative space-y-4">
                <!-- Top Big Photo -->
                <div class="rounded-3xl overflow-hidden shadow-xl border-4 border-white bg-surface-soft aspect-16/10">
                    @if (file_exists(public_path('storage/site/consultation.webp')))
                        <img src="{{ asset('storage/site/consultation.webp') }}"
                             alt="Sesi Konsultasi Bersama Pasien SAM"
                             width="600"
                             height="380"
                             class="w-full h-full object-cover">
                    @elseif (!empty($galleryPhotos[0]))
                        <img src="{{ $galleryPhotos[0]['path'] }}"
                             alt="{{ $galleryPhotos[0]['alt'] }}"
                             width="600"
                             height="380"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-surface-soft flex items-center justify-center p-8 text-center text-primary font-bold">
                            Konsultasi & Pemeriksaan
                        </div>
                    @endif
                </div>

                <!-- Bottom Offset Photo Stacked -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl overflow-hidden shadow-lg border-3 border-white bg-surface-soft aspect-4/3 -mt-6 sm:-mt-8 ml-4 z-10">
                        @if (file_exists(public_path('storage/site/fitting.webp')))
                            <img src="{{ asset('storage/site/fitting.webp') }}"
                                 alt="Sesi Fitting Alat Pasien"
                                 width="300"
                                 height="220"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-surface-soft flex items-center justify-center text-xs font-bold text-primary p-2 text-center">
                                Sesi Fitting
                            </div>
                        @endif
                    </div>

                    <div class="rounded-2xl overflow-hidden shadow-lg border-3 border-white bg-surface-soft aspect-4/3 -mt-6 sm:-mt-8 mr-2 z-10">
                        @if (file_exists(public_path('storage/site/workshop.webp')))
                            <img src="{{ asset('storage/site/workshop.webp') }}"
                                 alt="Fabrikasi Workshop Mandiri"
                                 width="300"
                                 height="220"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-surface-soft flex items-center justify-center text-xs font-bold text-primary p-2 text-center">
                                Workshop Mandiri
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-section>
