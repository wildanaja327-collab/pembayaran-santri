<?php

namespace Database\Seeders;

use App\Models\JenisTagihan;
use App\Models\Kwitansi;
use App\Models\Pembayaran;
use App\Models\Program;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SantriDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Program Unggulan
        $programs = [
            ['id_program' => 'PRG-TFZ', 'nama_program' => 'Program Tahfidzul Qur\'an', 'keterangan' => 'Fokus hafalan Al-Qur\'an 30 Juz'],
            ['id_program' => 'PRG-ARB', 'nama_program' => 'Pendalaman Bahasa Arab', 'keterangan' => 'Kemahiran percakapan & nahu shorof bahasa Arab'],
            ['id_program' => 'PRG-ING', 'nama_program' => 'Pendalaman Bahasa Inggris', 'keterangan' => 'Kemahiran percakapan & akademik bahasa Inggris'],
        ];
        foreach ($programs as $prg) {
            Program::firstOrCreate(['id_program' => $prg['id_program']], $prg);
        }

        // 2. Akun Bendahara
        User::firstOrCreate(
            ['username' => 'bendahara'],
            [
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Ahmad Fauzi, S.E. (Bendahara Pesantren)',
                'no_telp' => '081234567890',
                'role' => 'bendahara',
            ]
        );

        // 3. Akun Wali Demo 1 & 2
        $wali1 = User::firstOrCreate(
            ['username' => 'wali_2026001'],
            [
                'password' => Hash::make('2026001'),
                'nama_lengkap' => 'H. Bambang Sulistyo',
                'no_telp' => '081398765432',
                'role' => 'wali_santri',
            ]
        );

        $wali2 = User::firstOrCreate(
            ['username' => 'wali_2026002'],
            [
                'password' => Hash::make('2026002'),
                'nama_lengkap' => 'Drs. Supriyadi, M.Pd',
                'no_telp' => '085211223344',
                'role' => 'wali_santri',
            ]
        );

        $wali3 = User::firstOrCreate(
            ['username' => 'wali_2026003'],
            [
                'password' => Hash::make('2026003'),
                'nama_lengkap' => 'Ir. Gunawan Wibowo',
                'no_telp' => '087812345678',
                'role' => 'wali_santri',
            ]
        );

        // 4. Data Santri Sampel
        $santriData = [
            [
                'nis' => '2026001',
                'nisn' => '0087654321',
                'nama_santri' => 'Muhammad Rayhan Pratama',
                'tingkat' => 'SMP',
                'kelas' => 'VII A (Abu Bakar)',
                'jenis_kelamin' => 'L',
                'tgl_lahir' => '2012-05-14',
                'id_program' => 'PRG-TFZ',
                'id_wali' => $wali1->id_user,
                'status_aktif' => 'aktif',
            ],
            [
                'nis' => '2026002',
                'nisn' => '0087654322',
                'nama_santri' => 'Fatimah Az-Zahra',
                'tingkat' => 'SMP',
                'kelas' => 'VIII B (Aisyah)',
                'jenis_kelamin' => 'P',
                'tgl_lahir' => '2011-08-20',
                'id_program' => 'PRG-ARB',
                'id_wali' => $wali2->id_user,
                'status_aktif' => 'aktif',
            ],
            [
                'nis' => '2026003',
                'nisn' => '0087654323',
                'nama_santri' => 'Ali Zainal Abidin',
                'tingkat' => 'SMA',
                'kelas' => 'X MIPA (Umar bin Khattab)',
                'jenis_kelamin' => 'L',
                'tgl_lahir' => '2009-02-10',
                'id_program' => 'PRG-ING',
                'id_wali' => $wali3->id_user,
                'status_aktif' => 'aktif',
            ],
            [
                'nis' => '2026004',
                'nisn' => '0087654324',
                'nama_santri' => 'Aisyah Putri Sulistyo',
                'tingkat' => 'SMA',
                'kelas' => 'XI IPS (Khadijah)',
                'jenis_kelamin' => 'P',
                'tgl_lahir' => '2008-11-25',
                'id_program' => 'PRG-TFZ',
                'id_wali' => $wali1->id_user, // Saudara kandung dari Rayhan!
                'status_aktif' => 'aktif',
            ],
        ];

        foreach ($santriData as $data) {
            Santri::firstOrCreate(['nis' => $data['nis']], $data);
        }

        // 5. Master Jenis Tagihan
        $spp = JenisTagihan::firstOrCreate(
            ['nama_tagihan' => 'Syahriah (SPP Bulanan)'],
            ['tipe' => 'bulanan', 'nominal_standar' => 500000.00, 'keterangan' => 'SPP dan operasional santri']
        );
        $munaqosyah = JenisTagihan::firstOrCreate(
            ['nama_tagihan' => 'Biaya Munaqosyah'],
            ['tipe' => 'insidental', 'nominal_standar' => 350000.00, 'keterangan' => 'Ujian kelulusan tahfidz/kitab']
        );
        $gedung = JenisTagihan::firstOrCreate(
            ['nama_tagihan' => 'Uang Pangkal / Pembangunan'],
            ['tipe' => 'insidental', 'nominal_standar' => 2500000.00, 'keterangan' => 'Pengembangan fasilitas asrama']
        );

        // 6. Buat Contoh Tagihan & Transaksi Lunas dengan Kwitansi Resmi
        $tagihan1 = Tagihan::firstOrCreate(
            ['nis' => '2026001', 'id_jenis_tagihan' => $spp->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => 5],
            [
                'nominal_tagihan' => 500000.00,
                'total_terbayar' => 500000.00,
                'sisa_tagihan' => 0.00,
                'jatuh_tempo' => '2026-05-10',
                'status_tagihan' => 'lunas',
            ]
        );

        $pembayaran1 = Pembayaran::firstOrCreate(
            ['order_id' => 'DMTQ-DEMO-001'],
            [
                'id_tagihan' => $tagihan1->id_tagihan,
                'tgl_bayar' => now()->subDays(2),
                'jumlah_bayar' => 500000.00,
                'biaya_admin' => 2500.00,
                'total_bayar' => 502500.00,
                'metode_bayar' => 'gateway',
                'channel_bayar' => 'BCA Virtual Account',
                'va_number' => '8808123456789001',
                'snap_token' => 'DEMO-TOKEN-001',
                'status_transaksi' => 'settlement',
                'settlement_time' => now()->subDays(2),
            ]
        );

        Kwitansi::firstOrCreate(
            ['id_pembayaran' => $pembayaran1->id_pembayaran],
            [
                'no_kwitansi' => 'KWT/' . date('Ym') . '/0001',
                'tgl_terbit' => now()->subDays(2),
            ]
        );

        // Tagihan 2: Belum Bayar (Rayhan SPP Bulan Juni)
        Tagihan::firstOrCreate(
            ['nis' => '2026001', 'id_jenis_tagihan' => $spp->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => 6],
            [
                'nominal_tagihan' => 500000.00,
                'total_terbayar' => 0.00,
                'sisa_tagihan' => 500000.00,
                'jatuh_tempo' => '2026-06-10',
                'status_tagihan' => 'belum_bayar',
            ]
        );

        // Tagihan 3: Cicilan (Fatimah Uang Pangkal)
        Tagihan::firstOrCreate(
            ['nis' => '2026002', 'id_jenis_tagihan' => $gedung->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => null],
            [
                'nominal_tagihan' => 2500000.00,
                'total_terbayar' => 1000000.00,
                'sisa_tagihan' => 1500000.00,
                'jatuh_tempo' => '2026-06-30',
                'status_tagihan' => 'dicicil',
            ]
        );

        // Tagihan 4: Belum Bayar (Ali Biaya Munaqosyah)
        Tagihan::firstOrCreate(
            ['nis' => '2026003', 'id_jenis_tagihan' => $munaqosyah->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => null],
            [
                'nominal_tagihan' => 350000.00,
                'total_terbayar' => 0.00,
                'sisa_tagihan' => 350000.00,
                'jatuh_tempo' => '2026-05-20',
                'status_tagihan' => 'belum_bayar',
            ]
        );
    }
}

