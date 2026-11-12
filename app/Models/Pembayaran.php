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

    /**
     * Dapatkan token keamanan acak (HMAC-SHA256) untuk verifikasi akses publik kwitansi tanpa login
     */
    public function getKwitansiToken(): string
    {
        return substr(hash_hmac('sha256', "kwt-{$this->id_pembayaran}-{$this->order_id}", config('app.key')), 0, 32);
    }

    /**
     * Dapatkan URL publik resmi kwitansi (otomatis mendeteksi Ngrok/domain publik aktif dan token keamanan)
     */
    public function getPublicKwitansiUrl(): string
    {
        $baseUrl = \App\Services\WhatsAppService::getPublicBaseUrl();
        $token = $this->getKwitansiToken();

        return "{$baseUrl}/kwitansi/{$this->id_pembayaran}/cetak?token={$token}";
    }
}

