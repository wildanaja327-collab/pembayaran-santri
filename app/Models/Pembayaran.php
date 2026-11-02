<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'id_tagihan',
        'tgl_bayar',
        'jumlah_bayar',
        'biaya_admin',
        'total_bayar',
        'metode_bayar',
        'channel_bayar',
        'va_number',
        'snap_token',
        'status_transaksi',
        'settlement_time',
        'gateway_response',
        'verified_by',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }

    public function kwitansi()
    {
        return $this->hasOne(Kwitansi::class, 'id_pembayaran', 'id_pembayaran');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }
}

