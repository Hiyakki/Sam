@props(['items' => []])

@if(!empty($items))
<nav aria-label="Breadcrumb" class="py-3 px-4 sm:px-0">
    <ol class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-text-muted">
        @foreach($items as $index => $item)
            @if(!$loop->first)
                <li aria-hidden="true" class="text-gray-400 select-none">
                    <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
            @endif

            @if(!empty($item['url']) && !$loop->last)
                <li>
                    <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors hover:underline">
                        {{ $item['name'] }}
                    </a>
                </li>
            @else
                <li aria-current="page" class="text-primary font-semibold truncate max-w-xs sm:max-w-md">
                    {{ $item['name'] }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
