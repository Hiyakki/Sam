@extends('layouts.app')

@section('title', 'Tentang Kami - SAM Orthotic Prosthetic')

@section('content')
<div class="bg-gradient-to-b from-[#EBF2E8]/40 to-transparent py-12 sm:py-16 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Profil Usaha</span>
            <h1 class="text-3xl sm:text-5xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2 tracking-tight">
                Tentang SAM Orthotic Prosthetic
            </h1>
            <p class="text-base sm:text-lg text-[#4B5549] mt-4 leading-relaxed">
                Berdiri sejak 2014, kami mendedikasikan keahlian dan kepedulian kami untuk membantu setiap pasien kembali melangkah mandiri melalui alat bantu gerak tubuh presisi.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <div class="lg:col-span-8 space-y-10">
            <!-- Cerita -->
            <div class="bg-white rounded-2xl p-8 border border-[#E3E3E1] shadow-xs">
                <h2 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24] mb-4">
                    Perjalanan & Dedikasi Kami
                </h2>
                <p class="text-[#4B5549] leading-relaxed mb-4">
                    SAM Orthotic Prosthetic (dikenal melalui media sosial @sam_kakipalsu) telah melayani ratusan pasien dari berbagai wilayah di Indonesia. Kami memahami bahwa setiap pasien memiliki kondisi fisik, aktivitas harian, dan tujuan pemulihan yang unik.
                </p>
                <p class="text-[#4B5549] leading-relaxed">
                    Oleh karena itu, setiap alat dibuat secara kustom (custom-made) dengan kombinasi pengukuran anatomis yang akurat, material berkualitas tinggi, serta sesi fitting dan evaluasi berkala untuk memastikan kenyamanan dan fungsi yang maksimal.
                </p>
            </div>

            <!-- Visi & Misi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-[#F7F7F6] rounded-2xl p-6 border border-[#E3E3E1]">
                    <div class="w-10 h-10 rounded-lg bg-[#A5BF98] text-white flex items-center justify-center font-bold mb-4">
                        V
                    </div>
                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24] mb-2">Visi Kami</h3>
                    <p class="text-sm text-[#4B5549] leading-relaxed">
                        Menjadi pusat layanan ortotik dan prostetik terpercaya yang menghadirkan alat bantu gerak berkualitas tinggi, nyaman, dan terjangkau bagi seluruh lapisan masyarakat.
                    </p>
                </div>

                <div class="bg-[#F7F7F6] rounded-2xl p-6 border border-[#E3E3E1]">
                    <div class="w-10 h-10 rounded-lg bg-[#869480] text-white flex items-center justify-center font-bold mb-4">
                        M
                    </div>
                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24] mb-2">Misi Kami</h3>
                    <p class="text-sm text-[#4B5549] leading-relaxed">
                        Memberikan pelayanan penuh empati, menerapkan teknik pengukuran dan fitting presisi, serta terus melakukan evaluasi dan pendampingan perawatan alat bagi pasien.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar / Cabang -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-[#EBF2E8]/60 border border-[#A5BF98]/40 rounded-2xl p-6">
                <h3 class="text-lg font-bold font-['Playfair_Display'] text-[#242E24] mb-3">
                    Lokasi Kantor & Workshop
                </h3>
                <p class="text-xs text-[#4B5549] mb-4">
                    Kunjungi cabang kami untuk konsultasi langsung dan sesi pengukuran.
                </p>
                <div class="space-y-4 text-sm text-[#242E24]">
                    <div class="border-t border-[#A5BF98]/30 pt-3">
                        <div class="font-bold">Workshop & Klinik Utama</div>
                        <div class="text-xs text-[#4B5549] mt-1">Alamat dapat dikelola dan diperbarui dari panel admin.</div>
                        <div class="text-xs text-[#869480] font-semibold mt-1">Senin - Sabtu (08:00 - 17:00 WIB)</div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-[#A5BF98]/30">
                    <a href="{{ route('appointment') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-colors">
                        Buat Janji Kunjungan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
