@extends('layouts.app')

@section('title', 'Pertanyaan Umum (FAQ) - SAM Orthotic Prosthetic')
@section('meta_description', 'Temukan jawaban atas pertanyaan seputar pemesanan kaki palsu, brace ortotik, resep dokter, jadwal konsultasi, dan proses fitting di SAM.')

@section('content')
<!-- Header Banner -->
<div class="bg-gradient-to-b from-surface-soft via-background to-background py-12 sm:py-16 border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-bold uppercase tracking-widest text-primary bg-white px-3 py-1 rounded-full border border-gray-200 inline-block shadow-2xs">
            Pusat Informasi & Panduan
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] text-primary mt-3 tracking-tight">
            Pertanyaan yang Sering Diajukan
        </h1>
        <p class="text-sm sm:text-base text-text-muted mt-3 max-w-2xl mx-auto leading-relaxed">
            Dapatkan informasi lengkap mengenai alur konsultasi, pembuatan alat ortotik & prostetik kustom, dokumen rujukan dokter, hingga penyesuaian alat.
        </p>
    </div>
</div>

<!-- Main FAQ Section with Alpine Instant Search & Accordion -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
     x-data="{
        searchQuery: '',
        activeAccordion: null,
        toggle(id) {
            this.activeAccordion = (this.activeAccordion === id) ? null : id;
        },
        matches(question, answer, category) {
            if (!this.searchQuery.trim()) return true;
            const q = this.searchQuery.toLowerCase();
            return question.toLowerCase().includes(q) ||
                   answer.toLowerCase().includes(q) ||
                   category.toLowerCase().includes(q);
        }
     }">

    <!-- Search Input -->
    <div class="relative mb-10">
        <input type="text"
               x-model="searchQuery"
               placeholder="Ketik kata kunci pertanyaan (misal: rujukan dokter, garansi, proses fitting, biaya)..."
               class="w-full text-sm sm:text-base rounded-2xl border border-gray-200 py-4 pl-12 pr-10 focus:outline-hidden focus:border-primary bg-white shadow-xs">
        <svg class="w-5 h-5 text-primary absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <button type="button"
                x-show="searchQuery.length > 0"
                @click="searchQuery = ''"
                class="absolute right-4 top-4 text-xs text-text-muted hover:text-primary p-1 font-bold">
            &times;
        </button>
    </div>

    @if($groupedFaqs->isNotEmpty())
        <div class="space-y-10">
            @foreach($groupedFaqs as $categoryName => $faqList)
                <section class="space-y-4"
                         x-data="{ hasVisibleItems: true }">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-accent"></span>
                        <h2 class="text-xl font-bold font-['Playfair_Display'] text-primary">
                            {{ $categoryName }}
                        </h2>
                    </div>

                    <div class="space-y-3">
                        @foreach($faqList as $faq)
                            <div class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs transition-colors hover:border-primary/40"
                                 x-show="matches('{{ addslashes($faq->question) }}', '{{ addslashes(strip_tags($faq->answer)) }}', '{{ addslashes($categoryName) }}')"
                                 x-transition>
                                <button type="button"
                                        @click="toggle({{ $faq->id }})"
                                        class="w-full flex items-center justify-between text-left focus:outline-hidden gap-4">
                                    <span class="font-semibold text-base sm:text-lg text-primary">
                                        {{ $faq->question }}
                                    </span>
                                    <span class="w-8 h-8 rounded-full bg-surface-soft text-primary flex items-center justify-center shrink-0 text-base font-bold transition-transform"
                                          :class="activeAccordion === {{ $faq->id }} ? 'rotate-180 bg-primary text-white' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="activeAccordion === {{ $faq->id }}"
                                     x-cloak
                                     class="mt-4 pt-4 border-t border-gray-200 text-sm text-text-muted leading-relaxed prose prose-sm max-w-none prose-a:text-accent prose-a:font-semibold hover:prose-a:underline">
                                    {!! $faq->answer !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <!-- Empty State Default -->
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-200 shadow-xs">
            <h3 class="font-bold text-lg text-primary">Belum Ada FAQ Tersedia</h3>
            <p class="text-xs text-text-muted mt-2">Daftar pertanyaan umum sedang disiapkan oleh tim medis kami.</p>
        </div>
    @endif

    <!-- Help Banner (Navy Background) -->
    @php
        $waNumber = preg_replace('/[^0-9]/', '', $settings['company.whatsapp'] ?? '6281234567890');
    @endphp
    <div class="mt-14 bg-primary text-white border border-primary-dark rounded-3xl p-8 sm:p-10 text-center space-y-4 shadow-lg">
        <div class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center mx-auto shadow-xs">
            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        </div>
        <h3 class="font-bold text-xl font-['Playfair_Display'] text-white">
            Pertanyaan Anda Belum Terjawab?
        </h3>
        <p class="text-sm text-white/80 max-w-md mx-auto leading-relaxed">
            Klinisi ortotis prostetis kami siap berdiskusi secara langsung untuk menganalisis kebutuhan alat tubuh Anda.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo SAM Orthotic Prosthetic, saya memiliki pertanyaan seputar konsultasi dan alat.') }}"
               target="_blank"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                </svg>
                <span>Tanya via WhatsApp</span>
            </a>
            <a href="{{ route('appointment') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold text-white bg-accent hover:bg-accent-hover transition-colors shadow-xs">
                <span>Buat Janji Konsultasi</span>
            </a>
        </div>
    </div>
</div>

{{-- Schema.org JSON-LD FAQPage --}}
@php
    $faqEntities = [];
    if (isset($groupedFaqs)) {
        foreach ($groupedFaqs as $cat => $faqs) {
            foreach ($faqs as $item) {
                $faqEntities[] = [
                    '@'.'type' => 'Question',
                    'name' => $item->question,
                    'acceptedAnswer' => [
                        '@'.'type' => 'Answer',
                        'text' => strip_tags($item->answer),
                    ],
                ];
            }
        }
    }
@endphp
@if(count($faqEntities) > 0)
    <script type="application/ld+json">
    {!! json_encode([
        '@'.'context' => 'https://schema.org',
        '@'.'type' => 'FAQPage',
        'mainEntity' => $faqEntities,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
@endsection
