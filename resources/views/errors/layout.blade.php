@php
    $siteSettings = rescue(fn () => \App\Models\SiteSetting::allCached(), [], false);
    $rawWa = $siteSettings['contact.whatsapp'] ?? $siteSettings['company.whatsapp'] ?? '081234567890';
    $waPhone = preg_replace('/[^0-9]/', '', $rawWa) ?? '6281234567890';
    if (str_starts_with($waPhone, '0')) {
        $waPhone = '62' . substr($waPhone, 1);
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - SAM Orthotic Prosthetic</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sam.png') }}">
    <meta name="theme-color" content="#0f2b48">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex flex-col font-['Inter',sans-serif] antialiased">
    <!-- Header Minimal -->
    <header class="py-6 px-4 sm:px-6 lg:px-8 border-b border-gray-200/80 bg-white">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <img src="{{ asset('images/logo-sam.png') }}" alt="SAM Orthotic Prosthetic" class="h-10 sm:h-11 w-auto object-contain">
            </a>
            <a href="https://wa.me/{{ $waPhone }}" target="_blank" rel="noopener noreferrer"
               class="text-xs sm:text-sm font-semibold text-[#15803D] hover:text-[#166534] transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 fill-current text-[#25D366]" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                </svg>
                <span>Bantuan Pasien</span>
            </a>
        </div>
    </header>

    <!-- Content -->
    <main class="grow flex items-center justify-center p-6 sm:p-10">
        <div class="max-w-xl w-full bg-white rounded-3xl p-8 sm:p-12 border border-gray-200/90 shadow-sm text-center space-y-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl @yield('badge_bg', 'bg-slate-100 text-[#1E2A4A]') mx-auto ring-8 ring-slate-50">
                @yield('icon')
            </div>

            <div>
                <span class="text-5xl sm:text-6xl font-extrabold font-['Playfair_Display'] text-[#1E2A4A] tracking-tight block">
                    @yield('code')
                </span>
                <h1 class="text-xl sm:text-2xl font-bold font-['Playfair_Display'] text-[#1E2A4A] mt-3">
                    @yield('heading')
                </h1>
                <p class="text-sm text-[#64748B] mt-3 leading-relaxed max-w-md mx-auto">
                    @yield('message')
                </p>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#1E2A4A] hover:bg-[#141D36] transition-colors shadow-xs">
                    <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode('Halo SAM Orthotic Prosthetic, saya mengalami kendala pada halaman web: ' . url()->current()) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#25D366] hover:bg-[#20bd5a] transition-colors shadow-xs">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span>Hubungi WhatsApp</span>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer Minimal -->
    <footer class="py-6 px-4 text-center text-xs text-[#94A3B8] border-t border-gray-200/80 bg-white">
        <p>© 2014 - {{ date('Y') }} SAM Orthotic Prosthetic. Hak Cipta Dilindungi.</p>
    </footer>
</body>
</html>
