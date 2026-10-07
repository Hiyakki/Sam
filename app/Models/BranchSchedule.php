<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'day_of_week',
        'is_open',
        'open_time',
        'close_time',
        'slot_duration_minutes',
        'capacity_per_slot',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_open' => 'boolean',
            'slot_duration_minutes' => 'integer',
            'capacity_per_slot' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getDayNameAttribute(): string
    {
        return match ($this->day_of_week) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
            default => 'Tidak Diketahui',
        };
    }
}
