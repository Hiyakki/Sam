@php
    $previewTitle = $title ?: 'Judul Halaman - SAM Orthotic Prosthetic';
    $previewUrl = $url ?: url('/katalog/sample-slug');
    $previewDesc = $description ?: 'Deskripsi halaman akan muncul di sini sesuai dengan ringkasan konten produk atau kategori untuk hasil pencarian Google.';
    $parsedHost = parse_url($previewUrl, PHP_URL_HOST) ?? 'localhost';
@endphp

<div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xs space-y-1.5 max-w-2xl font-sans">
    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 truncate">
        <span class="inline-block w-4 h-4 rounded-full bg-primary/20 text-primary text-[10px] font-bold text-center leading-4">S</span>
        <span class="font-medium text-gray-700 dark:text-gray-300">SAM Orthotic Prosthetic</span>
        <span class="text-gray-400">›</span>
        <span class="truncate">{{ $previewUrl }}</span>
    </div>
    <div class="text-base sm:text-lg text-[#1a0dab] dark:text-[#8ab4f8] hover:underline cursor-pointer font-medium leading-snug line-clamp-1">
        {{ $previewTitle }}
    </div>
    <div class="text-xs sm:text-sm text-[#4d5156] dark:text-[#bdc1c6] leading-relaxed line-clamp-2">
        {{ Str::limit(strip_tags($previewDesc), 160) }}
    </div>
</div>
