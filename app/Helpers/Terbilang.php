<?php

namespace App\Helpers;

class Terbilang
{
    private static $bilangan = [
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
        'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
    ];

    public static function make($nilai): string
    {
        $nilai = abs((float)$nilai);
        $huruf = '';

        if ($nilai < 12) {
            $huruf = ' ' . self::$bilangan[(int)$nilai];
        } elseif ($nilai < 20) {
            $huruf = self::make($nilai - 10) . ' Belas';
        } elseif ($nilai < 100) {
            $huruf = self::make($nilai / 10) . ' Puluh ' . self::make(fmod($nilai, 10));
        } elseif ($nilai < 200) {
            $huruf = ' Seratus ' . self::make($nilai - 100);
        } elseif ($nilai < 1000) {
            $huruf = self::make($nilai / 100) . ' Ratus ' . self::make(fmod($nilai, 100));
        } elseif ($nilai < 2000) {
            $huruf = ' Seribu ' . self::make($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $huruf = self::make($nilai / 1000) . ' Ribu ' . self::make(fmod($nilai, 1000));
        } elseif ($nilai < 1000000000) {
            $huruf = self::make($nilai / 1000000) . ' Juta ' . self::make(fmod($nilai, 1000000));
        } elseif ($nilai < 1000000000000) {
            $huruf = self::make($nilai / 1000000000) . ' Miliar ' . self::make(fmod($nilai, 1000000000));
        } elseif ($nilai < 1000000000000000) {
            $huruf = self::make($nilai / 1000000000000) . ' Triliun ' . self::make(fmod($nilai, 1000000000000));
        }

        return trim(preg_replace('/\s+/', ' ', $huruf));
    }

    public static function rupiah($nilai): string
    {
        if ($nilai <= 0) {
            return 'Nol Rupiah';
        }
        return self::make($nilai) . ' Rupiah';
    }
}
