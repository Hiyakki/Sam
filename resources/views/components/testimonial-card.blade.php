@props([
    'testimonial',
    'class' => '',
])

<div class="bg-white rounded-3xl border border-gray-200/90 p-8 flex flex-col justify-between shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg relative overflow-hidden {{ $class }}">
    <!-- Decorative Quote Icon in background -->
    <div class="absolute top-4 right-5 text-surface-soft font-serif text-7xl select-none pointer-events-none leading-none -z-0">
        &ldquo;
    </div>

    <div class="relative z-10">
        <!-- Rating Stars -->
        @if ($testimonial->rating)
            <div class="flex items-center gap-1 text-accent mb-4">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
        @endif

        <blockquote class="text-text-muted text-sm sm:text-base leading-relaxed italic mb-6">
            &ldquo;{{ $testimonial->content }}&rdquo;
        </blockquote>
    </div>

    <!-- Patient / Author Info -->
    <div class="pt-5 border-t border-gray-100 flex items-center gap-3.5 relative z-10">
        @if ($testimonial->photo)
            <img src="{{ Storage::disk('public')->url($testimonial->photo) }}"
                 alt="{{ $testimonial->name }}"
                 width="48"
                 height="48"
                 loading="lazy"
                 class="w-12 h-12 rounded-full object-cover border border-gray-200 shrink-0">
        @else
            <div class="w-12 h-12 rounded-full bg-surface-soft text-primary font-bold text-base flex items-center justify-center shrink-0 border border-primary/10 shadow-2xs">
                {{ strtoupper(substr($testimonial->name, 0, 1)) }}
            </div>
        @endif

        <div class="overflow-hidden">
            <h4 class="font-bold text-sm sm:text-base text-primary truncate">
                {{ $testimonial->name }}
            </h4>

            @if ($testimonial->role_or_info)
                <p class="text-xs text-text-muted truncate">
                    {{ $testimonial->role_or_info }}
                </p>
            @endif

            @if ($testimonial->product)
                <span class="inline-block mt-0.5 text-[11px] font-semibold text-primary/80 bg-surface-soft px-2 py-0.5 rounded-md truncate max-w-full">
                    Alat: {{ $testimonial->product->name }}
                </span>
            @endif
        </div>
    </div>
</div>
