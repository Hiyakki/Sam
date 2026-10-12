@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa')
@section('code', '419')
@section('heading', 'Sesi Halaman Berakhir')
@section('badge_bg', 'bg-amber-50 text-amber-700')
@section('icon')
    <svg class="w-10 h-10 stroke-current text-amber-600" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
@endsection
@section('message', 'Sesi keamanan halaman telah kedaluwarsa demi melindungi data Anda. Silakan muat ulang halaman atau kembali ke Beranda.')
