@props([
    'bg' => 'white', // 'white', 'surface', 'background', 'primary', 'none'
    'id' => null,
    'container' => true,
    'containerClass' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
    'overflow' => 'hidden',
    'class' => '',
])

@php
    $bgClasses = match ($bg) {
        'surface' => 'bg-surface-soft border-y border-gray-200/80',
        'background' => 'bg-background border-y border-gray-200/80',
        'primary' => 'bg-primary text-white border-t border-primary-dark',
        'none' => '',
        default => 'bg-white',
    };
@endphp

<section {{ $id ? "id={$id}" : '' }}
         class="py-16 sm:py-20 lg:py-24 relative {{ $overflow === 'hidden' ? 'overflow-hidden' : '' }} {{ $bgClasses }} {{ $class }}">
    @if ($container)
        <div class="{{ $containerClass }} relative z-10">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</section>
