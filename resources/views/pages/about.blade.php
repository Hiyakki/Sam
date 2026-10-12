@extends('layouts.app')

@section('title', 'Tentang Kami - ' . ($settings['general.site_name'] ?? 'SAM Orthotic Prosthetic'))
@section('meta_description', 'Profil dan sejarah SAM Orthotic Prosthetic sejak 2014. Fasilitas workshop mandiri, tim profesional bersertifikat, dan kantor cabang di Surakarta, Purwokerto, dan Tegal.')

@section('content')
    {{-- 1. Header Banner Navy --}}
    @include('pages.about._header')

    {{-- 2. Cerita SAM & Kolase Foto --}}
    @include('pages.about._story')

    {{-- 3. Angka & Fakta Terverifikasi --}}
    @include('pages.about._stats')

    {{-- 4. Visi & Misi --}}
    @include('pages.about._vision_mission')

    {{-- 5. Nilai Layanan --}}
    @include('pages.about._values')

    {{-- 6. Tonggak Perjalanan (Milestones Timeline) --}}
    @include('pages.about._milestones')

    {{-- 7. Tim (Sembunyi jika kosong) --}}
    @include('pages.about._team')

    {{-- 8. Cabang & Peta Embed --}}
    @include('pages.about._branches')

    {{-- 9. CTA Penutup --}}
    @include('pages.home._cta')

    {{-- Schema.org JSON-LD LocalBusiness per Cabang --}}
    @if(isset($branches) && $branches->isNotEmpty())
        @php
            $branchList = [];
            foreach ($branches as $branch) {
                $bData = [
                    '@'.'type' => 'LocalBusiness',
                    'name' => ($settings['general.site_name'] ?? 'SAM Orthotic Prosthetic') . ' - Cabang ' . $branch->name,
                    'url' => url('/about'),
                    'telephone' => $branch->phone_wa ?: ($settings['company.whatsapp'] ?? '+6281234567890'),
                    'address' => [
                        '@'.'type' => 'PostalAddress',
                        'streetAddress' => $branch->address,
                        'addressCountry' => 'ID',
                    ],
                ];
                if ($branch->latitude && $branch->longitude) {
                    $bData['geo'] = [
                        '@'.'type' => 'GeoCoordinates',
                        'latitude' => (float) $branch->latitude,
                        'longitude' => (float) $branch->longitude,
                    ];
                }
                $branchList[] = $bData;
            }
        @endphp
        <script type="application/ld+json">
        {!! json_encode([
            '@'.'context' => 'https://schema.org',
            '@'.'graph' => $branchList,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif
@endsection
