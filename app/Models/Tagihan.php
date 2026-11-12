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
        'kategori_tunggakan',
        'alasan_tunggakan',
        'tgl_janji_bayar',
        'catatan_bendahara',
        'updated_alasan_at',
    ];

    public const KATEGORI_TUNGGAKAN = [
        'panen' => [
            'label' => 'Menunggu Hasil Panen',
            'icon' => 'bi-flower1',
            'class' => 'bg-success text-white',
            'deskripsi' => 'Wali santri menunggu musim panen/hasil komoditas tani',
        ],
        'gaji_tertunda' => [
            'label' => 'Gaji / Kiriman Tertunda',
            'icon' => 'bi-briefcase-fill',
            'class' => 'bg-info text-dark',
            'deskripsi' => 'Menunggu pencairan gaji bulanan atau kiriman wali perantauan',
        ],
        'keringanan' => [
            'label' => 'Dispensasi / Keringanan',
            'icon' => 'bi-award-fill',
            'class' => 'bg-primary text-white',
            'deskripsi' => 'Izin penundaan atau permohonan keringanan yang disetujui pimpinan',
        ],
        'musibah' => [
            'label' => 'Sakit / Musibah Keluarga',
            'icon' => 'bi-heart-pulse-fill',
            'class' => 'bg-danger text-white',
            'deskripsi' => 'Terkendala musibah keluarga, rawat inap, atau kendala darurat',
        ],
        'janji_bayar' => [
            'label' => 'Janji Bayar',
            'icon' => 'bi-calendar-check-fill',
            'class' => 'bg-warning text-dark',
            'deskripsi' => 'Wali berkomitmen melunasi pada tanggal tertentu',
        ],
        'belum_respon' => [
            'label' => 'Belum Ada Konfirmasi',
            'icon' => 'bi-telephone-x-fill',
            'class' => 'bg-secondary text-white',
            'deskripsi' => 'Sudah dikirimkan pengingat namun belum ada respon dari wali',
        ],
        'lainnya' => [
            'label' => 'Lainnya',
            'icon' => 'bi-chat-left-dots-fill',
            'class' => 'bg-dark text-white',
            'deskripsi' => 'Catatan khusus lainnya dari bendahara pondok',
        ],
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

    public function getKategoriInfoAttribute()
    {
        return self::KATEGORI_TUNGGAKAN[$this->kategori_tunggakan] ?? null;
    }

    public function getKategoriBadgeAttribute()
    {
        $info = $this->kategori_info;
        if (!$info) {
            return '<span class="badge bg-light text-muted border"><i class="bi bi-question-circle me-1"></i>Belum Ada Catatan</span>';
        }

        return '<span class="badge ' . $info['class'] . ' shadow-sm"><i class="bi ' . $info['icon'] . ' me-1"></i>' . e($info['label']) . '</span>';
    }

    public function getAlasanLengkapAttribute()
    {
        $parts = [];
        if ($this->kategori_info) {
            $parts[] = $this->kategori_info['label'];
        }
        if ($this->alasan_tunggakan) {
            $parts[] = $this->alasan_tunggakan;
        }
        if ($this->tgl_janji_bayar) {
            $parts[] = 'Janji Bayar: ' . date('d/m/Y', strtotime($this->tgl_janji_bayar));
        }

        return empty($parts) ? '-' : implode(' — ', $parts);
    }
}

