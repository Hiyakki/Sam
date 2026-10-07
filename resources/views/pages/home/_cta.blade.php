@php
    $whatsapp = $settings['contact.whatsapp'] ?? '6281234567890';
@endphp

<x-section bg="primary" id="cta-konsultasi" class="relative overflow-hidden">
    <!-- Soft Decorative Shapes -->
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-accent/10 blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl mx-auto text-center relative z-10 space-y-6">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white border border-white/15 text-xs font-semibold tracking-wide uppercase">
            <span>Mulai Langkah Baru Anda</span>
        </div>

        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] text-white tracking-tight leading-[1.2]">
            Konsultasikan Kebutuhan Alat Gerak Bersama Tim SAM
        </h2>

        <p class="text-base sm:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed font-normal">
            Jadwalkan sesi kunjungan untuk pemeriksaan anatomis dan konsultasi langsung mengenai pembuatan atau servis alat ortotik prostetik Anda.
        </p>

        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
            <x-button variant="accent" :href="route('appointment')" size="lg" class="w-full sm:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Buat Janji Kunjungan
            </x-button>

            <x-button variant="outline-white" href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Halo SAM Orthotic Prosthetic, saya ingin konsultasi mengenai alat gerak.') }}" target="_blank" rel="noopener noreferrer" size="lg" class="w-full sm:w-auto">
                <svg class="w-5 h-5 mr-2 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/>
                </svg>
                Chat WhatsApp
            </x-button>
        </div>
    </div>
</x-section>
