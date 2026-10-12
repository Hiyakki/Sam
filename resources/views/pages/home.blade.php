@extends('layouts.app')

@section('title', ($settings['general.site_name'] ?? 'SAM Orthotic Prosthetic') . ' - Solusi Kaki Palsu & Penyangga Tubuh Terpercaya')

@section('content')
    {{-- 1. Hero Section (2-col desktop, 1-col mobile, factual trust metrics) --}}
    @include('pages.home._hero')

    {{-- 2. Strip Kategori (Orthosis, Prosthesis dengan hitungan akurat) --}}
    @include('pages.home._categories')

    {{-- 3. Layanan & Alur 3 Tahap (Konsultasi -> Pengukuran/Fitting -> Perawatan/Perbaikan) --}}
    @include('pages.home._services')

    {{-- 4. Produk Pilihan (Carousel/Grid produk aktif dengan foto WebP) --}}
    @include('pages.home._featured_products')

    {{-- 5. Kenapa SAM (4 kartu keunggulan dari site_settings) --}}
    @include('pages.home._advantages')

    {{-- 6. Galeri Suasana (Collage foto asli workshop & pelayanan) --}}
    @include('pages.home._gallery')

    {{-- 7. Testimoni Pasien (Hanya aktif dan consent confirmed) --}}
    @include('pages.home._testimonials')

    {{-- 8. FAQ Ringkas (Accordion FAQ on home) --}}
    @include('pages.home._faq')

    {{-- 9. Lokasi & Cabang (Layout detail 2-kolom atau kartu responsive) --}}
    @include('pages.home._branches')

    {{-- 10. CTA Penutup (Section navy penuh dengan tombol aksi) --}}
    @include('pages.home._cta')

    {{-- Schema.org JSON-LD Structured Data: MedicalBusiness & LocalBusiness per Cabang --}}
    @php
        $graph = [
            [
                '@'.'type' => 'MedicalBusiness',
                '@'.'id' => url('/') . '#organization',
                'name' => $settings['general.site_name'] ?? 'SAM Orthotic Prosthetic',
                'url' => url('/'),
                'logo' => asset('images/logo-sam.png'),
                'description' => 'Layanan klinis dan fabrikasi alat bantu gerak tubuh ortotik prostetik kustom presisi tinggi sejak 2014.',
                'telephone' => $settings['company.whatsapp'] ?? '+6281234567890',
                'email' => $settings['company.email'] ?? 'info@sam-orthotic.com',
                'address' => [
                    '@'.'type' => 'PostalAddress',
                    'streetAddress' => $settings['company.address'] ?? 'Indonesia',
                    'addressCountry' => 'ID',
                ],
                'medicalSpecialty' => ['Orthotics', 'Prosthetics'],
            ],
        ];

        if (isset($branches) && $branches->isNotEmpty()) {
            foreach ($branches as $branch) {
                $branchItem = [
                    '@'.'type' => 'LocalBusiness',
                    '@'.'id' => url('/') . '#branch-' . $branch->slug,
                    'name' => ($settings['general.site_name'] ?? 'SAM Orthotic Prosthetic') . ' - Cabang ' . $branch->name,
                    'url' => url('/'),
                    'telephone' => $branch->phone_wa ?: ($settings['company.whatsapp'] ?? '+6281234567890'),
                    'address' => [
                        '@'.'type' => 'PostalAddress',
                        'streetAddress' => $branch->address,
                        'addressCountry' => 'ID',
                    ],
                ];
                if ($branch->latitude && $branch->longitude) {
                    $branchItem['geo'] = [
                        '@'.'type' => 'GeoCoordinates',
                        'latitude' => (float) $branch->latitude,
                        'longitude' => (float) $branch->longitude,
                    ];
                }
                $graph[] = $branchItem;
            }
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode([
        '@'.'context' => 'https://schema.org',
        '@'.'graph' => $graph,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
