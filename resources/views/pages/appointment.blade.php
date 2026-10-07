@extends('layouts.app')

@section('title', 'Pendaftaran Janji Temu & Konsultasi - SAM Orthotic Prosthetic')
@section('meta_description', 'Reservasi jadwal konsultasi, pemeriksaan biomekanik, dan fitting alat ortotik prostetik di cabang SAM terdekat.')

@section('content')
<!-- Header Banner -->
<div class="bg-gradient-to-b from-[#EBF2E8]/60 via-[#F7F7F6] to-[#F7F7F6] py-12 sm:py-16 border-b border-[#E3E3E1]/60">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-bold uppercase tracking-widest text-[#869480] bg-white/80 backdrop-blur-xs px-3 py-1 rounded-full border border-[#869480]/20 inline-block">
            Pendaftaran Online Resmi
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-['Playfair_Display'] text-[#242E24] mt-3 tracking-tight">
            Buat Janji Temu & Konsultasi
        </h1>
        <p class="text-sm sm:text-base text-[#4B5549] mt-3 max-w-2xl mx-auto leading-relaxed">
            Pilih cabang terdekat, tentukan tanggal kunjungan, dan amankan slot waktu konsultasi Anda secara langsung tanpa antre.
        </p>
    </div>
</div>

<!-- Main Form Container -->
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14"
     x-data="{
        branchId: '{{ old('branch_id', '') }}',
        serviceId: '{{ old('service_id', '') }}',
        appointmentDate: '{{ old('appointment_date', '') }}',
        selectedSlot: '{{ old('start_time', '') }}',
        services: [],
        slots: [],
        branchScheduleSummary: '',
        branchAddress: '',
        isLoadingServices: false,
        isLoadingSlots: false,
        slotError: '',
        isSubmitting: false,

        init() {
            if (this.branchId) {
                this.loadServices(this.branchId);
                if (this.appointmentDate) {
                    this.loadSlots();
                }
            }
        },

        onBranchChange() {
            this.serviceId = '';
            this.appointmentDate = '';
            this.selectedSlot = '';
            this.slots = [];
            this.slotError = '';
            if (this.branchId) {
                this.loadServices(this.branchId);
            } else {
                this.services = [];
                this.branchScheduleSummary = '';
                this.branchAddress = '';
            }
        },

        async loadServices(id) {
            this.isLoadingServices = true;
            try {
                const res = await fetch(`/api/appointment/branches/${id}/services`);
                if (res.ok) {
                    const data = await res.json();
                    this.services = data.services || [];
                    this.branchScheduleSummary = data.branch.operating_hours_summary || '';
                    this.branchAddress = data.branch.address || '';
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isLoadingServices = false;
            }
        },

        onDateChange() {
            this.selectedSlot = '';
            this.slotError = '';
            if (this.branchId && this.appointmentDate) {
                this.loadSlots();
            }
        },

        async loadSlots() {
            if (!this.branchId || !this.appointmentDate) return;
            this.isLoadingSlots = true;
            this.slotError = '';
            this.slots = [];
            try {
                const res = await fetch(`/api/appointment/slots?branch_id=${this.branchId}&date=${this.appointmentDate}`);
                const data = await res.json();
                if (res.ok) {
                    this.slots = data.slots || [];
                    if (this.slots.length === 0) {
                        this.slotError = 'Kantor cabang tutup atau tidak ada slot yang tersedia pada tanggal ini.';
                    }
                } else {
                    this.slotError = data.message || 'Gagal memuat jadwal slot.';
                }
            } catch (e) {
                this.slotError = 'Terjadi gangguan jaringan saat memuat slot waktu.';
            } finally {
                this.isLoadingSlots = false;
            }
        }
     }">

    @if ($errors->any())
        <div class="mb-8 p-5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Mohon periksa kembali formulir Anda:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1 pt-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#E3E3E1] shadow-xs">
        <form action="{{ route('appointment.store') }}"
              method="POST"
              enctype="multipart/form-data"
              @submit="isSubmitting = true"
              class="space-y-8">
            @csrf

            <!-- Honeypot anti-spam (harus tetap kosong) -->
            <div style="display:none; visibility:hidden; opacity:0; position:absolute; left:-9999px;">
                <label for="website_hp">Leave empty</label>
                <input type="text" name="website_hp" id="website_hp" autocomplete="off" tabindex="-1">
            </div>

            <!-- Bagian 1: Identitas Pasien -->
            <div class="space-y-5">
                <div class="border-b border-[#E3E3E1] pb-3">
                    <h2 class="text-lg font-bold font-['Playfair_Display'] text-[#242E24] flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#EBF2E8] text-[#869480] text-xs flex items-center justify-center font-bold">1</span>
                        <span>Identitas & Kontak Pasien</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Field 1: Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label for="patient_name" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                            Nama Lengkap Pasien <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="patient_name"
                               id="patient_name"
                               value="{{ old('patient_name') }}"
                               required
                               placeholder="Nama lengkap sesuai kartu identitas"
                               class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors">
                    </div>

                    <!-- Field 2: Alamat Email -->
                    <div>
                        <label for="patient_email" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="patient_email"
                               id="patient_email"
                               value="{{ old('patient_email') }}"
                               required
                               placeholder="contoh@email.com"
                               class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors">
                    </div>

                    <!-- Field 3: Usia Pasien -->
                    <div>
                        <label for="patient_age" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                            Usia Pasien (Tahun) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="patient_age"
                               id="patient_age"
                               min="0"
                               max="120"
                               value="{{ old('patient_age') }}"
                               required
                               placeholder="Contoh: 35"
                               class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors">
                    </div>

                    <!-- Field 4: No Telepon / WA -->
                    <div class="sm:col-span-2">
                        <label for="patient_phone" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                            Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                        </label>
                        <input type="tel"
                               name="patient_phone"
                               id="patient_phone"
                               value="{{ old('patient_phone') }}"
                               required
                               placeholder="08xxxxxxxxxx atau +628xxxxxxxxxx"
                               class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors">
                        <p class="text-[11px] text-[#869480] mt-1">
                            Digunakan untuk konfirmasi kehadiran dan pengiriman pengingat jadwal konsultasi.
                        </p>
                    </div>

                    <!-- Field 5: Keluhan Medis -->
                    <div class="sm:col-span-2">
                        <label for="complaint" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                            Keluhan Fisik / Kebutuhan Alat <span class="text-red-500">*</span>
                        </label>
                        <textarea name="complaint"
                                  id="complaint"
                                  rows="3"
                                  maxlength="1000"
                                  required
                                  placeholder="Ceritakan kondisi atau kebutuhan alat (misal: kaki palsu bawah lutut, kelainan kaki drop foot, skoliosis, dsb)..."
                                  class="w-full text-sm rounded-xl border border-[#E3E3E1] p-4 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors">{{ old('complaint') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Lokasi & Jenis Layanan -->
            <div class="space-y-5 pt-4">
                <div class="border-b border-[#E3E3E1] pb-3">
                    <h2 class="text-lg font-bold font-['Playfair_Display'] text-[#242E24] flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#EBF2E8] text-[#869480] text-xs flex items-center justify-center font-bold">2</span>
                        <span>Lokasi & Layanan Pemeriksaan</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Field 6: Pilih Kantor -->
                    <div>
                        <label for="branch_id" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                            Pilih Kantor Cabang <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id"
                                id="branch_id"
                                x-model="branchId"
                                @change="onBranchChange()"
                                required
                                class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors">
                            <option value="">-- Pilih Cabang SAM --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Field 7: Pilih Layanan -->
                    <div>
                        <label for="service_id" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                            Pilih Layanan <span class="text-red-500">*</span>
                        </label>
                        <select name="service_id"
                                id="service_id"
                                x-model="serviceId"
                                :disabled="!branchId || isLoadingServices"
                                required
                                class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors disabled:opacity-50">
                            <option value="">-- Pilih Layanan Tersedia --</option>
                            <template x-for="s in services" :key="s.id">
                                <option :value="s.id" x-text="s.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Info Operasional Cabang -->
                <div x-show="branchScheduleSummary"
                     x-transition
                     class="p-4 rounded-xl bg-[#EBF2E8]/60 border border-[#A5BF98]/40 text-xs text-[#242E24] space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#869480]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Jadwal Operasional Cabang:</span>
                    </div>
                    <p class="text-[#4B5549]" x-text="branchScheduleSummary"></p>
                    <p class="text-[11px] text-[#869480]" x-text="branchAddress"></p>
                </div>
            </div>

            <!-- Bagian 3: Jadwal Kedatangan -->
            <div class="space-y-5 pt-4">
                <div class="border-b border-[#E3E3E1] pb-3">
                    <h2 class="text-lg font-bold font-['Playfair_Display'] text-[#242E24] flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#EBF2E8] text-[#869480] text-xs flex items-center justify-center font-bold">3</span>
                        <span>Tanggal & Waktu Kunjungan</span>
                    </h2>
                </div>

                <!-- Field 8: Tanggal Kedatangan -->
                <div>
                    <label for="appointment_date" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                        Tanggal Kedatangan <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="appointment_date"
                           id="appointment_date"
                           x-model="appointmentDate"
                           @change="onDateChange()"
                           :min="new Date().toISOString().split('T')[0]"
                           :disabled="!branchId"
                           required
                           class="w-full text-sm rounded-xl border border-[#E3E3E1] px-4 py-3 bg-[#F7F7F6] focus:bg-white focus:outline-hidden focus:border-[#869480] transition-colors disabled:opacity-50">
                    <p class="text-[11px] text-[#869480] mt-1">
                        Pemesanan dibuka hingga 60 hari ke depan. Minggu & hari libur nasional tutup secara reguler.
                    </p>
                </div>

                <!-- Field 9: Pilih Slot Waktu -->
                <div x-show="appointmentDate" x-transition class="space-y-3 pt-2">
                    <label class="block text-xs font-bold text-[#242E24] uppercase tracking-wider">
                        Pilih Slot Jam Kedatangan <span class="text-red-500">*</span>
                    </label>

                    <!-- Loading State -->
                    <div x-show="isLoadingSlots" class="p-6 text-center text-xs text-[#869480]">
                        <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-[#869480]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Memeriksa ketersediaan kuota slot...
                    </div>

                    <!-- Error Message jika tutup / penuh -->
                    <div x-show="slotError && !isLoadingSlots"
                         class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800"
                         x-text="slotError">
                    </div>

                    <!-- Grid Tombol Slot Waktu -->
                    <div x-show="slots.length > 0 && !isLoadingSlots"
                         class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <template x-for="slot in slots" :key="slot.start">
                            <button type="button"
                                    :disabled="!slot.available"
                                    @click="selectedSlot = slot.start"
                                    :class="{
                                        'bg-[#242E24] text-white ring-2 ring-[#242E24] font-bold shadow-xs': selectedSlot === slot.start && slot.available,
                                        'bg-[#F7F7F6] text-[#242E24] hover:bg-[#EBF2E8] border border-[#E3E3E1]': selectedSlot !== slot.start && slot.available,
                                        'bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed opacity-60': !slot.available
                                    }"
                                    class="p-3 rounded-xl text-center text-xs transition-all flex flex-col items-center justify-center min-h-[64px]">
                                <span class="font-semibold text-sm" x-text="slot.start"></span>
                                <span class="text-[10px] mt-0.5 opacity-80" x-text="slot.available ? 'Tersedia' : (slot.reason || 'Penuh')"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Hidden input to pass selected slot to POST request -->
                    <input type="hidden" name="start_time" :value="selectedSlot" required>
                    <p x-show="selectedSlot" class="text-xs text-[#869480] font-medium pt-1">
                        Slot terpilih: <strong class="text-[#242E24]" x-text="selectedSlot + ' WIB'"></strong>
                    </p>
                </div>
            </div>

            <!-- Bagian 4: Berkas & Persetujuan -->
            <div class="space-y-5 pt-4">
                <div class="border-b border-[#E3E3E1] pb-3">
                    <h2 class="text-lg font-bold font-['Playfair_Display'] text-[#242E24] flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#EBF2E8] text-[#869480] text-xs flex items-center justify-center font-bold">4</span>
                        <span>Dokumen Medis & Persetujuan</span>
                    </h2>
                </div>

                <!-- Field 10: Upload Resep Dokter (Opsional) -->
                <div>
                    <label for="prescription" class="block text-xs font-bold text-[#242E24] uppercase tracking-wider mb-1.5">
                        Unggah Resep / Rujukan Dokter <span class="text-xs font-normal text-[#869480] lowercase">(opsional)</span>
                    </label>
                    <input type="file"
                           name="prescription"
                           id="prescription"
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-[#4B5549] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#EBF2E8] file:text-[#242E24] hover:file:bg-[#A5BF98]/50 file:cursor-pointer p-2 rounded-xl border border-[#E3E3E1] bg-[#F7F7F6]">
                    <p class="text-[11px] text-[#869480] mt-1.5">
                        Format: PDF, JPG, PNG (Maksimal 5 MB). File disimpan dalam storage privat terenkripsi demi menjaga kerahasiaan medis Anda.
                    </p>
                </div>

                <!-- Field 11: Persetujuan Kebijakan Privasi -->
                <div class="pt-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="privacy_consent"
                               value="1"
                               {{ old('privacy_consent') ? 'checked' : '' }}
                               required
                               class="mt-1 w-4 h-4 rounded text-[#242E24] focus:ring-[#869480] border-[#E3E3E1]">
                        <span class="text-xs text-[#4B5549] leading-relaxed">
                            Saya menyetujui bahwa data yang saya masukkan adalah benar untuk keperluan penjadwalan konsultasi dan telah membaca <a href="{{ route('privacy') }}" target="_blank" class="font-bold text-[#242E24] underline hover:text-[#869480]">Kebijakan Privasi</a> SAM Orthotic Prosthetic sesuai UU PDP. <span class="text-red-500">*</span>
                        </span>
                    </label>
                </div>

                <!-- Field 12: Tombol Submit -->
                <div class="pt-6">
                    <button type="submit"
                            :disabled="!selectedSlot || isSubmitting"
                            class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-[#E8343B] hover:bg-[#D1252C] transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="isSubmitting">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </template>
                        <span x-text="isSubmitting ? 'Memproses Pendaftaran...' : 'Konfirmasi & Ajukan Janji Temu'"></span>
                    </button>
                    <p class="text-center text-[11px] text-[#869480] mt-3">
                        Setelah pengajuan, Anda akan menerima Kode Janji Temu untuk pelacakan dan perubahan jadwal.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
