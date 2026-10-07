@if ($testimonials->isNotEmpty())
<x-section bg="surface" id="testimoni">
    <x-section-heading
        badge="Pengalaman Pasien"
        title="Apa Kata Mereka yang Menggunakan Alat SAM?"
        subtitle="Kenyamanan dan kepuasan pasien saat kembali beraktivitas mandiri adalah tujuan utama kami."
    />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($testimonials as $t)
            <x-testimonial-card :testimonial="$t" />
        @endforeach
    </div>
</x-section>
@endif
