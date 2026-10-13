@extends('layouts.app')

@php
    $seoData = \App\Support\Seo::make($settings ?? null)->forTestimonials();
@endphp

@section('title', $seoData->getTitle())
@section('meta_description', $seoData->getDescription())
@section('canonical_url', $seoData->getCanonical())

@section('content')
<section class="py-12 md:py-20 bg-surface-soft min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <x-breadcrumb :items="$seoData->getBreadcrumbs()" />
        </div>

        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold font-['Playfair_Display'] text-primary mb-5">Pengalaman Pasien Bersama SAM</h1>
            <p class="text-lg text-text-muted max-w-2xl mx-auto">Kami terus berupaya memberikan solusi mobilitas terbaik. Berikut adalah beberapa testimoni dan penilaian dari pasien yang telah mempercayakan pembuatan alat bantu gerak mereka kepada kami.</p>
            
            @php
                $avgRating = $testimonials->avg('rating') ?? 0;
                $totalReviews = $testimonials->total();
            @endphp
            
            @if($totalReviews > 0)
                <x-rating-summary :average="$avgRating" :total="$totalReviews" />
            @endif
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="max-w-3xl mx-auto bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl mb-12 flex items-start gap-4 shadow-sm">
            <svg class="w-6 h-6 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="text-base font-bold text-green-900 mb-1">Berhasil Terkirim</h4>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif
        
        <!-- Error validation message for form (if failed to open modal automatically) -->
        @if($errors->any())
        <div class="max-w-3xl mx-auto bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl mb-12 flex items-start gap-4 shadow-sm">
            <svg class="w-6 h-6 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h4 class="text-base font-bold text-red-900 mb-1">Gagal Mengirim Ulasan</h4>
                <p class="text-sm font-medium text-red-800">Mohon cek kembali form Anda (klik "Tulis Ulasan Anda" untuk memperbaiki isian yang salah).</p>
            </div>
        </div>
        @endif

        <!-- List Testimoni -->
        @if($testimonials->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 items-start">
                @foreach($testimonials as $testimonial)
                    <x-testimonial-card :testimonial="$testimonial" />
                @endforeach
            </div>

            <div class="mt-12 flex justify-center">
                {{ $testimonials->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 px-4 bg-white rounded-3xl border border-gray-100 shadow-sm max-w-3xl mx-auto">
                <div class="w-24 h-24 bg-primary/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2 font-['Playfair_Display']">Belum Ada Ulasan</h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">Jadilah yang pertama membagikan pengalaman Anda menggunakan layanan dari SAM Orthotic Prosthetic.</p>
                <x-testimonial-form />
            </div>
        @endif

        <!-- Bottom CTA -->
        @if($testimonials->count() > 0)
            <div class="mt-20 text-center">
                <div class="inline-block bg-white p-8 rounded-3xl shadow-sm border border-gray-100 max-w-2xl w-full">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 font-['Playfair_Display']">Punya Pengalaman dengan SAM?</h3>
                    <p class="text-gray-500 mb-6">Bagikan cerita Anda untuk membantu pasien lain menemukan solusi terbaik bagi mobilitas mereka.</p>
                    <x-testimonial-form />
                </div>
            </div>
        @endif

    </div>
</section>

@push('schema')
    @php
        $sd = \App\Support\StructuredData::make($settings ?? null);
        $schemas = [
            $sd->breadcrumbs($seoData->getBreadcrumbs()),
        ];
    @endphp
    {!! $sd->render($schemas) !!}
@endpush
@endsection
