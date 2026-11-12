<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTagihan extends Model
{
    use HasFactory;

    protected $table = 'jenis_tagihan';
    protected $primaryKey = 'id_jenis_tagihan';
    public $timestamps = false;

    protected $fillable = [
        'nama_tagihan',
        'tipe',
        'nominal_standar',
        'keterangan',
    ];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_jenis_tagihan', 'id_jenis_tagihan');
    }
}

