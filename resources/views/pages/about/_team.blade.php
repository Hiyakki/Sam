@if (!empty($team) && is_array($team) && count($team) > 0)
<x-section bg="surface" id="tim-ahli">
    <x-section-heading
        badge="Tenaga Medis & Teknis"
        title="Tim Ortotis & Prostetis SAM"
        subtitle="Didukung tenaga yang berdedikasi tinggi dalam perancangan dan evaluasi alat gerak."
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
        @foreach ($team as $member)
            <div class="bg-white rounded-3xl p-7 text-center border border-gray-200/90 shadow-xs hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                <div class="w-28 h-28 mx-auto rounded-full overflow-hidden bg-surface-soft border-4 border-white shadow-md mb-5">
                    @if (!empty($member['photo']))
                        <img src="{{ Storage::disk('public')->url($member['photo']) }}"
                             alt="{{ $member['name'] ?? '' }}"
                             width="120"
                             height="120"
                             loading="lazy"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-primary text-white text-2xl font-bold">
                            {{ strtoupper(substr($member['name'] ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                </div>

                <h3 class="text-xl font-bold font-['Playfair_Display'] text-primary mb-1">
                    {{ $member['name'] ?? '' }}
                </h3>

                <p class="text-xs sm:text-sm text-text-muted font-medium">
                    {{ $member['role'] ?? 'Tenaga Ortotik Prostetik' }}
                </p>
            </div>
        @endforeach
    </div>
</x-section>
@endif
