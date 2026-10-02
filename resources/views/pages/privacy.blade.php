@extends('layouts.app')

@section('title', 'Kebijakan Privasi - SAM Orthotic Prosthetic')

@section('content')
<div class="bg-gradient-to-b from-[#EBF2E8]/40 to-transparent py-12 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
        <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Kerahasiaan & Keamanan</span>
        <h1 class="text-3xl sm:text-4xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
            Kebijakan Privasi
        </h1>
        <p class="text-sm text-[#4B5549] mt-2">
            Terakhir diperbarui: {{ date('d F Y') }}
        </p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-[#E3E3E1] shadow-xs space-y-8 text-sm text-[#4B5549] leading-relaxed">
        <section class="space-y-3">
            <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                1. Komitmen Privasi Kami
            </h2>
            <p>
                SAM Orthotic Prosthetic berkomitmen melindungi dan menghormati privasi pasien serta pengunjung website kami sesuai dengan Undang-Undang Perlindungan Data Pribadi (UU PDP) Republik Indonesia.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                2. Data yang Kami Kumpulkan
            </h2>
            <p>Saat Anda membuat janji temu atau menghubungi kami, kami dapat mengumpulkan informasi berupa:</p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Nama lengkap</li>
                <li>Alamat email</li>
                <li>Nomor telepon / WhatsApp</li>
                <li>Usia</li>
                <li>Keluhan medis atau kondisi fisik terkait kebutuhan alat bantu gerak</li>
                <li>Salinan resep atau rujukan dokter (jika diunggah secara sukarela)</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                3. Penyimpanan & Keamanan Dokumen Medis
            </h2>
            <p>
                File resep dokter dan data medis disimpan dalam sistem penyimpanan privat (non-public disk) yang tidak dapat diakses bebas lewat internet publik dan hanya dapat dibuka oleh tenaga klinis/admin berwenang kami.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                4. Hak Pasien atas Data Pribadi
            </h2>
            <p>
                Anda berhak meminta informasi mengenai data pribadi Anda yang tersimpan, meminta perbaikan data, atau meminta penghapusan data janji temu Anda melalui kontak resmi customer service kami.
            </p>
        </section>
    </div>
</div>
@endsection
