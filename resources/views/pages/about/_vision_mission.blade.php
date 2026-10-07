@php
    $vision = $settings['about.vision'] ?? 'Menjadi penyedia alat orthotic dan prosthetic terpercaya yang membantu setiap pasien menjalani kehidupan yang lebih aktif dan mandiri.';
    $rawMission = $settings['about.mission'] ?? [];
    $mission = [];

    if (is_array($rawMission)) {
        $mission = $rawMission;
    } elseif (is_string($rawMission) && !empty(trim($rawMission))) {
        $decoded = json_decode($rawMission, true);
        if (is_array($decoded)) {
            $mission = $decoded;
        } else {
            // Split by newline if plain text
            $lines = preg_split('/\r\n|\r|\n/', trim($rawMission));
            $mission = array_filter(array_map('trim', $lines));
        }
    }

    if (empty($mission)) {
        $mission = [
            'Menyediakan alat bantu gerak presisi tinggi yang disesuaikan dengan anatomi unik tiap pasien.',
            'Memberikan konsultasi dan pendampingan rehabilitasi secara empati dan profesional.',
            'Menjamin kualitas komponen tahan lama dan layanan purna jual yang berkelanjutan.',
        ];
    }
@endphp

<x-section bg="white" id="visi-misi">
    <x-section-heading
        badge="Arah & Tujuan"
        title="Visi dan Misi Pelayanan Kami"
        subtitle="Landasan kami dalam mendampingi pasien meraih kebebasan gerak mandiri."
    />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch max-w-5xl mx-auto">
        <!-- Visi Card -->
        <x-card variant="surface" :hover="true" class="flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-primary text-white flex items-center justify-center font-bold text-xl mb-6 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold font-['Playfair_Display'] text-primary mb-4">
                    Visi Kami
                </h3>

                <p class="text-text-muted text-base leading-relaxed">
                    {{ $vision }}
                </p>
            </div>

            <div class="pt-6 mt-6 border-t border-gray-200/80">
                <span class="text-xs font-semibold text-primary uppercase tracking-wider">Komitmen Masa Depan</span>
            </div>
        </x-card>

        <!-- Misi Card -->
        <x-card variant="white" :hover="true" class="flex flex-col justify-between border-2 border-primary/20">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-primary-dark text-white flex items-center justify-center font-bold text-xl mb-6 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold font-['Playfair_Display'] text-primary mb-4">
                    Misi Kami
                </h3>

                @if (!empty($mission) && is_array($mission))
                    <ol class="space-y-3.5 text-text-muted text-sm sm:text-base leading-relaxed">
                        @foreach ($mission as $idx => $m)
                            @php
                                $mText = is_array($m) ? ($m['text'] ?? '') : $m;
                            @endphp
                            @if (!empty(trim($mText)))
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-surface-soft text-primary font-bold text-xs flex items-center justify-center shrink-0 mt-0.5 border border-primary/10">
                                        {{ $idx + 1 }}
                                    </span>
                                    <span>{{ $mText }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                @endif
            </div>

            <div class="pt-6 mt-6 border-t border-gray-200/80">
                <span class="text-xs font-semibold text-accent uppercase tracking-wider">Langkah Konkret</span>
            </div>
        </x-card>
    </div>
</x-section>
