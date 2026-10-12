@extends('errors.layout')

@section('title', '500 - Kendala Server')
@section('code', '500')
@section('heading', 'Terjadi Kendala Internal Server')
@section('badge_bg', 'bg-red-50 text-red-700')
@section('icon')
    <svg class="w-10 h-10 stroke-current text-[#E8343B]" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
    </svg>
@endsection
@section('message', 'Terjadi kendala teknis internal pada server kami. Tim teknis SAM segera menangani masalah ini. Untuk kebutuhan mendesak, silakan hubungi tim kami via WhatsApp.')
