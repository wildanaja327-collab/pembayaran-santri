<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $table = 'santri';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nis',
        'nisn',
        'nama_santri',
        'tingkat',
        'kelas',
        'jenis_kelamin',
        'tgl_lahir',
        'id_program',
        'id_wali',
        'status_aktif',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'id_program', 'id_program');
    }

    public function wali()
    {
        return $this->belongsTo(User::class, 'id_wali', 'id_user');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'nis', 'nis');
    }

    public function notifikasiWa()
    {
        return $this->hasMany(NotifikasiWa::class, 'nis', 'nis');
    }

    /**
     * Dapatkan analisis status kepatuhan pembayaran santri yang akurat
     */
    public function getKepatuhanInfo(): array
    {
        $tagihan = $this->tagihan;
        $totalCount = $tagihan->count();
        $totalTagihan = (float) $tagihan->sum('nominal_tagihan');
        $totalTerbayar = (float) $tagihan->sum('total_terbayar');
        $totalSisa = (float) $tagihan->sum('sisa_tagihan');

        $nowStr = now()->toDateString();
        $in7DaysStr = now()->addDays(7)->toDateString();

        $tagihanBelumLunas = $tagihan->whereIn('status_tagihan', ['belum_bayar', 'dicicil']);

        // Tagihan yang sudah melewati tanggal jatuh tempo (BENAR-BENAR MENUNGGAK)
        $overdueCount = $tagihanBelumLunas->filter(function ($t) use ($nowStr) {
            return !empty($t->jatuh_tempo) && $t->jatuh_tempo < $nowStr;
        })->count();

        // Tagihan yang mendekati jatuh tempo (1 - 7 hari ke depan)
        $soonCount = $tagihanBelumLunas->filter(function ($t) use ($nowStr, $in7DaysStr) {
            return !empty($t->jatuh_tempo) && $t->jatuh_tempo >= $nowStr && $t->jatuh_tempo <= $in7DaysStr;
        })->count();

        // 1. Jika tidak ada tagihan sama sekali (bebas biaya / santri baru)
        if ($totalCount === 0 || $totalTagihan <= 0) {
            return [
                'persen' => 100,
                'status' => 'Tertib',
                'sub' => 'Bebas Tagihan',
                'color' => 'success',
                'badge_bg' => 'bg-success',
                'icon' => 'bi-patch-check-fill',
            ];
        }

        $persen = round(($totalTerbayar / $totalTagihan) * 100);

        // 2. Jika seluruh tagihan sudah lunas 100%
        if ($totalSisa <= 0) {
            return [
                'persen' => 100,
                'status' => 'Tertib',
                'sub' => '100% Lunas',
                'color' => 'success',
                'badge_bg' => 'bg-success',
                'icon' => 'bi-patch-check-fill',
            ];
        }

        // 3. Ada tagihan yang sudah lewat batas waktu (Menunggak)
        if ($overdueCount > 0) {
            return [
                'persen' => $persen,
                'status' => 'Menunggak',
                'sub' => "{$overdueCount} Tagihan Lewat Tempo",
                'color' => 'danger',
                'badge_bg' => 'bg-danger',
                'icon' => 'bi-exclamation-triangle-fill',
            ];
        }

        // 4. Ada tagihan mendekati jatuh tempo (H-7)
        if ($soonCount > 0) {
            return [
                'persen' => $persen,
                'status' => 'Perlu Perhatian',
                'sub' => 'Mendekati Jatuh Tempo',
                'color' => 'warning',
                'badge_bg' => 'bg-warning',
                'icon' => 'bi-hourglass-split',
            ];
        }

        // 5. Ada tagihan aktif berjalan (belum jatuh tempo)
        return [
            'persen' => $persen,
            'status' => $persen >= 50 ? 'Tertib' : 'Lancar',
            'sub' => "{$persen}% Terbayar",
            'color' => 'success',
            'badge_bg' => 'bg-success',
            'icon' => 'bi-emoji-smile-fill',
        ];
    }
}

