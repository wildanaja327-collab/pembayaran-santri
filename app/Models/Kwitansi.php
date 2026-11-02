<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    use HasFactory;

    protected $table = 'kwitansi';
    protected $primaryKey = 'id_kwitansi';
    public $timestamps = false;

    protected $fillable = [
        'no_kwitansi',
        'id_pembayaran',
        'tgl_terbit',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'id_pembayaran', 'id_pembayaran');
    }
}

