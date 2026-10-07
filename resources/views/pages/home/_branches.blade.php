@if ($branches->isNotEmpty())
<x-section bg="surface" id="lokasi-cabang">
    <x-section-heading
        badge="Lokasi & Kontak"
        title="Kunjungi Klinik & Workshop Kami"
        subtitle="Temukan lokasi workshop terdekat untuk konsultasi fisik dan fitting alat secara langsung."
    />

    @if ($branches->count() === 1)
        <!-- Single Branch: 2-column detailed layout (Info + Google Maps) -->
        <div class="max-w-5xl mx-auto">
            <x-branch-card :branch="$branches->first()" layout="detailed" />
        </div>
    @else
        <!-- Multiple Branches: Responsive Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($branches as $branch)
                <x-branch-card :branch="$branch" layout="card" />
            @endforeach
        </div>
    @endif
</x-section>
@endif
