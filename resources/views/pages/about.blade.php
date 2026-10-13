@extends('layouts.app')

@php
    $seoData = \App\Support\Seo::make($settings)->forAbout();
@endphp

@section('title', $seoData->getTitle())
@section('meta_description', $seoData->getDescription())
@section('canonical_url', $seoData->getCanonical())

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

    {{-- Schema.org JSON-LD Structured Data: Organization, LocalBusiness per Cabang, & BreadcrumbList --}}
    @push('schema')
        @php
            $sd = \App\Support\StructuredData::make($settings);
            $schemas = [
                $sd->organization(),
                $sd->breadcrumbs($seoData->getBreadcrumbs()),
            ];

            if (isset($branches) && $branches->isNotEmpty()) {
                foreach ($branches as $branch) {
                    $schemas[] = $sd->localBusiness($branch);
                }
            }
        @endphp
        {!! $sd->render($schemas) !!}
    @endpush
@endsection
