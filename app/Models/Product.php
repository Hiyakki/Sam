<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'function',
        'indication',
        'meta_title',
        'meta_description',
        'photo',
        'is_active',
        'sort_order',
        'price',
    ];

    protected static function booted(): void
    {
        static::updating(function (Product $product) {
            if ($product->isDirty('slug') && $product->getOriginal('slug')) {
                $oldPath = '/katalog/' . $product->getOriginal('slug');
                $newPath = '/katalog/' . $product->slug;

                if ($oldPath !== $newPath) {
                    Redirect::updateOrCreate(
                        ['source_path' => $oldPath],
                        ['target_path' => $newPath, 'status_code' => 301]
                    );

                    // Pastikan tidak ada redirect chain (hanya 1 lompatan)
                    Redirect::where('target_path', $oldPath)
                        ->update(['target_path' => $newPath]);
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}
