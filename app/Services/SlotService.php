<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchClosure;
use App\Models\BranchSchedule;
use App\Models\Service;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SlotService
{
    /**
     * Mengambil layanan aktif yang terhubung dengan cabang terkait.
     *
     * @return Collection<int, Service>
     */
    public function availableServices(Branch $branch): Collection
    {
        return $branch->services()
            ->where('services.is_active', true)
            ->get();
    }

    /**
     * Memeriksa apakah suatu tanggal dapat dipesan untuk cabang tersebut.
     */
    public function isDateBookable(Branch $branch, CarbonInterface|string $date): bool
    {
        if (! $branch->is_active) {
            return false;
        }

        $targetDate = $this->parseDate($date);
        $today = Carbon::today('Asia/Jakarta');

        // Tidak boleh memesan tanggal di masa lalu
        if ($targetDate->lessThan($today)) {
            return false;
        }

        // Batas maksimal hari pemesanan ke depan (default: 60 hari)
        $maxDays = (int) SiteSetting::get('general.appointment_max_days', 60);
        if ($targetDate->greaterThan($today->copy()->addDays($maxDays))) {
            return false;
        }

        $dateString = $targetDate->format('Y-m-d');

        // Cek hari libur (BranchClosure) baik untuk cabang spesifik maupun libur global (branch_id IS NULL)
        $closure = BranchClosure::whereDate('date', $dateString)
            ->where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                    ->orWhereNull('branch_id');
            })
            ->first();

        if ($closure && $closure->is_closed) {
            return false;
        }

        // Cek jadwal reguler mingguan (BranchSchedule)
        $dayOfWeek = $targetDate->dayOfWeekIso; // 1 = Senin, ..., 7 = Minggu
        $schedule = BranchSchedule::where('branch_id', $branch->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // Jika ada jam khusus di closure dan tidak tutup seharian, maka bookable
        if ($closure && ! $closure->is_closed && filled($closure->open_time) && filled($closure->close_time)) {
            return true;
        }

        if (! $schedule || ! $schedule->is_open) {
            return false;
        }

        return true;
    }

    /**
     * Menghitung dan menghasilkan daftar slot waktu pada cabang dan tanggal tertentu.
     *
     * @return array<int, array{
     *     start: string,
     *     end: string,
     *     label: string,
     *     capacity: int,
     *     booked_count: int,
     *     available: bool,
     *     reason?: string
     * }>
     */
    public function slotsFor(Branch $branch, CarbonInterface|string $date): array
    {
        $targetDate = $this->parseDate($date);

        if (! $this->isDateBookable($branch, $targetDate)) {
            return [];
        }

        $dateString = $targetDate->format('Y-m-d');
        $dayOfWeek = $targetDate->dayOfWeekIso;

        // Ambil penutupan khusus jika ada
        $closure = BranchClosure::whereDate('date', $dateString)
            ->where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                    ->orWhereNull('branch_id');
            })
            ->first();

        $schedule = BranchSchedule::where('branch_id', $branch->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // Tentukan jam buka & tutup
        if ($closure && ! $closure->is_closed && filled($closure->open_time) && filled($closure->close_time)) {
            $openTimeStr = substr((string) $closure->open_time, 0, 5);
            $closeTimeStr = substr((string) $closure->close_time, 0, 5);
        } elseif ($schedule && $schedule->is_open) {
            $openTimeStr = substr((string) $schedule->open_time, 0, 5);
            $closeTimeStr = substr((string) $schedule->close_time, 0, 5);
        } else {
            return [];
        }

        $slotDuration = $schedule?->slot_duration_minutes ?: 60;
        $capacityPerSlot = $schedule?->capacity_per_slot ?: 1;

        // Ambil jumlah janji aktif pada cabang dan tanggal tersebut
        $bookedCounts = Appointment::where('branch_id', $branch->id)
            ->whereDate('appointment_date', $dateString)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->groupBy(fn ($appt) => substr((string) $appt->start_time, 0, 5))
            ->map->count();

        $slots = [];
        $currentCursor = Carbon::createFromFormat('Y-m-d H:i', "{$dateString} {$openTimeStr}", 'Asia/Jakarta');
        $closeCursor = Carbon::createFromFormat('Y-m-d H:i', "{$dateString} {$closeTimeStr}", 'Asia/Jakarta');

        $isToday = $targetDate->isSameDay(Carbon::today('Asia/Jakarta'));
        $now = Carbon::now('Asia/Jakarta');
        $bufferMinutes = (int) SiteSetting::get('general.slot_buffer_minutes', 60);

        while ($currentCursor->copy()->addMinutes($slotDuration)->lessThanOrEqualTo($closeCursor)) {
            $startStr = $currentCursor->format('H:i');
            $endCursor = $currentCursor->copy()->addMinutes($slotDuration);
            $endStr = $endCursor->format('H:i');

            $booked = $bookedCounts->get($startStr, 0);
            $isPastOrBuffered = false;
            $reason = null;

            if ($isToday) {
                // Jika slot waktu berada sebelum waktu sekarang + buffer
                $cutoffTime = $now->copy()->addMinutes($bufferMinutes);
                if ($currentCursor->lessThanOrEqualTo($cutoffTime)) {
                    $isPastOrBuffered = true;
                    $reason = 'Waktu telah lewat';
                }
            }

            $isFull = ($booked >= $capacityPerSlot);
            if ($isFull && ! $reason) {
                $reason = 'Slot penuh';
            }

            $isAvailable = (! $isPastOrBuffered && ! $isFull);

            $slotItem = [
                'start' => $startStr,
                'end' => $endStr,
                'label' => "{$startStr} - {$endStr} WIB",
                'capacity' => $capacityPerSlot,
                'booked_count' => $booked,
                'available' => $isAvailable,
            ];

            if ($reason) {
                $slotItem['reason'] = $reason;
            }

            $slots[] = $slotItem;

            $currentCursor->addMinutes($slotDuration);
        }

        return $slots;
    }

    /**
     * Memesan janji temu secara atomik dengan database lock untuk mencegah race condition.
     *
     * @param array{
     *     branch_id: int,
     *     service_id: int,
     *     appointment_date: string,
     *     start_time: string,
     *     end_time?: string,
     *     patient_name: string,
     *     patient_email: string,
     *     patient_phone: string,
     *     patient_age: int|string,
     *     complaint: string,
     *     prescription_path?: string|null
     * } $data
     *
     * @throws ValidationException
     */
    public function book(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $branch = Branch::where('id', $data['branch_id'])->lockForUpdate()->first();

            if (! $branch || ! $branch->is_active) {
                throw ValidationException::withMessages([
                    'branch_id' => 'Kantor cabang yang dipilih tidak aktif atau tidak ditemukan.',
                ]);
            }

            // Validasi apakah layanan terhubung dengan cabang
            $serviceExists = $branch->services()
                ->where('services.id', $data['service_id'])
                ->where('services.is_active', true)
                ->exists();

            if (! $serviceExists) {
                throw ValidationException::withMessages([
                    'service_id' => 'Layanan yang dipilih tidak tersedia pada cabang ini.',
                ]);
            }

            $targetDate = $this->parseDate($data['appointment_date']);

            if (! $this->isDateBookable($branch, $targetDate)) {
                throw ValidationException::withMessages([
                    'appointment_date' => 'Tanggal kunjungan tidak tersedia untuk reservasi.',
                ]);
            }

            $dateString = $targetDate->format('Y-m-d');
            $startTime = substr((string) $data['start_time'], 0, 5);

            // Validasi slot apakah ada di dalam jam operasional
            $availableSlots = collect($this->slotsFor($branch, $targetDate));
            $matchingSlot = $availableSlots->firstWhere('start', $startTime);

            if (! $matchingSlot) {
                throw ValidationException::withMessages([
                    'start_time' => 'Jam kunjungan yang dipilih tidak sesuai dengan jadwal operasional.',
                ]);
            }

            if (! $matchingSlot['available']) {
                throw ValidationException::withMessages([
                    'start_time' => 'Slot sudah terisi, silakan pilih jam lain.',
                ]);
            }

            // Lock baris janji pada slot terkait untuk memastikan kapasitas
            $activeAppointmentsCount = Appointment::where('branch_id', $branch->id)
                ->whereDate('appointment_date', $dateString)
                ->where(function ($q) use ($startTime) {
                    $q->where('start_time', $startTime)
                        ->orWhere('start_time', "{$startTime}:00");
                })
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->count();

            if ($activeAppointmentsCount >= $matchingSlot['capacity']) {
                throw ValidationException::withMessages([
                    'start_time' => 'Slot sudah terisi, silakan pilih jam lain.',
                ]);
            }

            $endTime = $data['end_time'] ?? $matchingSlot['end'];
            $normalizedPhone = $this->normalizePhoneNumber((string) $data['patient_phone']);

            $code = $this->generateUniqueCode();

            return Appointment::create([
                'code' => $code,
                'branch_id' => $branch->id,
                'service_id' => $data['service_id'],
                'appointment_date' => $dateString,
                'start_time' => "{$startTime}:00",
                'end_time' => strlen($endTime) === 5 ? "{$endTime}:00" : $endTime,
                'patient_name' => trim((string) $data['patient_name']),
                'patient_email' => strtolower(trim((string) $data['patient_email'])),
                'patient_phone' => $normalizedPhone,
                'patient_age' => (int) $data['patient_age'],
                'complaint' => trim((string) $data['complaint']),
                'prescription_path' => $data['prescription_path'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Menghasilkan kode janji acak unik (minimal 10 karakter).
     */
    public function generateUniqueCode(): string
    {
        do {
            // Contoh format: SAM-7K9X2M4B (12 karakter total, sangat mudah dibaca)
            $code = 'SAM'.strtoupper(Str::random(9));
        } while (Appointment::where('code', $code)->exists());

        return $code;
    }

    /**
     * Parse input tanggal menjadi instance Carbon dengan timezone Asia/Jakarta.
     */
    protected function parseDate(CarbonInterface|string $date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date->copy()->timezone('Asia/Jakarta')->startOfDay();
        }

        if ($date instanceof CarbonInterface) {
            return Carbon::instance($date)->timezone('Asia/Jakarta')->startOfDay();
        }

        return Carbon::parse($date, 'Asia/Jakarta')->startOfDay();
    }

    /**
     * Menormalkan nomor telepon ke format internasional standar Indonesia (62xxxxxxxxxx).
     */
    protected function normalizePhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($cleaned, '0')) {
            return '62'.substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        }

        return '62'.$cleaned;
    }
}
