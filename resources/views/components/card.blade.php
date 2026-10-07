@props([
    'variant' => 'white', // 'white', 'surface', 'navy'
    'hover' => true,
    'padding' => 'p-6 sm:p-8',
    'rounded' => 'rounded-3xl',
    'class' => '',
])

@php
    $variantClasses = match ($variant) {
        'surface' => 'bg-surface-soft border border-gray-200/80 text-text',
        'navy' => 'bg-primary border border-primary-dark text-white',
        default => 'bg-white border border-gray-200/80 text-text',
    };

    $hoverClasses = $hover ? 'transition-all duration-300 hover:-translate-y-1 hover:shadow-lg' : '';
@endphp

<div {{ $attributes->merge(['class' => "{$rounded} {$variantClasses} {$hoverClasses} {$padding} shadow-xs {$class}"]) }}>
    {{ $slot }}
</div>
