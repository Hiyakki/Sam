@props([
    'badge' => null,
    'title' => '',
    'subtitle' => null,
    'align' => 'center', // 'center', 'left'
    'theme' => 'light',  // 'light', 'dark'
    'class' => '',
])

@php
    $alignClass = $align === 'center' ? 'text-center max-w-3xl mx-auto' : 'text-left max-w-2xl';
    $titleColor = $theme === 'dark' ? 'text-white' : 'text-primary';
    $subtitleColor = $theme === 'dark' ? 'text-white/80' : 'text-text-muted';
@endphp

<div class="{{ $alignClass }} mb-12 sm:mb-16 {{ $class }}">
    @if ($badge)
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full {{ $theme === 'dark' ? 'bg-white/10 text-white border border-white/20' : 'bg-surface-soft border border-primary/15 text-primary' }} text-xs font-semibold tracking-wide uppercase mb-3">
            <span class="w-2 h-2 rounded-full {{ $theme === 'dark' ? 'bg-accent' : 'bg-accent' }} animate-pulse"></span>
            <span>{{ $badge }}</span>
        </div>
    @endif

    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] {{ $titleColor }} tracking-tight leading-[1.2]">
        {{ $title }}
    </h2>

    @if ($subtitle)
        <p class="text-base sm:text-lg {{ $subtitleColor }} mt-3 sm:mt-4 leading-relaxed font-normal">
            {{ $subtitle }}
        </p>
    @endif
</div>
