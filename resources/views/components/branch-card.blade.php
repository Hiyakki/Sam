@props([
    'branch',
    'layout' => 'card', // 'card' or 'detailed' (2-kolom info + peta)
    'class' => '',
])

@php
    $cleanWa = preg_replace('/[^0-9]/', '', (string) $branch->phone_wa);
    $waUrl = "https://wa.me/{$cleanWa}?text=" . rawurlencode("Halo {$branch->name}, saya ingin bertanya mengenai layanan dan jadwal kunjungan.");
    $days = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];
@endphp

@if ($layout === 'detailed')
    <div class="bg-white rounded-3xl border border-gray-200/90 overflow-hidden shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-0 {{ $class }}">
        <!-- Left: Information (7 cols) -->
        <div class="lg:col-span-7 p-8 sm:p-10 flex flex-col justify-between">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-soft text-primary text-xs font-semibold tracking-wide uppercase mb-3">
                    <span class="w-2 h-2 rounded-full bg-accent"></span>
                    <span>Kantor & Workshop</span>
                </div>

                <h3 class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-primary mb-3">
                    <a href="{{ route('branch.show', $branch->slug) }}" class="hover:text-accent transition-colors">
                        {{ $branch->name }}
                    </a>
                </h3>

                <!-- Address -->
                <div class="flex items-start gap-3 text-text-muted text-sm sm:text-base leading-relaxed mb-6">
                    <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ $branch->address }}</span>
                </div>

                <!-- Operating Schedules -->
                @if ($branch->schedules && $branch->schedules->isNotEmpty())
                    <div class="bg-surface-soft/70 rounded-2xl p-5 mb-6 border border-gray-200/60">
                        <div class="text-xs font-bold uppercase tracking-wider text-primary mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Jadwal Operasional</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            @foreach ($branch->schedules as $sched)
                                <div class="flex items-center justify-between py-1 border-b border-gray-200/40 last:border-0">
                                    <span class="font-medium text-text">{{ $days[$sched->day_of_week] ?? 'Hari ' . $sched->day_of_week }}</span>
                                    @if ($sched->is_open)
                                        <span class="text-primary font-semibold">{{ substr((string) $sched->open_time, 0, 5) }} - {{ substr((string) $sched->close_time, 0, 5) }} WIB</span>
                                    @else
                                        <span class="text-red-500 font-medium">Tutup</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('branch.show', $branch->slug) }}"
                   class="inline-flex items-center justify-center px-5 py-3 rounded-xl text-sm font-semibold text-white bg-primary hover:bg-primary-dark transition-all shadow-xs">
                    Detail & Jadwal Cabang
                </a>

                <a href="{{ $waUrl }}"
                   target="_blank"
                   rel="noopener noreferrer nofollow"
                   class="inline-flex items-center justify-center px-5 py-3 rounded-xl text-sm font-semibold text-white bg-[#128C7E] hover:bg-[#075E54] shadow-xs hover:shadow-md transition-all">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/>
                    </svg>
                    WhatsApp Cabang
                </a>

                @if ($branch->maps_link)
                    <a href="{{ $branch->maps_link }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-xl text-sm font-semibold text-primary bg-surface-soft hover:bg-surface-soft/80 border border-gray-200/80 transition-all">
                        <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Google Maps
                    </a>
                @endif
            </div>
        </div>

        <!-- Right: Map Embed (5 cols) -->
        <div class="lg:col-span-5 bg-surface-soft relative min-h-[320px] lg:min-h-full">
            @if ($branch->maps_embed_url)
                <iframe src="{{ $branch->maps_embed_url }}"
                        title="Peta Lokasi {{ $branch->name }}"
                        width="600"
                        height="400"
                        class="w-full h-full min-h-[320px] lg:min-h-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
            @else
                <div class="w-full h-full min-h-[320px] flex flex-col items-center justify-center p-8 text-center text-text-muted">
                    <svg class="w-12 h-12 text-primary/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <p class="text-sm font-medium">{{ $branch->name }}</p>
                    <p class="text-xs text-text-muted mt-1">{{ $branch->address }}</p>
                </div>
            @endif
        </div>
    </div>
@else
    <!-- Standard Grid Card -->
    <div class="bg-white rounded-3xl border border-gray-200/90 p-7 sm:p-8 flex flex-col justify-between shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-primary/40 {{ $class }}">
        <div>
            <div class="w-12 h-12 rounded-2xl bg-surface-soft text-primary flex items-center justify-center mb-5 border border-primary/10 shadow-2xs">
                <svg class="w-6 h-6 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>

            <h3 class="text-xl font-bold font-['Playfair_Display'] text-primary mb-2">
                <a href="{{ route('branch.show', $branch->slug) }}" class="hover:text-accent transition-colors">
                    {{ $branch->name }}
                </a>
            </h3>

            <p class="text-xs sm:text-sm text-text-muted leading-relaxed mb-5">
                {{ $branch->address }}
            </p>

            @if ($branch->schedules && $branch->schedules->isNotEmpty())
                <div class="py-3 px-4 rounded-xl bg-surface-soft/80 border border-gray-200/60 mb-6 text-xs text-text-muted space-y-1">
                    <div class="font-bold text-primary flex items-center gap-1.5 mb-1">
                        <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Jam Buka</span>
                    </div>
                    @php
                        $openSchedules = $branch->schedules->where('is_open', true);
                        $firstOpen = $openSchedules->first();
                    @endphp
                    @if ($firstOpen)
                        <p class="text-xs">Senin - Sabtu: {{ substr((string) $firstOpen->open_time, 0, 5) }} - {{ substr((string) $firstOpen->close_time, 0, 5) }} WIB</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
            <a href="{{ route('branch.show', $branch->slug) }}"
               class="text-xs font-semibold text-primary hover:text-accent inline-flex items-center gap-1">
                <span>Lihat Profil</span>
                <span>&rarr;</span>
            </a>

            <a href="{{ $waUrl }}"
               target="_blank"
               rel="noopener noreferrer nofollow"
               class="text-xs font-semibold text-[#128C7E] hover:underline inline-flex items-center gap-1">
                <span>WA Cabang</span>
                <span>&nearr;</span>
            </a>
        </div>
    </div>
@endif
