@extends('layouts.app')

@section('title', 'SAM Orthotic Prosthetic - Solusi Kaki Palsu & Penyangga Tubuh Terpercaya')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-[#EBF2E8]/60 via-white to-white py-16 sm:py-24 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#EBF2E8] border border-[#A5BF98]/40 text-[#444E41] text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-[#E8343B]"></span>
                    <span>Melayani Pembuatan Alat Ortotik & Prostetik Sejak 2014</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-['Playfair_Display'] text-[#242E24] tracking-tight leading-[1.15]">
                    Kembalikan <span class="text-[#869480] italic">Langkah & Mobilitas</span> Anda dengan Presisi
                </h1>

                <p class="text-base sm:text-lg text-[#4B5549] max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    SAM Orthotic Prosthetic menghadirkan solusi pembuatan alat bantu gerak tubuh (penyangga/brace) dan anggota gerak tiruan (kaki & tangan palsu) berkualitas tinggi, dirancang khusus sesuai anatomi dan kenyamanan optimal setiap pasien.
                </p>

                <!-- Action CTAs -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('appointment') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-base font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Buat Janji Konsultasi
                    </a>

                    <a href="https://wa.me/6281234567890?text=Halo%20SAM%20Orthotic%20Prosthetic,%20saya%20ingin%20berkonsultasi"
                       target="_blank"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-base font-semibold text-[#242E24] bg-white border border-[#E3E3E1] hover:bg-[#F7F7F6] hover:border-[#869480] transition-all shadow-xs">
                        <svg class="w-5 h-5 mr-2 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/>
                        </svg>
                        Chat WhatsApp
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="pt-6 grid grid-cols-3 gap-4 border-t border-[#E3E3E1]/70 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <div class="text-2xl font-bold font-['Playfair_Display'] text-[#869480]">10+ Thn</div>
                        <div class="text-xs text-[#4B5549] mt-0.5">Pengalaman Sejak 2014</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold font-['Playfair_Display'] text-[#869480]">100%</div>
                        <div class="text-xs text-[#4B5549] mt-0.5">Custom & Fitting Akurat</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold font-['Playfair_Display'] text-[#869480]">Garansi</div>
                        <div class="text-xs text-[#4B5549] mt-0.5">Perbaikan & Evaluasi</div>
                    </div>
                </div>
            </div>

            <!-- Right Visual Graphic -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <div class="w-full aspect-4/3 rounded-3xl bg-gradient-to-tr from-[#A5BF98]/40 to-[#869480]/20 p-8 border border-[#A5BF98]/30 flex flex-col justify-between shadow-sm relative overflow-hidden">
                        <div class="space-y-3">
                            <span class="inline-block px-3 py-1 rounded-full bg-white/80 text-xs font-semibold text-[#444E41] tracking-wider uppercase">
                                Workshop Mandiri
                            </span>
                            <h3 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24]">
                                Presisi Ergonomis untuk Kebutuhan Tiap Pasien
                            </h3>
                            <p class="text-sm text-[#4B5549] leading-relaxed">
                                Setiap alat dibuat melalui pengukuran detail, uji coba fitting, dan penyesuaian berkelanjutan agar pasien dapat kembali mandiri dan percaya diri.
                            </p>
                        </div>

                        <div class="bg-white/90 backdrop-blur-xs rounded-2xl p-4 border border-[#E3E3E1] shadow-xs flex items-center justify-between">
                            <div>
                                <div class="text-xs text-[#869480] font-semibold uppercase tracking-wider">Layanan Konsultasi</div>
                                <div class="text-sm font-bold text-[#242E24]">Reservasi Jadwal Fleksibel</div>
                            </div>
                            <a href="{{ route('appointment') }}" class="text-xs font-bold text-[#E8343B] hover:underline flex items-center gap-1">
                                <span>Pilih Slot</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Kategori Utama -->
<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Katalog Alat Bantu</span>
            <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Kategori Produk Utama
            </h2>
            <p class="text-sm sm:text-base text-[#4B5549] mt-3">
                Kami merancang dan memproduksi tiga kelompok utama alat bantu ortopedi dan rehabilitasi fisik dengan standar mutu terbaik.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1: Orthosis -->
            <div class="group bg-[#F7F7F6] hover:bg-[#EBF2E8]/40 border border-[#E3E3E1] hover:border-[#A5BF98] rounded-2xl p-8 transition-all duration-300 hover:shadow-md flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-[#A5BF98] text-white flex items-center justify-center font-bold text-xl mb-6 shadow-xs">
                        01
                    </div>
                    <h3 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24] mb-3">
                        Orthosis (Brace & Penyangga)
                    </h3>
                    <p class="text-sm text-[#4B5549] leading-relaxed mb-6">
                        Alat bantu penyangga tubuh untuk fiksasi, koreksi, dan mobilisasi: Spinal Orthosis (Skoliosis/Kifosis), Upper Limb, dan Lower Limb (AFO, KAFO, Insole).
                    </p>
                </div>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center text-sm font-semibold text-[#869480] group-hover:text-[#444E41] transition-colors">
                    Lihat Produk Orthosis &rarr;
                </a>
            </div>

            <!-- Card 2: Prosthesis -->
            <div class="group bg-[#F7F7F6] hover:bg-[#EBF2E8]/40 border border-[#E3E3E1] hover:border-[#A5BF98] rounded-2xl p-8 transition-all duration-300 hover:shadow-md flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-[#869480] text-white flex items-center justify-center font-bold text-xl mb-6 shadow-xs">
                        02
                    </div>
                    <h3 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24] mb-3">
                        Prosthesis (Kaki & Tangan Tiruan)
                    </h3>
                    <p class="text-sm text-[#4B5549] leading-relaxed mb-6">
                        Pengganti anggota tubuh yang hilang akibat amputasi atau kelainan bawaan: Kaki palsu bawah/atas lutut (Transtibial/Transfemoral), tangan palsu, dan jari tiruan.
                    </p>
                </div>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center text-sm font-semibold text-[#869480] group-hover:text-[#444E41] transition-colors">
                    Lihat Produk Prosthesis &rarr;
                </a>
            </div>

            <!-- Card 3: Komponen -->
            <div class="group bg-[#F7F7F6] hover:bg-[#EBF2E8]/40 border border-[#E3E3E1] hover:border-[#A5BF98] rounded-2xl p-8 transition-all duration-300 hover:shadow-md flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-[#242E24] text-white flex items-center justify-center font-bold text-xl mb-6 shadow-xs">
                        03
                    </div>
                    <h3 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24] mb-3">
                        Komponen & Suku Cadang
                    </h3>
                    <p class="text-sm text-[#4B5549] leading-relaxed mb-6">
                        Komponen pelengkap dan suku cadang berkualitas: Socket, Knee Joint (Mechanical/Hydraulic/C-Leg), Foot Component, Adaptor Socket, Tube, dan Klem.
                    </p>
                </div>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center text-sm font-semibold text-[#869480] group-hover:text-[#444E41] transition-colors">
                    Lihat Komponen &rarr;
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-16 bg-[#242E24] text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] max-w-2xl mx-auto">
            Butuh Konsultasi Mengenai Alat Bantu yang Tepat?
        </h2>
        <p class="text-sm sm:text-base text-[#E3E3E1]/80 max-w-xl mx-auto mt-4">
            Tim kami siap membantu Anda memilih solusi alat ortotik atau prostetik yang paling tepat melalui pemeriksaan langsung.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('appointment') }}" class="px-8 py-3.5 rounded-xl font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-all shadow-md">
                Jadwalkan Janji Temu Sekarang
            </a>
            <a href="{{ route('faq') }}" class="px-8 py-3.5 rounded-xl font-semibold text-white/90 bg-white/10 hover:bg-white/20 transition-all border border-white/20">
                Baca Pertanyaan Umum (FAQ)
            </a>
        </div>
    </div>
</section>
@endsection
