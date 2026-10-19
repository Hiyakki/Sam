<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone_wa',
        'email',
        'maps_embed_url',
        'maps_link',
        'photo',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'branch_service')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BranchSchedule::class);
    }

    public function closures(): HasMany
    {
        return $this->hasMany(BranchClosure::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Jadwal operasional yang terkelompok secara cerdas untuk hari-hari berturut-turut dengan jam yang sama.
     *
     * @return array<int, array{label: string, is_open: bool, open_time: ?string, close_time: ?string, hours: string, days: array<int, int>}>
     */
    public function getGroupedSchedulesAttribute(): array
    {
        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $schedules = $this->schedules->sortBy('day_of_week')->values();

        if ($schedules->isEmpty()) {
            return [
                [
                    'label' => 'Senin – Sabtu',
                    'is_open' => true,
                    'open_time' => '08:00',
                    'close_time' => '17:00',
                    'hours' => '08:00 – 17:00 WIB',
                    'days' => [1, 2, 3, 4, 5, 6],
                ],
                [
                    'label' => 'Minggu',
                    'is_open' => false,
                    'open_time' => null,
                    'close_time' => null,
                    'hours' => 'Tutup',
                    'days' => [7],
                ],
            ];
        }

        $groups = [];
        $currentGroup = null;

        foreach ($schedules as $sched) {
            $isOpen = (bool) $sched->is_open;
            $openTime = $isOpen ? substr((string) $sched->open_time, 0, 5) : null;
            $closeTime = $isOpen ? substr((string) $sched->close_time, 0, 5) : null;

            if ($currentGroup === null) {
                $currentGroup = [
                    'is_open' => $isOpen,
                    'open_time' => $openTime,
                    'close_time' => $closeTime,
                    'days' => [(int) $sched->day_of_week],
                ];
            } else {
                $lastDay = end($currentGroup['days']);
                $isConsecutive = ((int) $sched->day_of_week === $lastDay + 1);
                $isSameSchedule = ($currentGroup['is_open'] === $isOpen)
                    && ($currentGroup['open_time'] === $openTime)
                    && ($currentGroup['close_time'] === $closeTime);

                if ($isConsecutive && $isSameSchedule) {
                    $currentGroup['days'][] = (int) $sched->day_of_week;
                } else {
                    $groups[] = $currentGroup;
                    $currentGroup = [
                        'is_open' => $isOpen,
                        'open_time' => $openTime,
                        'close_time' => $closeTime,
                        'days' => [(int) $sched->day_of_week],
                    ];
                }
            }
        }

        if ($currentGroup !== null) {
            $groups[] = $currentGroup;
        }

        return array_map(function ($group) use ($dayNames) {
            $days = $group['days'];
            $firstDay = $dayNames[$days[0]] ?? ('Hari '.$days[0]);
            $lastDay = $dayNames[end($days)] ?? ('Hari '.end($days));

            if (count($days) === 7) {
                $label = 'Setiap Hari';
            } elseif (count($days) === 1) {
                $label = $firstDay;
            } else {
                $label = "{$firstDay} – {$lastDay}";
            }

            if ($group['is_open']) {
                $hours = "{$group['open_time']} – {$group['close_time']} WIB";
            } else {
                $hours = 'Tutup';
            }

            return [
                'label' => $label,
                'is_open' => $group['is_open'],
                'open_time' => $group['open_time'],
                'close_time' => $group['close_time'],
                'hours' => $hours,
                'days' => $days,
            ];
        }, $groups);
    }

    /**
     * Mengecek status buka cabang untuk hari ini dan jam saat ini.
     *
     * @return array{is_open_today: bool, is_open_now: bool, today_label: string, today_hours: ?string}
     */
    public function getTodayStatusAttribute(): array
    {
        $now = now('Asia/Jakarta');
        $todayIso = (int) $now->dayOfWeekIso;
        $currentTime = $now->format('H:i:s');

        $todaySchedule = $this->schedules->firstWhere('day_of_week', $todayIso);

        if (! $todaySchedule || ! $todaySchedule->is_open) {
            return [
                'is_open_today' => false,
                'is_open_now' => false,
                'today_label' => 'Tutup Hari Ini',
                'today_hours' => null,
            ];
        }

        $openTime = substr((string) $todaySchedule->open_time, 0, 5);
        $closeTime = substr((string) $todaySchedule->close_time, 0, 5);
        $rawOpen = (string) $todaySchedule->open_time;
        $rawClose = (string) $todaySchedule->close_time;
        $isOpenNow = ($currentTime >= $rawOpen && $currentTime <= $rawClose);

        return [
            'is_open_today' => true,
            'is_open_now' => $isOpenNow,
            'today_label' => $isOpenNow ? 'Buka Sekarang' : 'Buka Hari Ini',
            'today_hours' => "{$openTime} – {$closeTime} WIB",
        ];
    }

    /**
     * Ringkasan jam operasional untuk tampilan publik.
     */
    public function getOperatingHoursSummaryAttribute(): string
    {
        $schedules = $this->schedules->sortBy('day_of_week');

        if ($schedules->isEmpty()) {
            return 'Senin - Sabtu: 08:00 - 17:00 WIB (Minggu Tutup)';
        }

        $openDays = $schedules->where('is_open', true);
        $closedDays = $schedules->where('is_open', false);

        if ($openDays->isEmpty()) {
            return 'Sementara Tutup';
        }

        $firstOpen = $openDays->first();
        $openTime = substr((string) $firstOpen->open_time, 0, 5);
        $closeTime = substr((string) $firstOpen->close_time, 0, 5);

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $openDayNumbers = $openDays->pluck('day_of_week')->sort()->values();

        if ($openDayNumbers->count() === 6 && $openDayNumbers->last() === 6) {
            return "Senin - Sabtu: {$openTime} - {$closeTime} WIB (Minggu Tutup)";
        }

        if ($openDayNumbers->count() === 7) {
            return "Setiap Hari: {$openTime} - {$closeTime} WIB";
        }

        $summary = $openDays->map(function ($s) use ($dayNames) {
            $day = $dayNames[$s->day_of_week] ?? '';
            $open = substr((string) $s->open_time, 0, 5);
            $close = substr((string) $s->close_time, 0, 5);

            return "{$day}: {$open} - {$close}";
        })->join(', ');

        if ($closedDays->isNotEmpty()) {
            $closedNames = $closedDays->map(fn ($s) => $dayNames[$s->day_of_week] ?? '')->filter()->join(', ');
            $summary .= " ({$closedNames} Tutup)";
        }

        return $summary;
    }
}
