@props([
    'variant' => 'accent', // 'accent' (merah logo), 'primary' (navy), 'outline', 'outline-white', 'ghost'
    'href' => null,
    'size' => 'md',        // 'sm', 'md', 'lg'
    'class' => '',
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'px-4 py-2 text-xs font-semibold rounded-xl',
        'lg' => 'px-8 py-4 text-base font-semibold rounded-2xl',
        default => 'px-6 py-3 sm:px-7 sm:py-3.5 text-sm sm:text-base font-semibold rounded-xl',
    };

    $variantClasses = match ($variant) {
        'accent' => 'text-white bg-accent hover:bg-accent-hover shadow-md hover:shadow-lg active:scale-95',
        'primary' => 'text-white bg-primary hover:bg-primary-dark shadow-md hover:shadow-lg active:scale-95',
        'outline' => 'text-primary bg-white border border-gray-300 hover:bg-surface-soft hover:border-primary/40 shadow-2xs active:scale-95',
        'outline-white' => 'text-white bg-white/10 hover:bg-white/20 border border-white/25 active:scale-95',
        'ghost' => 'text-primary hover:bg-surface-soft/60 active:scale-95',
        default => 'text-white bg-accent hover:bg-accent-hover',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center transition-all duration-200 cursor-pointer {$sizeClasses} {$variantClasses} {$class}"]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => "inline-flex items-center justify-center transition-all duration-200 cursor-pointer {$sizeClasses} {$variantClasses} {$class}"]) }}>
        {{ $slot }}
    </button>
@endif
