@extends('layouts.app')

@section('title', $seoData->getTitle())
@section('meta_description', $seoData->getDescription())
@section('canonical_url', $seoData->getCanonical())

@section('content')
<!-- Header Banner -->
<div class="bg-gradient-to-b from-surface-soft via-background to-background py-10 sm:py-14 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <x-breadcrumb :items="$seoData->getBreadcrumbs()" />
        </div>

        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-primary bg-white px-3 py-1 rounded-full border border-gray-200 inline-block shadow-2xs">
                Klinik & Lokasi Pelayanan
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] text-primary mt-3 tracking-tight">
                SAM Orthotic Prosthetic Cabang {{ $branch->name }}
            </h1>
            <p class="text-sm sm:text-base text-text-muted mt-3 leading-relaxed">
                Fasilitas pelayanan konsultasi, pengukuran anatomi, dan fitting langsung alat bantu gerak ortotik dan prostetik kustom di wilayah {{ $branch->name }}.
            </p>
        </div>
    </div>
</div>

<!-- Main Details -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        
        <!-- Left: Detail NAP & Jam Buka (7 cols) -->
        <div class="lg:col-span-7 space-y-8">
            <!-- Informasi Kontak & Alamat (NAP) -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-xs space-y-6">
                <h2 class="text-xl font-bold font-['Playfair_Display'] text-primary">
                    Informasi Cabang & Kontak
                </h2>

                <div class="space-y-4 text-sm text-text-muted">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-surface-soft text-primary flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-primary block">Alamat Lengkap:</span>
                            <span class="leading-relaxed">{{ $branch->address }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-surface-soft text-primary flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-primary block">Telepon / WhatsApp:</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branch->phone_wa) }}?text={{ rawurlencode('Halo SAM Cabang ' . $branch->name . ', saya ingin konsultasi.') }}"
                               target="_blank"
                               rel="noopener noreferrer nofollow"
                               class="text-primary hover:text-accent font-semibold transition-colors">
                                {{ $branch->phone_wa }}
                            </a>
                        </div>
                    </div>

                    @if($branch->email)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-surface-soft text-primary flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-primary block">Email Layanan:</span>
                                <a href="mailto:{{ $branch->email }}" class="text-primary hover:text-accent transition-colors">
                                    {{ $branch->email }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="pt-4 flex flex-wrap gap-3">
                    <a href="{{ route('appointment', ['branch' => $branch->id]) }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-accent hover:bg-accent-hover transition-colors shadow-sm">
                        Buat Janji di Cabang Ini
                    </a>
                    @if($branch->maps_link)
                        <a href="{{ $branch->maps_link }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-primary bg-surface-soft hover:bg-gray-200 transition-colors border border-gray-200">
                            Petunjuk Arah (Google Maps) &rarr;
                        </a>
                    @endif
                </div>
            </div>

            <!-- Jam Operasional -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-xs space-y-4">
                <h2 class="text-xl font-bold font-['Playfair_Display'] text-primary">
                    Jadwal Operasional Klinik
                </h2>
                <p class="text-xs text-text-muted">
                    {{ $branch->operating_hours_summary }}
                </p>

                <div class="divide-y divide-gray-100 text-sm">
                    @php
                        $dayNames = [
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            7 => 'Minggu',
                        ];
                    @endphp
                    @foreach($branch->schedules as $sched)
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="font-medium text-primary">{{ $dayNames[$sched->day_of_week] ?? 'Hari' }}</span>
                            @if($sched->is_open)
                                <span class="text-text-muted font-mono text-xs">
                                    {{ substr((string)$sched->open_time, 0, 5) }} - {{ substr((string)$sched->close_time, 0, 5) }} WIB
                                </span>
                            @else
                                <span class="text-rose-600 font-semibold text-xs bg-rose-50 px-2 py-0.5 rounded-md">
                                    Tutup
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Layanan Tersedia -->
            @if($branch->services->isNotEmpty())
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-xs space-y-4">
                    <h2 class="text-xl font-bold font-['Playfair_Display'] text-primary">
                        Layanan di Cabang {{ $branch->name }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($branch->services as $service)
                            <div class="p-3.5 rounded-2xl bg-surface-soft border border-gray-200 flex items-start gap-3">
                                <span class="w-2 h-2 rounded-full bg-accent mt-1.5 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-xs text-primary block">{{ $service->name }}</span>
                                    @if($service->description)
                                        <span class="text-[11px] text-text-muted line-clamp-2 mt-0.5">{{ $service->description }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Peta Lokasi & Foto (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            @if($branch->maps_embed_url)
                <div class="bg-white rounded-3xl p-4 border border-gray-200 shadow-xs space-y-3">
                    <h2 class="text-base font-bold text-primary font-['Playfair_Display'] px-2">
                        Peta Lokasi Google Maps
                    </h2>
                    <div class="relative w-full aspect-4/3 rounded-2xl overflow-hidden border border-gray-200 bg-surface-soft">
                        <iframe src="{{ $branch->maps_embed_url }}"
                                width="600"
                                height="450"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Peta Lokasi SAM Cabang {{ $branch->name }}"
                                class="w-full h-full object-cover">
                        </iframe>
                    </div>
                </div>
            @endif

            @if($branch->photo)
                <div class="bg-white rounded-3xl p-4 border border-gray-200 shadow-xs space-y-3">
                    <h2 class="text-base font-bold text-primary font-['Playfair_Display'] px-2">
                        Gedung Klinik & Workshop
                    </h2>
                    <div class="relative aspect-4/3 rounded-2xl overflow-hidden border border-gray-200 bg-surface-soft">
                        <img src="{{ Storage::disk('public')->url($branch->photo) }}"
                             alt="Gedung SAM Orthotic Prosthetic Cabang {{ $branch->name }}"
                             width="600"
                             height="450"
                             loading="lazy"
                             class="w-full h-full object-cover">
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

@push('schema')
    {!! (new \App\Support\StructuredData($settings))->render($schemas) !!}
@endpush
@endsection
