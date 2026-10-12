@php
    $siteSettings = \App\Models\SiteSetting::allCached();
    $siteWa = $siteSettings['contact.whatsapp'] ?? '6281234567890';
    $siteIg = $siteSettings['contact.instagram'] ?? '@sam_kakipalsu';
    $siteEmail = $siteSettings['contact.email'] ?? 'sam.orthotic@example.com';
    $siteName = $siteSettings['general.site_name'] ?? 'SAM Orthotic Prosthetic';
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', $siteName . ' - Solusi Alat Bantu Gerak Terpercaya Sejak 2014')</title>
    <meta name="description" content="@yield('meta_description', 'Layanan pembuatan alat orthotic (penyangga/brace) dan prosthetic (kaki dan tangan palsu) berkualitas dengan fitting presisi dan tenaga ahli berpengalaman sejak 2014.')">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sam.png') }}">

    <!-- Open Graph / Meta Social -->
    <meta property="og:title" content="@yield('title', $siteName . ' - Solusi Alat Bantu Gerak Terpercaya Sejak 2014')">
    <meta property="og:description" content="@yield('meta_description', 'Layanan pembuatan alat orthotic (penyangga/brace) dan prosthetic (kaki dan tangan palsu) berkualitas dengan fitting presisi dan tenaga ahli berpengalaman sejak 2014.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background text-text antialiased min-h-screen flex flex-col font-['Inter',sans-serif]">

    <!-- Top Announcement Bar -->
    <div class="bg-primary-dark text-white text-xs py-2 px-4 text-center font-medium tracking-wide">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1 sm:gap-4">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                <span>Melayani Pembuatan Kaki Palsu, Tangan Palsu, & Penyangga Tubuh (Orthosis) Sejak 2014</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] sm:text-xs text-white/90">
                <a href="https://instagram.com/{{ ltrim($siteIg, '@') }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-1">
                    <span>IG: {{ $siteIg }}</span>
                </a>
                <span class="opacity-40">|</span>
                <span>Operasional: Senin - Sabtu (08:00 - 17:00 WIB)</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-200 shadow-xs" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-sam.png') }}" alt="{{ $siteName }}" class="h-11 sm:h-12 w-auto object-contain">
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="{{ route('home') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:text-primary hover:bg-surface-soft/60' }}">
                        Home
                    </a>
                    <a href="{{ route('about') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('about') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:text-primary hover:bg-surface-soft/60' }}">
                        About Us
                    </a>
                    <a href="{{ route('catalog.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('catalog.*') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:text-primary hover:bg-surface-soft/60' }}">
                        Katalog
                    </a>
                    <a href="{{ route('faq') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('faq') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:text-primary hover:bg-surface-soft/60' }}">
                        FAQ
                    </a>
                    <a href="{{ route('testimonials.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('testimonials.*') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:text-primary hover:bg-surface-soft/60' }}">
                        Testimoni
                    </a>
                    <a href="{{ route('appointment') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('appointment') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:text-primary hover:bg-surface-soft/60' }}">
                        Appointment
                    </a>
                </nav>

                <!-- Right Action Button (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('appointment') }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-full text-sm font-semibold text-white bg-accent hover:bg-accent-hover transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Buat Janji
                    </a>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            type="button"
                            class="p-2 rounded-lg text-text-muted hover:text-primary hover:bg-surface-soft focus:outline-hidden"
                            aria-label="Toggle navigation">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-b border-gray-200 bg-white px-4 pt-2 pb-6 space-y-2 shadow-lg">
            <a href="{{ route('home') }}"
               class="block px-3.5 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:bg-surface-soft' }}">
                Home
            </a>
            <a href="{{ route('about') }}"
               class="block px-3.5 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('about') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:bg-surface-soft' }}">
                About Us
            </a>
            <a href="{{ route('catalog.index') }}"
               class="block px-3.5 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('catalog.*') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:bg-surface-soft' }}">
                Katalog Produk
            </a>
            <a href="{{ route('faq') }}"
               class="block px-3.5 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('faq') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:bg-surface-soft' }}">
                FAQ (Tanya Jawab)
            </a>
            <a href="{{ route('testimonials.index') }}"
               class="block px-3.5 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('testimonials.*') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:bg-surface-soft' }}">
                Testimoni Pasien
            </a>
            <a href="{{ route('appointment') }}"
               class="block px-3.5 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('appointment') ? 'bg-surface-soft text-primary font-semibold' : 'text-text-muted hover:bg-surface-soft' }}">
                Appointment
            </a>
            <div class="pt-4">
                <a href="{{ route('appointment') }}"
                   class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-semibold text-white bg-accent hover:bg-accent-hover transition-colors shadow-sm">
                    Buat Janji Konsultasi
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Site Footer -->
    <footer class="bg-primary text-white border-t border-primary-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4">
                    <div class="bg-white p-2.5 rounded-2xl shadow-xs inline-flex items-center">
                        <img src="{{ asset('images/logo-sam.png') }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain">
                    </div>
                    <p class="text-sm text-white/80 leading-relaxed">
                        Penyedia layanan pembuatan alat bantu ortotik (brace/penyangga) dan prostetik (kaki & tangan tiruan) presisi dengan komitmen mengembalikan mobilitas dan kenyamanan hidup Anda.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-xs text-white/90 font-medium">
                        <span>Terpercaya Sejak 2014</span>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="text-base font-semibold font-['Playfair_Display'] text-white mb-4 tracking-wide">
                        Menu Utama
                    </h4>
                    <ul class="space-y-2.5 text-sm text-white/80">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Katalog Orthosis & Prosthesis</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">Pertanyaan Umum (FAQ)</a></li>
                        <li><a href="{{ route('testimonials.index') }}" class="hover:text-white transition-colors">Testimoni Pasien</a></li>
                        <li><a href="{{ route('appointment') }}" class="hover:text-white transition-colors">Reservasi Janji Temu</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan Kami -->
                <div>
                    <h4 class="text-base font-semibold font-['Playfair_Display'] text-white mb-4 tracking-wide">
                        Layanan Unggulan
                    </h4>
                    <ul class="space-y-2.5 text-sm text-white/80">
                        <li>Konsultasi Masalah Gerak & Postur</li>
                        <li>Fitting & Penyesuaian Alat</li>
                        <li>Maintenance & Perbaikan Alat</li>
                        <li>Prosthesis Atas & Bawah Tubuh</li>
                        <li>Orthosis Spinal, Upper & Lower Limb</li>
                    </ul>
                </div>

                <!-- Col 4: Kontak & Operasional -->
                <div class="space-y-4">
                    <h4 class="text-base font-semibold font-['Playfair_Display'] text-white mb-4 tracking-wide">
                        Kontak & Lokasi
                    </h4>
                    <ul class="space-y-3 text-sm text-white/80">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Workshop & Klinik Pusat SAM, Indonesia</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <a href="https://wa.me/{{ $siteWa }}" target="_blank" class="hover:text-white transition-colors">WhatsApp Customer Care</a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <a href="mailto:{{ $siteEmail }}" class="hover:text-white transition-colors">{{ $siteEmail }}</a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-accent shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <a href="https://instagram.com/{{ ltrim($siteIg, '@') }}" target="_blank" class="hover:text-white transition-colors">{{ $siteIg }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright & Disclaimer -->
            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/60">
                <p>© 2014 - {{ date('Y') }} {{ $siteName }}. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                    <span>•</span>
                    <span>Waktu Server: Asia/Jakarta</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
