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
