<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        if ($setting->type === 'json') {
            return json_decode($setting->value, true) ?? $default;
        }

        return $setting->value ?? $default;
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
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(function ($item) {
                $val = $item->type === 'json' ? json_decode($item->value, true) : $item->value;

                return [$item->key => $val];
            })
            ->toArray();
    }
}
