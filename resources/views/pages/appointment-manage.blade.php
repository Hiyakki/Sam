@extends('layouts.app')

@section('title', 'Kelola Janji Temu: ' . $appointment->code . ' - SAM Orthotic Prosthetic')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16"
     x-data="{
        showRescheduleModal: false,
        newDate: '',
        newSlot: '',
        slots: [],
        isLoadingSlots: false,
        slotError: '',
        branchId: '{{ $appointment->branch_id }}',

        async loadNewSlots() {
            if (!this.newDate) return;
            this.isLoadingSlots = true;
            this.slotError = '';
            this.slots = [];
            this.newSlot = '';
            try {
                const res = await fetch(`/api/appointment/slots?branch_id=${this.branchId}&date=${this.newDate}`);
                const data = await res.json();
                if (res.ok) {
                    this.slots = data.slots || [];
                    if (this.slots.length === 0) {
                        this.slotError = 'Kantor cabang tutup atau tidak ada slot pada tanggal ini.';
                    }
                } else {
                    this.slotError = data.message || 'Gagal memuat slot jadwal.';
                }
            } catch (e) {
                this.slotError = 'Gangguan jaringan saat memuat slot waktu.';
            } finally {
                this.isLoadingSlots = false;
            }
        }
     }">

    <!-- Alert Status Flash -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-[#EBF2E8] border border-[#A5BF98] text-[#242E24] text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-[#869480] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm space-y-1">
            <div class="font-bold">Gagal memproses permintaan:</div>
            <ul class="list-disc pl-5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#E3E3E1] shadow-xs space-y-8">
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#E3E3E1] pb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#869480]">Detail Reservasi Pasien</span>
                <h1 class="text-2xl sm:text-3xl font-bold font-['Playfair_Display'] text-[#242E24] mt-1">
                    Kode: {{ $appointment->code }}
                </h1>
            </div>

            <!-- Status Badge -->
            <div>
                @php
                    $statusStyles = [
                        'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'confirmed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'completed' => 'bg-blue-50 text-blue-800 border-blue-200',
                        'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
                        'no_show' => 'bg-gray-100 text-gray-700 border-gray-300',
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Terkonfirmasi',
                        'completed' => 'Kunjungan Selesai',
                        'cancelled' => 'Dibatalkan' . ($appointment->cancelled_by === 'patient' ? ' (Oleh Pasien)' : ' (Oleh Admin)'),
                        'no_show' => 'Tidak Hadir',
                    ];
                @endphp
                <span class="inline-flex px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $statusStyles[$appointment->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                    {{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}
                </span>
            </div>
        </div>

        <!-- Detail Data Pasien & Jadwal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-xs sm:text-sm">
            <!-- Kolom 1: Data Pasien -->
            <div class="space-y-4">
                <h3 class="font-bold text-base font-['Playfair_Display'] text-[#242E24]">Informasi Pasien</h3>
                <div class="space-y-2.5">
                    <div>
                        <span class="text-[#869480] block text-[11px] uppercase tracking-wider font-semibold">Nama Pasien</span>
                        <span class="font-bold text-[#242E24]">{{ $appointment->patient_name }} ({{ $appointment->patient_age }} Tahun)</span>
                    </div>
                    <div>
                        <span class="text-[#869480] block text-[11px] uppercase tracking-wider font-semibold">Kontak WhatsApp</span>
                        <span class="text-[#242E24]">{{ $appointment->patient_phone }}</span>
                    </div>
                    <div>
                        <span class="text-[#869480] block text-[11px] uppercase tracking-wider font-semibold">Email</span>
                        <span class="text-[#242E24]">{{ $appointment->patient_email }}</span>
                    </div>
                    <div>
                        <span class="text-[#869480] block text-[11px] uppercase tracking-wider font-semibold">Keluhan / Kebutuhan</span>
                        <p class="text-[#4B5549] bg-[#F7F7F6] p-3 rounded-xl border border-[#E3E3E1] mt-1 leading-relaxed">
                            {{ $appointment->complaint }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Jadwal & Lokasi -->
            <div class="space-y-4">
                <h3 class="font-bold text-base font-['Playfair_Display'] text-[#242E24]">Jadwal & Lokasi</h3>
                <div class="space-y-2.5">
                    <div>
                        <span class="text-[#869480] block text-[11px] uppercase tracking-wider font-semibold">Kantor Cabang</span>
                        <span class="font-bold text-[#242E24]">{{ $appointment->branch?->name }}</span>
                        <p class="text-[11px] text-[#4B5549] mt-0.5">{{ $appointment->branch?->address }}</p>
                    </div>
                    <div>
                        <span class="text-[#869480] block text-[11px] uppercase tracking-wider font-semibold">Layanan</span>
                        <span class="font-semibold text-[#242E24]">{{ $appointment->service?->name }}</span>
                    </div>
                    <div>
                        <span class="text-[#869480] block text-[11px] uppercase tracking-wider font-semibold">Waktu Kedatangan</span>
                        <div class="mt-1 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#EBF2E8] border border-[#A5BF98]/60 text-[#242E24] font-bold">
                            <svg class="w-4 h-4 text-[#869480]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $appointment->appointment_date->isoFormat('dddd, D MMMM Y') }}</span>
                            <span>•</span>
                            <span>{{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }} WIB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Mandiri Pasien -->
        <div class="pt-6 border-t border-[#E3E3E1] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Aksi 1: Ubah Jadwal -->
                @if($canReschedule)
                    <button type="button"
                            @click="showRescheduleModal = true"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#242E24] hover:bg-[#323D32] transition-colors shadow-xs">
                        Ubah Jadwal Kunjungan
                    </button>
                @else
                    <span class="text-xs text-[#869480] italic">
                        @if(in_array($appointment->status, ['completed', 'cancelled', 'no_show']))
                            Jadwal tidak dapat diubah karena status sudah final.
                        @else
                            Perubahan jadwal hanya dapat dilakukan minimal {{ $minHours }} jam sebelum janji temu.
                        @endif
                    </span>
                @endif

                <!-- Aksi 2: Batalkan Janji -->
                @if($canCancel)
                    <form action="{{ route('appointment.cancel', $appointment->code) }}"
                          method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini? Tindakan ini tidak dapat diurungkan.');">
                        @csrf
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                            Batalkan Janji
                        </button>
                    </form>
                @endif
            </div>

            <!-- Aksi 3: Chat WA Cabang -->
            @php
                $branchWa = preg_replace('/[^0-9]/', '', $appointment->branch?->phone_wa ?? $settings['company.whatsapp'] ?? '6281234567890');
                $waMessage = "Halo {$appointment->branch?->name}, saya ingin menanyakan janji temu saya dengan kode {$appointment->code} atas nama {$appointment->patient_name}.";
            @endphp
            <a href="https://wa.me/{{ $branchWa }}?text={{ urlencode($waMessage) }}"
               target="_blank"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#25D366] hover:bg-[#20b858] transition-colors shadow-xs">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                </svg>
                <span>Hubungi Cabang via WA</span>
            </a>
        </div>
    </div>

    <!-- Modal Reschedule Jadwal -->
    <div x-show="showRescheduleModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-[#E3E3E1] shadow-xl space-y-6"
             @click.away="showRescheduleModal = false">
            <div class="flex items-center justify-between border-b border-[#E3E3E1] pb-3">
                <h3 class="font-bold text-lg font-['Playfair_Display'] text-[#242E24]">
                    Pilih Jadwal Kunjungan Baru
                </h3>
                <button type="button" @click="showRescheduleModal = false" class="text-[#869480] hover:text-[#242E24] text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('appointment.reschedule', $appointment->code) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Pilih Tanggal Baru -->
                <div>
                    <label class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                        Tanggal Kedatangan Baru
                    </label>
                    <input type="date"
                           name="appointment_date"
                           x-model="newDate"
                           @change="loadNewSlots()"
                           :min="new Date().toISOString().split('T')[0]"
                           required
                           class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6]">
                </div>

                <!-- Slot Waktu Baru -->
                <div x-show="newDate" class="space-y-2">
                    <label class="block text-xs font-bold text-[#242E24] uppercase tracking-wider">
                        Pilih Slot Jam Baru
                    </label>

                    <div x-show="isLoadingSlots" class="text-xs text-[#869480] py-2 text-center">
                        Memeriksa ketersediaan slot...
                    </div>

                    <div x-show="slotError && !isLoadingSlots"
                         class="p-3 rounded-xl bg-amber-50 text-amber-800 text-xs"
                         x-text="slotError"></div>

                    <div x-show="slots.length > 0 && !isLoadingSlots"
                         class="grid grid-cols-3 gap-2">
                        <template x-for="slot in slots" :key="slot.start">
                            <button type="button"
                                    :disabled="!slot.available"
                                    @click="newSlot = slot.start"
                                    :class="{
                                        'bg-[#242E24] text-white font-bold ring-2 ring-[#242E24]': newSlot === slot.start && slot.available,
                                        'bg-[#F7F7F6] text-[#242E24] hover:bg-[#EBF2E8] border border-[#E3E3E1]': newSlot !== slot.start && slot.available,
                                        'bg-gray-100 text-gray-400 opacity-50 cursor-not-allowed': !slot.available
                                    }"
                                    class="p-2.5 rounded-lg text-xs flex flex-col items-center justify-center">
                                <span class="font-semibold" x-text="slot.start"></span>
                                <span class="text-[9px] opacity-75" x-text="slot.available ? 'Tersedia' : 'Penuh'"></span>
                            </button>
                        </template>
                    </div>

                    <input type="hidden" name="start_time" :value="newSlot" required>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E3E3E1]">
                    <button type="button"
                            @click="showRescheduleModal = false"
                            class="px-4 py-2 text-xs font-semibold text-[#4B5549] hover:bg-[#F7F7F6] rounded-xl">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="!newSlot"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#E8343B] hover:bg-[#D1252C] disabled:opacity-50 transition-colors">
                        Simpan Perubahan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
