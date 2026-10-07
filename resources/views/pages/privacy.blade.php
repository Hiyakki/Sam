@extends('layouts.app')

@section('title', 'Kebijakan Privasi - SAM Orthotic Prosthetic')
@section('meta_description', 'Kebijakan privasi perlindungan data medis dan informasi pribadi pasien SAM Orthotic Prosthetic.')

@section('content')
<!-- Header Banner -->
<div class="bg-gradient-to-b from-[#EBF2E8]/60 via-[#F7F7F6] to-[#F7F7F6] py-12 sm:py-16 border-b border-[#E3E3E1]/60">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-bold uppercase tracking-widest text-[#869480] bg-white/80 backdrop-blur-xs px-3 py-1 rounded-full border border-[#869480]/20 inline-block">
            Kerahasiaan & Keamanan
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] text-[#242E24] mt-3 tracking-tight">
            Kebijakan Privasi
        </h1>
        <p class="text-sm text-[#4B5549] mt-2">
            Perlindungan Data Pasien & Pengunjung Website SAM Orthotic Prosthetic
        </p>
    </div>
</div>

<!-- Privacy Content -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-[#E3E3E1] shadow-xs">
        @if(!empty($settings['general.privacy_policy']))
            <div class="prose prose-sm sm:prose-base max-w-none text-[#4B5549] leading-relaxed">
                {!! $settings['general.privacy_policy'] !!}
            </div>
        @else
            <div class="space-y-8 text-sm text-[#4B5549] leading-relaxed">
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                        1. Komitmen Privasi Kami
                    </h2>
                    <p>
                        SAM Orthotic Prosthetic berkomitmen melindungi dan menghormati privasi pasien serta pengunjung website kami sesuai dengan Undang-Undang Perlindungan Data Pribadi (UU PDP No. 27 Tahun 2022) Republik Indonesia. Dokumen ini menjelaskan bagaimana kami mengumpulkan, mengelola, dan melindungi data pribadi Anda.
                    </p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                        2. Data Pribadi yang Kami Kumpulkan
                    </h2>
                    <p>Saat Anda membuat janji temu, berkonsultasi, atau menghubungi kami, kami dapat mengumpulkan data berupa:</p>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li>Identitas dasar: Nama lengkap, usia, dan alamat domisili.</li>
                        <li>Kontak: Alamat email dan nomor telepon / WhatsApp yang dapat dihubungi.</li>
                        <li>Data medis relevan: Keluhan fisik, riwayat amputasi, skoliosis, kebutuhan brace atau prostesis.</li>
                        <li>Dokumen medis pendukung: Resep dokter, resume medis, atau hasil rontgen (jika diunggah secara sukarela oleh pasien).</li>
                    </ul>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                        3. Kerahasiaan Dokumen & Resep Dokter
                    </h2>
                    <p>
                        Dokumen resep dokter dan rujukan medis yang diunggah saat pemesanan janji temu disimpan dalam sistem penyimpanan privat (non-public storage disk) dengan enkripsi akses. File tersebut <strong>tidak dapat diakses atau diunduh oleh publik umum</strong> di internet dan hanya dapat dibuka oleh tenaga klinis serta administrator resmi kami untuk keperluan asesmen alat.
                    </p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                        4. Hak Pasien atas Data Pribadi
                    </h2>
                    <p>
                        Sesuai regulasi UU PDP, setiap pasien memiliki hak untuk:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li>Mengakses dan meminta konfirmasi mengenai data janji temu yang tercatat di sistem kami.</li>
                        <li>Meminta perbaikan atau pembaruan jika terdapat kekeliruan data identitas/nomor telepon.</li>
                        <li>Meminta penghapusan data janji temu atau berkas rujukan medis setelah masa layanan/garansi berakhir.</li>
                    </ul>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24]">
                        5. Kontak Petugas Pengelola Data
                    </h2>
                    <p>
                        Apabila Anda memiliki pertanyaan, keluhan, atau ingin mengajukan permohonan terkait data pribadi Anda, silakan hubungi kami melalui:
                    </p>
                    <div class="bg-[#F7F7F6] p-4 rounded-xl border border-[#E3E3E1] space-y-1 text-xs">
                        <div><strong>Email:</strong> {{ $settings['company.email'] ?? 'info@sam-orthotic.com' }}</div>
                        <div><strong>WhatsApp:</strong> {{ $settings['company.whatsapp'] ?? '+62 812-3456-7890' }}</div>
                        <div><strong>Alamat Operasional:</strong> {{ $settings['company.address'] ?? 'Indonesia' }}</div>
                    </div>
                </section>
            </div>
        @endif
    </div>
</div>
@endsection
