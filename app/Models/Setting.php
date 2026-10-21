<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function all_(): array
    {
        return Cache::rememberForever('settings', fn() => static::pluck('value', 'key')->all());
    }

    public static function get(string $key, $default = null)
    {
        return static::all_()[$key] ?? $default;
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings');
    }
}
