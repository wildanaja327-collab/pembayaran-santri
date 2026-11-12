<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';
    protected $primaryKey = 'setting_key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'label',
        'kategori',
        'updated_at',
    ];

    /**
     * Dapatkan nilai pengaturan berdasarkan key dengan default fallback
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Simpan / perbarui nilai pengaturan
     */
    public static function set(string $key, ?string $value, ?string $label = null, string $kategori = 'umum'): self
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'label' => $label ?? ucwords(str_replace('_', ' ', $key)),
                'kategori' => $kategori,
                'updated_at' => now(),
            ]
        );
    }
}

