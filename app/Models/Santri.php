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
}

