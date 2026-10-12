@if ($homeFaqs->isNotEmpty())
<x-section bg="white" id="faq-ringkas">
    <div class="max-w-4xl mx-auto" x-data="{ activeFaq: null }">
        <x-section-heading
            badge="Tanya Jawab"
            title="Pertanyaan yang Sering Diajukan"
            subtitle="Informasi penting mengenai proses konsultasi, pembuatan, dan pemeliharaan alat ortotik prostetik."
        />

        <div class="space-y-4">
            @foreach ($homeFaqs as $faq)
                <div class="bg-surface-soft/80 rounded-2xl border border-gray-200/80 p-6 transition-all duration-200">
                    <button @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})"
                            type="button"
                            class="w-full flex items-center justify-between text-left focus:outline-hidden cursor-pointer">
                        <span class="font-semibold text-base sm:text-lg text-primary pr-4">{{ $faq->question }}</span>
                        <span class="w-8 h-8 rounded-full bg-white text-primary flex items-center justify-center font-bold text-lg shrink-0 border border-gray-200 shadow-2xs"
                              x-text="activeFaq === {{ $faq->id }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === {{ $faq->id }}"
                         x-cloak
                         x-collapse
                         class="mt-4 pt-4 border-t border-gray-200/80 text-sm sm:text-base text-text-muted leading-relaxed prose prose-sm max-w-none prose-a:text-accent prose-a:font-semibold hover:prose-a:underline">
                        {!! $faq->answer !!}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <x-button variant="outline" :href="route('faq')">
                <span>Lihat Semua Pertanyaan & Jawaban (FAQ)</span>
                <span class="ml-2">&rarr;</span>
            </x-button>
        </div>
    </div>
</x-section>
@endif
