@extends('layouts.app')

@section('title', 'Tentang Kami - ' . ($settings['general.site_name'] ?? 'SAM Orthotic Prosthetic'))

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
@endsection
