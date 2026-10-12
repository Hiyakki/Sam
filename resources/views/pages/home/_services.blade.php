<x-section bg="surface" id="alur-layanan">
    <x-section-heading
        badge="Alur Pelayanan"
        title="3 Tahap Pembuatan & Pendampingan Alat"
        subtitle="Proses terstruktur untuk memastikan setiap alat nyaman digunakan dan mendukung mobilitas mandiri."
    />

    <!-- 3-Step Timeline with Connecting Lines -->
    <div class="relative max-w-5xl mx-auto">
        <!-- Connecting Line (Desktop) -->
        <div class="hidden md:block absolute top-1/2 left-16 right-16 h-0.5 bg-primary/20 -translate-y-12 z-0"></div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
            <!-- Step 1: Konsultasi -->
            <div class="bg-white rounded-3xl p-8 border border-gray-200/90 shadow-xs flex flex-col justify-between text-center relative hover:-translate-y-1 transition-transform duration-300">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-surface-soft text-primary font-bold text-xl flex items-center justify-center mx-auto mb-6 border border-primary/10 shadow-2xs relative">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-accent text-white font-bold text-xs flex items-center justify-center shadow-xs">
                            1
                        </span>
                    </div>

                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-primary mb-3">
                        1. Konsultasi & Asesmen
                    </h3>

                    <p class="text-xs sm:text-sm text-text-muted leading-relaxed">
                        Pemeriksaan kondisi anatomis, analisis pola gerak atau keluhan, serta diskusi pemilihan spesifikasi alat yang paling sesuai.
                    </p>
                </div>
            </div>

            <!-- Step 2: Pengukuran & Fitting -->
            <div class="bg-white rounded-3xl p-8 border border-gray-200/90 shadow-xs flex flex-col justify-between text-center relative hover:-translate-y-1 transition-transform duration-300">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-surface-soft text-primary font-bold text-xl flex items-center justify-center mx-auto mb-6 border border-primary/10 shadow-2xs relative">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.828 2.828a2 2 0 01-2.828 0l-1.414-1.414a2 2 0 010-2.828L9.757 9.757m4.364 4.364l-4.364-4.364"></path>
                        </svg>
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-accent text-white font-bold text-xs flex items-center justify-center shadow-xs">
                            2
                        </span>
                    </div>

                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-primary mb-3">
                        2. Pengukuran & Fitting
                    </h3>

                    <p class="text-xs sm:text-sm text-text-muted leading-relaxed">
                        Pengambilan cetakan anatomi, fabrikasi di workshop mandiri SAM, dan sesi fitting langsung untuk memastikan tingkat kenyamanan.
                    </p>
                </div>
            </div>

            <!-- Step 3: Perawatan & Penyesuaian -->
            <div class="bg-white rounded-3xl p-8 border border-gray-200/90 shadow-xs flex flex-col justify-between text-center relative hover:-translate-y-1 transition-transform duration-300">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-surface-soft text-primary font-bold text-xl flex items-center justify-center mx-auto mb-6 border border-primary/10 shadow-2xs relative">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-accent text-white font-bold text-xs flex items-center justify-center shadow-xs">
                            3
                        </span>
                    </div>

                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-primary mb-3">
                        3. Perawatan & Evaluasi
                    </h3>

                    <p class="text-xs sm:text-sm text-text-muted leading-relaxed">
                        Evaluasi penggunaan berkala, panduan pemakaian mandiri, serta ketersediaan servis atau perbaikan alat bila diperlukan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Button at the end of section -->
        <div class="text-center mt-12">
            <x-button variant="accent" :href="route('appointment')">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Buat Janji Kunjungan Sekarang
            </x-button>
        </div>
    </div>
</x-section>
