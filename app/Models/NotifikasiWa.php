<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiWa extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_wa';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'nis',
        'no_tujuan',
        'pesan',
        'jenis_pesan',
        'status_kirim',
        'tgl_kirim',
        'response_api',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
}

