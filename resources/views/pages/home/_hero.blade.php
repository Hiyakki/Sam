@php
    $heroTitle = $settings['home.hero_title'] ?? 'Kembalikan Langkah & Mobilitas Anda dengan Presisi';
    $heroSubtitle = $settings['home.hero_subtitle'] ?? 'SAM Orthotic Prosthetic menghadirkan solusi pembuatan alat bantu gerak tubuh kustom sejak 2014.';
    $heroCta = $settings['home.cta_text'] ?? 'Buat Janji Konsultasi';
    $heroImages = $settings['home.hero_image'] ?? [];
    if (is_string($heroImages)) {
        $heroImages = [$heroImages];
    }
    
    $heroPhotoUrls = collect($heroImages)->map(fn($img) => Storage::url($img))->toArray();
    
    if (empty($heroPhotoUrls)) {
        $heroPhotoUrls = file_exists(public_path('storage/site/hero-clinic.webp')) ? [asset('storage/site/hero-clinic.webp')] : [];
    }
    $whatsapp = $settings['contact.whatsapp'] ?? '6281234567890';
@endphp

<section class="relative overflow-hidden bg-gradient-to-b from-surface-soft/80 via-background to-white py-14 sm:py-20 lg:py-24 border-b border-gray-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
            
            <!-- Left: Text & Action CTAs (7 cols) -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-primary/20 text-primary text-xs font-semibold tracking-wide shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                    <span>Pelayanan Ortotik & Prostetik Kustom Sejak 2014</span>
                </div>

                <!-- Big Headline (Serif) -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-['Playfair_Display'] text-primary tracking-tight leading-[1.14]">
                    {!! nl2br(e($heroTitle)) !!}
                </h1>

                <!-- Subtitle (1-2 sentences) -->
                <p class="text-base sm:text-lg text-text-muted max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    {{ $heroSubtitle }}
                </p>

                <!-- Action Buttons -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <x-button variant="accent" :href="route('appointment')" class="w-full sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $heroCta }}
                    </x-button>

                    <x-button variant="outline" href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Halo SAM Orthotic Prosthetic, saya ingin berkonsultasi mengenai alat bantu gerak.') }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto">
                        <svg class="w-5 h-5 mr-2 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/>
                        </svg>
                        Chat WhatsApp
                    </x-button>
                </div>

                <!-- Verified Factual Trust Bar -->
                <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 border-t border-gray-200/90 max-w-xl mx-auto lg:mx-0">
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary">{{ $stats['since_year'] }}</div>
                        <div class="text-xs text-text-muted mt-0.5 font-medium">Tahun Berdiri</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary">{{ $stats['branches_count'] }}</div>
                        <div class="text-xs text-text-muted mt-0.5 font-medium">Kantor Aktif</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary">{{ $stats['products_count'] }}+</div>
                        <div class="text-xs text-text-muted mt-0.5 font-medium">Produk Kustom</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary">{{ $stats['services_count'] }}</div>
                        <div class="text-xs text-text-muted mt-0.5 font-medium">Layanan Utama</div>
                    </div>
                </div>
            </div>

            <!-- Right: Photo in Rounded Frame + Organic Shapes + Floating Cards (5 cols) -->
            <div class="lg:col-span-5 relative mt-4 lg:mt-0">
                <!-- Stacked Organic Background Shapes -->
                <div class="absolute -top-6 -right-6 w-64 h-64 sm:w-80 sm:h-80 rounded-full bg-primary/10 -z-10 blur-xl"></div>
                <div class="absolute -bottom-8 -left-8 w-56 h-56 sm:w-72 sm:h-72 rounded-3xl bg-surface-soft border border-primary/10 -rotate-6 -z-10"></div>
                <div class="absolute top-1/2 -right-10 w-40 h-40 rounded-full bg-accent/10 blur-2xl -z-10"></div>

                <!-- Main Rounded Photo Frame -->
                <div class="relative mx-auto max-w-md lg:max-w-none rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-white"
                     x-data="{ 
                        activeSlide: 0, 
                        slides: {{ json_encode($heroPhotoUrls) }},
                        init() {
                            if (this.slides.length > 1) {
                                setInterval(() => {
                                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                                }, 3500);
                            }
                        }
                     }">
                     
                    <template x-if="slides.length > 0">
                        <div class="relative w-full aspect-4/3 sm:aspect-1/1 lg:aspect-4/3">
                            <template x-for="(slide, index) in slides" :key="index">
                                <img :src="slide"
                                     alt="Suasana Pelayanan SAM Orthotic Prosthetic"
                                     x-show="activeSlide === index"
                                     x-transition:enter="transition-opacity ease-out duration-1000"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition-opacity ease-in duration-1000"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0 w-full h-full object-cover">
                            </template>
                        </div>
                    </template>

                    <template x-if="slides.length === 0">
                        <!-- Decorative Illustration if photo not available -->
                        <div class="w-full aspect-4/3 bg-gradient-to-tr from-surface-soft via-white to-surface-soft p-8 flex flex-col justify-between">
                            <div class="space-y-3">
                                <span class="inline-block px-3 py-1 rounded-full bg-white text-xs font-semibold text-primary tracking-wider uppercase border border-gray-200 shadow-2xs">
                                    Workshop & Klinik
                                </span>
                                <p class="text-2xl font-bold font-['Playfair_Display'] text-primary">
                                    Presisi Ergonomis untuk Mobilitas Pasien
                                </p>
                                <p class="text-xs sm:text-sm text-text-muted leading-relaxed">
                                    Setiap alat diproduksi melalui pengukuran detail dan uji coba fitting langsung.
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
</section>
