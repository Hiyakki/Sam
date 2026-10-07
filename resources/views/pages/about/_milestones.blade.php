@if (!empty($milestones) && is_array($milestones))
<x-section bg="white" id="perjalanan">
    <x-section-heading
        badge="Jejak Langkah"
        title="Tonggak Perjalanan SAM"
        subtitle="Melihat perkembangan dan dedikasi kami sejak awal mula berdiri hingga saat ini."
    />

    <div class="max-w-4xl mx-auto relative">
        <!-- Vertical timeline line on desktop/mobile -->
        <div class="absolute top-4 bottom-4 left-6 sm:left-1/2 w-0.5 bg-primary/20 -translate-x-1/2"></div>

        <div class="space-y-10 sm:space-y-12">
            @foreach ($milestones as $idx => $stone)
                @php
                    $isEven = $idx % 2 === 0;
                @endphp
                <div class="relative flex flex-col sm:flex-row items-start {{ $isEven ? 'sm:flex-row-reverse' : '' }} group">
                    <!-- Center Timeline Dot -->
                    <div class="absolute left-6 sm:left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-white border-4 border-accent flex items-center justify-center z-10 shadow-sm mt-1 sm:mt-4">
                        <div class="w-2 h-2 rounded-full bg-accent"></div>
                    </div>

                    <!-- Content Card (5 cols equivalent) -->
                    <div class="ml-14 sm:ml-0 sm:w-1/2 {{ $isEven ? 'sm:pl-10' : 'sm:pr-10' }} w-full">
                        <div class="bg-surface-soft/70 group-hover:bg-surface-soft border border-gray-200/90 group-hover:border-primary/40 rounded-3xl p-6 sm:p-7 shadow-xs hover:shadow-md transition-all duration-300">
                            <span class="inline-block px-3 py-1 rounded-full bg-primary text-white text-xs font-bold font-['Playfair_Display'] tracking-wider mb-2.5">
                                {{ $stone['year'] ?? '' }}
                            </span>

                            <h3 class="text-xl font-bold font-['Playfair_Display'] text-primary mb-2">
                                {{ $stone['title'] ?? '' }}
                            </h3>

                            <p class="text-xs sm:text-sm text-text-muted leading-relaxed">
                                {{ $stone['desc'] ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-section>
@endif
