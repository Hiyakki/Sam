<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'role_or_info',
        'product_id',
        'content',
        'rating',
        'photo',
        'consent_confirmed',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'consent_confirmed' => 'boolean',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer',
            'rating'            => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: testimoni aktif dan sudah dikonfirmasi izinnya, urut sort_order.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)
            ->where('consent_confirmed', true)
            ->orderBy('sort_order', 'asc');
    }
}
