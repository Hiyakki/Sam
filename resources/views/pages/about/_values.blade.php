@if (!empty($values) && is_array($values))
<x-section bg="surface" id="nilai-layanan">
    <x-section-heading
        badge="Prinsip Layanan"
        title="Nilai yang Kami Pegang Teguh"
        subtitle="Standar etika dan kualitas dalam setiap tahapan pembuatan alat ortotik prostetik."
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($values as $idx => $val)
            <div class="bg-white rounded-3xl p-7 border border-gray-200/90 shadow-xs hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-surface-soft text-primary font-bold text-lg flex items-center justify-center mb-5 border border-primary/10 shadow-2xs">
                    0{{ $idx + 1 }}
                </div>

                <h3 class="text-xl font-bold font-['Playfair_Display'] text-primary mb-2">
                    {{ $val['title'] ?? '' }}
                </h3>

                <p class="text-xs sm:text-sm text-text-muted leading-relaxed">
                    {{ $val['desc'] ?? '' }}
                </p>
            </div>
        @endforeach
    </div>
</x-section>
@endif
