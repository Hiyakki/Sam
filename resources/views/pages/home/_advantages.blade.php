@php
    $advantages = $settings['home.advantages'] ?? [];
    if (is_string($advantages)) {
        $advantages = json_decode($advantages, true) ?? [];
    }
@endphp

@if (!empty($advantages) && is_array($advantages))
<x-section bg="surface" id="keunggulan">
    <!-- Decorative Floating Shapes -->
    <div class="absolute -top-12 -left-12 w-64 h-64 rounded-full bg-primary/5 blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-12 -right-12 w-72 h-72 rounded-full bg-accent/5 blur-3xl pointer-events-none"></div>

    <x-section-heading
        badge="Komitmen Klinis"
        title="Mengapa Memilih SAM Orthotic Prosthetic?"
        subtitle="Dedikasi kami berlandaskan pengalaman nyata, pendekatan individual, dan proses pengerjaan mandiri."
    />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
        @foreach ($advantages as $adv)
            @php
                $icon = $adv['icon'] ?? 'calendar';
            @endphp
            <div class="bg-white rounded-3xl p-7 border border-gray-200/90 shadow-xs hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-surface-soft text-primary flex items-center justify-center mb-5 border border-primary/10 shadow-2xs">
                        @if ($icon === 'calendar')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @elseif ($icon === 'user-check')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        @elseif ($icon === 'wrench')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        @elseif ($icon === 'sparkles')
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        @elseif ($icon === 'users')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        @elseif ($icon === 'heart')
                            <svg class="w-6 h-6 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        @endif
                    </div>

                    <h3 class="font-bold text-lg text-primary font-['Playfair_Display'] mb-2">
                        {{ $adv['title'] ?? '' }}
                    </h3>

                    <p class="text-xs sm:text-sm text-text-muted leading-relaxed">
                        {{ $adv['desc'] ?? '' }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</x-section>
@endif
