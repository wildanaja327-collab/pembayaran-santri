<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun_ajaran';
    public $timestamps = false;

    protected $fillable = [
        'nama_tahun_ajaran',
        'semester',
        'tgl_mulai',
        'tgl_selesai',
        'is_aktif',
        'created_at',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public static function getAktif()
    {
        return self::where('is_aktif', 1)->first() ?? self::latest('id_tahun_ajaran')->first();
    }
}

