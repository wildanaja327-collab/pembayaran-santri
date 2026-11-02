<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    protected $primaryKey = 'id_tagihan';
    public $timestamps = false;

    protected $fillable = [
        'nis',
        'id_jenis_tagihan',
        'tahun_ajaran',
        'bulan',
        'nominal_tagihan',
        'total_terbayar',
        'sisa_tagihan',
        'jatuh_tempo',
        'status_tagihan',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }

    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class, 'id_jenis_tagihan', 'id_jenis_tagihan');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_tagihan', 'id_tagihan');
    }

    public function getNamaBulanAttribute()
    {
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $bulanList[$this->bulan] ?? '-';
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status_tagihan) {
            'lunas' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lunas</span>',
            'dicicil' => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Dicicil</span>',
            'menunggu_verifikasi' => '<span class="badge bg-info text-dark"><i class="bi bi-clock me-1"></i>Menunggu Verifikasi</span>',
            default => '<span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>Belum Bayar</span>',
        };
    }
}

