@props(['testimonial'])

<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
    <!-- Decorative Quote Icon -->
    <div class="absolute top-4 right-4 text-gray-100/60 transform rotate-180">
        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
        </svg>
    </div>

    <div class="relative z-10 flex-1 space-y-4">
        <!-- Rating -->
        @if($testimonial->rating)
            <x-star-rating :rating="$testimonial->rating" size="sm" />
        @endif
        
        <!-- Content -->
        <p class="text-base text-gray-700 leading-relaxed font-medium">
            "{{ $testimonial->content }}"
        </p>

        <!-- Product Link -->
        @if($testimonial->product)
            <div class="inline-flex items-center gap-1.5 text-xs font-medium text-primary bg-primary/5 px-2.5 py-1 rounded-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <a href="{{ route('catalog.show', $testimonial->product->slug) }}" class="hover:underline">
                    {{ $testimonial->product->name }}
                </a>
            </div>
        @endif
    </div>
    
    <!-- Footer: Author & Photos -->
    <div class="relative z-10 flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
        <div class="flex items-center gap-3">
            <!-- Initials Avatar -->
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                {{ collect(explode(' ', $testimonial->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
            </div>
            <div>
                <h3 class="font-bold text-gray-900 text-sm">{{ $testimonial->name }}</h3>
                <p class="text-xs text-gray-500">{{ $testimonial->role_or_info }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($testimonial->created_at)->translatedFormat('F Y') }}</p>
            </div>
        </div>
        
        <!-- Photos -->
        @if($testimonial->photos && is_array($testimonial->photos) && count($testimonial->photos) > 0)
        <div class="flex gap-2 shrink-0">
            @foreach($testimonial->photos as $photo)
            <a href="{{ Storage::url($photo) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200">
                <img src="{{ Storage::url($photo) }}" alt="Foto Testimoni" class="w-12 h-12 object-cover hover:scale-110 transition-transform duration-300">
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>
