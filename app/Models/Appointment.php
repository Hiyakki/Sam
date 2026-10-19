<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'service_id',
        'appointment_date',
        'start_time',
        'end_time',
        'patient_name',
        'patient_email',
        'patient_phone',
        'patient_age',
        'complaint',
        'prescription_path',
        'status',
        'cancelled_by',
        'admin_notes',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'patient_age' => 'integer',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereIn('status', ['pending', 'confirmed']);
    }
}
