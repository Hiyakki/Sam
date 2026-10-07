<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'site_settings.all';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    protected static function booted(): void
    {
        // Perubahan dari admin langsung tercermin di halaman publik.
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Semua setting (key => nilai ter-decode), di-cache.
     */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()
            ->get()
            ->mapWithKeys(fn (SiteSetting $item) => [$item->key => $item->decodedValue()])
            ->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allCached()[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $type = 'text', string $group = 'general'): self
    {
        $valToStore = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valToStore,
                'type' => is_array($value) ? 'json' : $type,
                'group' => $group,
            ]
        );
    }

    public static function getGroup(string $group): array
    {
        $prefix = $group.'.';

        return collect(static::allCached())
            ->filter(fn ($value, $key) => str_starts_with($key, $prefix))
            ->all();
    }

    public function decodedValue(): mixed
    {
        return $this->type === 'json' ? json_decode((string) $this->value, true) : $this->value;
    }
}
