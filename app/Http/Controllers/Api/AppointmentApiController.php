<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchClosure;
use App\Models\BranchSchedule;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentApiController extends Controller
{
    /**
     * Mengambil daftar layanan yang aktif dan terhubung ke cabang.
     */
    public function services(Branch $branch, SlotService $slotService): JsonResponse
    {
        if (! $branch->is_active) {
            return response()->json(['services' => []], 404);
        }

        $services = $slotService->availableServices($branch)->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
            ];
        });

        return response()->json([
            'branch' => [
                'id' => $branch->id,
                'name' => $branch->name,
                'address' => $branch->address,
                'phone_wa' => $branch->phone_wa,
                'operating_hours_summary' => $branch->operating_hours_summary,
            ],
            'services' => $services,
        ]);
    }

    /**
     * Mengambil daftar tanggal yang tidak dapat dipesan (tutup/libur) untuk cabang tertentu.
     */
    public function closedDates(Request $request): JsonResponse
    {
        $branchId = $request->query('branch_id');
        $branch = Branch::where('id', $branchId)->where('is_active', true)->first();

        if (! $branch) {
            return response()->json(['closed_dates' => [], 'closed_days_of_week' => [7]], 200);
        }

        // Cek hari reguler yang tutup dari branch_schedules (1=Senin, ..., 7=Minggu)
        $schedules = BranchSchedule::where('branch_id', $branch->id)->get();
        $closedDaysOfWeek = $schedules->where('is_open', false)->pluck('day_of_week')->values()->all();

        // Jika belum ada schedule, default hari Minggu (7) tutup
        if (empty($closedDaysOfWeek) && $schedules->isEmpty()) {
            $closedDaysOfWeek = [7];
        }

        // Ambil penutupan insidental (BranchClosure) dalam 90 hari ke depan
        $today = Carbon::today('Asia/Jakarta');
        $maxDate = $today->copy()->addDays(90);

        $closures = BranchClosure::where('is_closed', true)
            ->whereDate('date', '>=', $today->format('Y-m-d'))
            ->whereDate('date', '<=', $maxDate->format('Y-m-d'))
            ->where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                    ->orWhereNull('branch_id');
            })
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'closed_dates' => $closures,
            'closed_days_of_week' => $closedDaysOfWeek,
        ]);
    }

    /**
     * Mengambil slot waktu untuk cabang dan tanggal yang dipilih.
     */
    public function slots(Request $request, SlotService $slotService): JsonResponse
    {
        $branchId = $request->query('branch_id');
        $date = $request->query('date');

        if (! $branchId || ! $date) {
            return response()->json([
                'message' => 'Parameter branch_id dan date wajib disediakan.',
                'slots' => [],
            ], 422);
        }

        $branch = Branch::where('id', $branchId)->where('is_active', true)->first();

        if (! $branch) {
            return response()->json([
                'message' => 'Cabang tidak ditemukan atau nonaktif.',
                'slots' => [],
            ], 404);
        }

        try {
            $slots = $slotService->slotsFor($branch, $date);

            return response()->json([
                'branch_id' => $branch->id,
                'date' => $date,
                'slots' => $slots,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memuat jadwal slot.',
                'error' => $e->getMessage(),
                'slots' => [],
            ], 500);
        }
    }
}
