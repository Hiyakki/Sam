@extends('layouts.app')

@php
    $story = $settings['about.story'] ?? 'SAM Orthotic Prosthetic berdiri sejak tahun 2014 dengan komitmen menghadirkan alat bantu gerak berkualitas tinggi bagi masyarakat.';
    $vision = $settings['about.vision'] ?? 'Menjadi penyedia layanan ortotik dan prostetik terpercaya yang membantu setiap pasien menjalani kehidupan yang lebih aktif dan mandiri.';
    $mission = $settings['about.mission'] ?? [];
    $team = $settings['about.team'] ?? [];
    $siteName = $settings['general.site_name'] ?? 'SAM Orthotic Prosthetic';
@endphp

@section('title', 'Tentang Kami - ' . $siteName)

@section('content')
<div class="bg-gradient-to-b from-[#EBF2E8]/40 to-transparent py-12 sm:py-16 border-b border-[#E3E3E1]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Profil & Dedikasi</span>
            <h1 class="text-3xl sm:text-5xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2 tracking-tight">
                Tentang {{ $siteName }}
            </h1>
            <p class="text-base sm:text-lg text-[#4B5549] mt-4 leading-relaxed">
                Berdiri sejak 2014, kami mendedikasikan keahlian ortotik dan prostetik presisi untuk membantu setiap pasien kembali melangkah mandiri dan percaya diri.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <!-- Cerita & Dedikasi -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-16">
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white rounded-2xl p-8 border border-[#E3E3E1] shadow-xs">
                <h2 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24] mb-4">
                    Perjalanan & Dedikasi Kami
                </h2>
                <div class="text-[#4B5549] leading-relaxed prose max-w-none">
                    {!! $story !!}
                </div>
            </div>

            <!-- Visi & Misi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-[#F7F7F6] rounded-2xl p-6 border border-[#E3E3E1]">
                    <div class="w-10 h-10 rounded-lg bg-[#A5BF98] text-white flex items-center justify-center font-bold mb-4">
                        V
                    </div>
                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24] mb-2">Visi Kami</h3>
                    <p class="text-sm text-[#4B5549] leading-relaxed">
                        {{ $vision }}
                    </p>
                </div>

                <div class="bg-[#F7F7F6] rounded-2xl p-6 border border-[#E3E3E1]">
                    <div class="w-10 h-10 rounded-lg bg-[#869480] text-white flex items-center justify-center font-bold mb-4">
                        M
                    </div>
                    <h3 class="text-xl font-bold font-['Playfair_Display'] text-[#242E24] mb-2">Misi Kami</h3>
                    @if (!empty($mission) && is_array($mission))
                        <ul class="text-sm text-[#4B5549] space-y-2 list-disc pl-5 leading-relaxed">
                            @foreach ($mission as $item)
                                <li>{{ is_array($item) ? ($item['text'] ?? '') : $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-[#4B5549] leading-relaxed">
                            Memberikan pelayanan penuh empati, menerapkan teknik fitting presisi, serta menjaga kualitas material terbaik.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Callout -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-[#EBF2E8]/60 border border-[#A5BF98]/40 rounded-2xl p-6 space-y-4">
                <h3 class="text-lg font-bold font-['Playfair_Display'] text-[#242E24]">
                    Konsultasi & Pengukuran
                </h3>
                <p class="text-sm text-[#4B5549] leading-relaxed">
                    Setiap alat bantu gerak membutuhkan sesi konsultasi dan pengukuran langsung oleh tenaga ahli kami untuk memastikan kenyamanan anatomis Anda.
                </p>
                <a href="{{ route('appointment') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-sm font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-colors shadow-sm">
                    Buat Janji Konsultasi
                </a>
            </div>
        </div>
    </div>

    <!-- Tim Ahli (Bila ada data tim) -->
    @if (!empty($team) && is_array($team) && count($team) > 0)
    <div class="mb-16">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Tenaga Profesional</span>
            <h2 class="text-3xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Tim Ahli Kami
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($team as $member)
                <div class="bg-white rounded-2xl p-6 border border-[#E3E3E1] text-center shadow-xs">
                    @if (!empty($member['photo']))
                        <img src="{{ Storage::disk('public')->url($member['photo']) }}"
                             alt="{{ $member['name'] ?? '' }}"
                             class="w-24 h-24 rounded-full mx-auto object-cover border-2 border-[#A5BF98] mb-4">
                    @else
                        <div class="w-24 h-24 rounded-full bg-[#EBF2E8] text-[#869480] flex items-center justify-center font-bold text-2xl mx-auto mb-4">
                            {{ strtoupper(substr($member['name'] ?? 'T', 0, 1)) }}
                        </div>
                    @endif
                    <h3 class="font-bold text-lg text-[#242E24]">{{ $member['name'] ?? '' }}</h3>
                    <p class="text-xs text-[#869480] mt-1">{{ $member['role'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Daftar Cabang Aktif Lengkap -->
    <div>
        <div class="max-w-3xl mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Jaringan Lokasi</span>
            <h2 class="text-3xl font-bold font-['Playfair_Display'] text-[#242E24] mt-2">
                Cabang & Workshop Kami
            </h2>
            <p class="text-sm text-[#4B5549] mt-2">
                Kunjungi klinik dan workshop kami untuk konsultasi langsung, pengukuran, fitting alat, atau perbaikan.
            </p>
        </div>

        <div class="space-y-8">
            @forelse ($branches as $branch)
                <div class="bg-white rounded-3xl p-8 border border-[#E3E3E1] shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-7 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full bg-[#EBF2E8] text-[#444E41] text-xs font-bold">Cabang Aktif</span>
                            <h3 class="text-2xl font-bold font-['Playfair_Display'] text-[#242E24]">
                                {{ $branch->name }}
                            </h3>
                        </div>

                        <div class="space-y-3 text-sm text-[#4B5549]">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-[#869480] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>{{ $branch->address }}</span>
                            </div>

                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#869480] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span>WhatsApp: </span>
                                <a href="https://wa.me/{{ $branch->phone_wa }}" target="_blank" class="font-semibold text-[#869480] hover:underline">
                                    +{{ $branch->phone_wa }}
                                </a>
                            </div>

                            @if ($branch->email)
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-5 h-5 text-[#869480] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span>{{ $branch->email }}</span>
                                </div>
                            @endif

                            <div class="flex items-start gap-2.5 pt-2 border-t border-[#E3E3E1]">
                                <svg class="w-5 h-5 text-[#869480] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <span class="font-semibold text-[#242E24]">Jam Operasional:</span>
                                    <p class="text-xs text-[#4B5549] mt-0.5">
                                        {{ $branch->operating_hours_summary }}
                                    </p>
                                </div>
                            </div>

                            @if ($branch->services->isNotEmpty())
                                <div class="pt-2">
                                    <span class="text-xs font-semibold text-[#242E24]">Layanan Tersedia di Cabang Ini:</span>
                                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                                        @foreach ($branch->services as $svc)
                                            <span class="px-2.5 py-0.5 rounded-md bg-[#F7F7F6] border border-[#E3E3E1] text-[11px] font-medium text-[#444E41]">
                                                {{ $svc->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 flex flex-wrap gap-3">
                            <a href="{{ route('appointment') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#E8343B] hover:bg-[#D1252C] transition-colors shadow-xs">
                                Buat Janji di Cabang Ini
                            </a>
                            @if ($branch->maps_link)
                                <a href="{{ $branch->maps_link }}" target="_blank" rel="noopener noreferrer" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-[#242E24] bg-[#F7F7F6] border border-[#E3E3E1] hover:bg-white transition-colors">
                                    Buka Google Maps &rarr;
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Embed Peta atau Foto Cabang -->
                    <div class="lg:col-span-5 w-full">
                        @if ($branch->maps_embed_url)
                            <div class="w-full aspect-4/3 rounded-2xl overflow-hidden border border-[#E3E3E1] shadow-2xs">
                                {!! $branch->maps_embed_url !!}
                            </div>
                        @elseif ($branch->photo)
                            <div class="w-full aspect-4/3 rounded-2xl overflow-hidden border border-[#E3E3E1] shadow-2xs">
                                <img src="{{ Storage::disk('public')->url($branch->photo) }}" alt="{{ $branch->name }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-full aspect-4/3 rounded-2xl bg-[#F7F7F6] border border-[#E3E3E1] flex flex-col items-center justify-center p-6 text-center text-[#869480]">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="text-xs font-medium">Peta / Foto Lokasi Cabang</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 border border-[#E3E3E1] text-center text-[#4B5549]">
                    Informasi cabang sedang diperbarui.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
