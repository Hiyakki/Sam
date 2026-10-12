@props(['average' => 0, 'total' => 0])

<div class="flex items-center justify-center gap-4 bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100 inline-flex mt-6">
    <div class="text-3xl font-bold text-gray-900">{{ number_format($average, 1) }}</div>
    <div class="flex flex-col">
        <x-star-rating :rating="round($average)" size="sm" />
        <span class="text-xs text-gray-500 mt-1 font-medium">Berdasarkan {{ $total }} ulasan pasien</span>
    </div>
</div>
