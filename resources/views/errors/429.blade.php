@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan')
@section('code', '429')
@section('heading', 'Terlalu Banyak Permintaan')
@section('badge_bg', 'bg-orange-50 text-orange-700')
@section('icon')
    <svg class="w-10 h-10 stroke-current text-orange-600" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>
@endsection
@section('message', 'Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat dari perangkat Anda. Mohon tunggu beberapa saat sebelum mencoba kembali.')
