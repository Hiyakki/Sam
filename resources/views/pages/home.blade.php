@extends('layouts.app')

@php
    $seoData = \App\Support\Seo::make($settings)->forHome();
@endphp

@section('title', $seoData->getTitle())
@section('meta_description', $seoData->getDescription())
@section('canonical_url', $seoData->getCanonical())

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
    @push('schema')
        @php
            $sd = \App\Support\StructuredData::make($settings);
            $schemas = [
                $sd->organization(),
                $sd->webSite(),
            ];
            if (isset($branches) && $branches->isNotEmpty()) {
                foreach ($branches as $branch) {
                    $schemas[] = $sd->localBusiness($branch);
                }
            }
            if (isset($faqs) && $faqs->isNotEmpty()) {
                $schemas[] = $sd->faqPage($faqs);
            }
        @endphp
        {!! $sd->render($schemas) !!}
    @endpush
@endsection
