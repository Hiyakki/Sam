@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('code', '404')
@section('heading', 'Halaman Tidak Ditemukan')
@section('badge_bg', 'bg-slate-100 text-[#1E2A4A]')
@section('icon')
    <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
@endsection
@section('message', 'Halaman yang Anda tuju tidak tersedia, telah dipindahkan, atau tautan yang Anda gunakan tidak valid. Silakan kembali ke Beranda atau hubungi admin kami.')
