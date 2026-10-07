@props([
    'number' => '',
    'label' => '',
    'suffix' => '',
    'theme' => 'light', // 'light', 'dark'
    'class' => '',
])

@php
    $numberColor = $theme === 'dark' ? 'text-white' : 'text-primary';
    $labelColor = $theme === 'dark' ? 'text-white/80' : 'text-text-muted';
@endphp

<div class="text-center sm:text-left {{ $class }}">
    <div class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] {{ $numberColor }} tracking-tight flex items-baseline justify-center sm:justify-start gap-1">
        <span>{{ $number }}</span>
        @if ($suffix)
            <span class="text-xl sm:text-2xl font-semibold text-accent">{{ $suffix }}</span>
        @endif
    </div>
    <div class="text-xs sm:text-sm {{ $labelColor }} font-medium mt-1">
        {{ $label }}
    </div>
</div>
