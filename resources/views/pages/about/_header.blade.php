@php
    $siteName = $settings['general.site_name'] ?? 'SAM Orthotic Prosthetic';
@endphp

<section class="bg-primary text-white py-16 sm:py-20 relative overflow-hidden border-b border-primary-dark">
    <!-- Soft Decorative Shapes -->
    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-accent/10 blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Info (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs text-white/70 font-medium">
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
                    <span>/</span>
                    <span class="text-white">Tentang Kami</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-white border border-white/20 text-xs font-semibold tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-accent"></span>
                    <span>Tentang SAM Orthotic Prosthetic</span>
                </div>

                <h1 class="text-4xl sm:text-5xl font-bold font-['Playfair_Display'] tracking-tight leading-[1.2]">
                    Tentang SAM
                </h1>

                <p class="text-base sm:text-lg text-white/80 max-w-2xl leading-relaxed font-normal">
                    Membantu Anda melangkah kembali dengan percaya diri. Sejak 2014, kami mendedikasikan keahlian dan bengkel fabrikasi mandiri untuk menghadirkan alat bantu gerak presisi bagi setiap pasien.
                </p>
            </div>

            <!-- Right: Small Rounded Badge Image (4 cols) -->
            <div class="lg:col-span-4 hidden lg:flex justify-end">
                <div class="w-48 h-48 rounded-3xl overflow-hidden border-4 border-white/20 shadow-2xl relative rotate-3 bg-surface-soft">
                    @if (file_exists(public_path('storage/site/hero-clinic.webp')))
                        <img src="{{ asset('storage/site/hero-clinic.webp') }}"
                             alt="Workshop SAM"
                             width="200"
                             height="200"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-primary-dark flex items-center justify-center p-4 text-center">
                            <span class="font-bold text-white text-sm">Sejak 2014</span>
                        </div>
                    @endif
                    <div class="absolute bottom-2 left-2 right-2 bg-primary/90 backdrop-blur-xs py-1 px-2 rounded-xl text-center text-[10px] text-white font-semibold">
                        Klinik & Workshop
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
