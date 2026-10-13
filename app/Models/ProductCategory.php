<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'intro_text',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::updating(function (ProductCategory $category) {
            if ($category->isDirty('slug') && $category->getOriginal('slug')) {
                $oldPath = '/katalog?kategori=' . $category->getOriginal('slug');
                $newPath = '/katalog?kategori=' . $category->slug;

                if ($oldPath !== $newPath) {
                    Redirect::updateOrCreate(
                        ['source_path' => $oldPath],
                        ['target_path' => $newPath, 'status_code' => 301]
                    );

                    Redirect::where('target_path', $oldPath)
                        ->update(['target_path' => $newPath]);
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->orderBy('sort_order', 'asc');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id')
            ->orderBy('sort_order', 'asc');
    }

    public function scopeRoots(Builder $query): void
    {
        $query->whereNull('parent_id')->orderBy('sort_order', 'asc');
    }
}
