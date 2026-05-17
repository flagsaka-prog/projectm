<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache; // <-- Tambahkan ini

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        // Coba ambil dari Cache dulu. Jika tidak ada, baru query ke Database.
        // Cache akan disimpan selama 24 jam (addDay).
        return Cache::remember('setting_' . $key, now()->addDay(), function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);

        // Hapus cache lama agar saat di-get lagi, dia mengambil data terbaru dari DB
        Cache::forget('setting_' . $key);
    }
}
