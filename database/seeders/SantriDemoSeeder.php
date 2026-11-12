<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\JenisTagihan;
use App\Models\Kwitansi;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Program;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SantriDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tahun Ajaran Master
        $ta1 = TahunAjaran::firstOrCreate(
            ['nama_tahun_ajaran' => '2025/2026', 'semester' => 'Genap'],
            ['tgl_mulai' => '2026-01-01', 'tgl_selesai' => '2026-06-30', 'is_aktif' => 1]
        );
        $ta2 = TahunAjaran::firstOrCreate(
            ['nama_tahun_ajaran' => '2025/2026', 'semester' => 'Ganjil'],
            ['tgl_mulai' => '2025-07-01', 'tgl_selesai' => '2025-12-31', 'is_aktif' => 0]
        );
        $ta3 = TahunAjaran::firstOrCreate(
            ['nama_tahun_ajaran' => '2024/2025', 'semester' => 'Genap'],
            ['tgl_mulai' => '2025-01-01', 'tgl_selesai' => '2025-06-30', 'is_aktif' => 0]
        );

        // 2. Program Unggulan
        $programs = [
            ['id_program' => 'PRG-TFZ', 'nama_program' => 'Program Tahfidzul Qur\'an', 'keterangan' => 'Fokus hafalan Al-Qur\'an 30 Juz'],
            ['id_program' => 'PRG-ARB', 'nama_program' => 'Pendalaman Bahasa Arab', 'keterangan' => 'Kemahiran percakapan & nahu shorof bahasa Arab'],
            ['id_program' => 'PRG-ING', 'nama_program' => 'Pendalaman Bahasa Inggris', 'keterangan' => 'Kemahiran percakapan & akademik bahasa Inggris'],
        ];
        foreach ($programs as $prg) {
            Program::firstOrCreate(['id_program' => $prg['id_program']], $prg);
        }

        // 3. Akun Administrator Sistem
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Administrator IT Pesantren',
                'no_telp' => '081122334455',
                'role' => 'admin',
                'is_active' => 1,
            ]
        );

        // 4. Akun Bendahara Keuangan
        User::updateOrCreate(
            ['username' => 'bendahara'],
            [
                'password' => Hash::make('bendahara123'),
                'nama_lengkap' => 'Ahmad Fauzi, S.E. (Bendahara Pesantren)',
                'no_telp' => '081234567890',
                'role' => 'bendahara',
                'is_active' => 1,
            ]
        );

        // 5. Master Jenis Tagihan
        $spp = JenisTagihan::firstOrCreate(
            ['nama_tagihan' => 'Syahriah (SPP Bulanan)'],
            ['tipe' => 'bulanan', 'nominal_standar' => 500000.00, 'keterangan' => 'SPP dan konsumsi santri bulanan']
        );
        $munaqosyah = JenisTagihan::firstOrCreate(
            ['nama_tagihan' => 'Biaya Munaqosyah'],
            ['tipe' => 'insidental', 'nominal_standar' => 350000.00, 'keterangan' => 'Ujian kelulusan tahfidz dan kitab kuning']
        );
        $gedung = JenisTagihan::firstOrCreate(
            ['nama_tagihan' => 'Uang Pangkal / Pembangunan'],
            ['tipe' => 'insidental', 'nominal_standar' => 2500000.00, 'keterangan' => 'Pengembangan sarana & prasarana asrama']
        );

        // 6. Daftar Wali Santri (37 Akun Wali, beberapa Wali memiliki 2 atau 3 anak santri)
        $waliTemplates = [
            1 => ['nama' => 'H. Bambang Sulistyo', 'telp' => '081398765432'], // Wali dari Rayhan & Aisyah (2 anak)
            2 => ['nama' => 'Drs. Supriyadi, M.Pd', 'telp' => '085211223344'], // Wali dari Fatimah & Farhan (2 anak)
            3 => ['nama' => 'Ir. Gunawan Wibowo', 'telp' => '087812345678'], // Wali dari Ali & Zahra (2 anak)
            4 => ['nama' => 'KH. Ahmad Dahlan Al-Hafidz', 'telp' => '081223344556'], // Wali dari Zaid & Maryam (2 anak)
            5 => ['nama' => 'Drs. H. Suryono, M.Si', 'telp' => '085334455667'], // Wali dari Ibrahim, Ismail, & Ishaq (3 anak!)
            6 => ['nama' => 'H. Abdul Rasyid', 'telp' => '081234500006'],
            7 => ['nama' => 'Dedi Setiawan, S.Pd', 'telp' => '081234500007'],
            8 => ['nama' => 'H. Muhammad Ridho', 'telp' => '081234500008'],
            9 => ['nama' => 'Dr. H. Hendra Kurniawan', 'telp' => '081234500009'],
            10 => ['nama' => 'Agus Priyanto, S.E.', 'telp' => '081234500010'],
            11 => ['nama' => 'H. Wahyu Hidayat', 'telp' => '081234500011'],
            12 => ['nama' => 'Budi Santoso', 'telp' => '081234500012'],
            13 => ['nama' => 'H. Firman Utina', 'telp' => '081234500013'],
            14 => ['nama' => 'Drs. Usman Efendi', 'telp' => '081234500014'],
            15 => ['nama' => 'H. Arif Rahman', 'telp' => '081234500015'],
            16 => ['nama' => 'H. Syamsul Ma\'arif', 'telp' => '081234500016'],
            17 => ['nama' => 'Joko Susilo, M.Kom', 'telp' => '081234500017'],
            18 => ['nama' => 'H. Harun Al-Rasyid', 'telp' => '081234500018'],
            19 => ['nama' => 'H. Lukman Hakim', 'telp' => '081234500019'],
            20 => ['nama' => 'Drs. H. Basri Hasan', 'telp' => '081234500020'],
            21 => ['nama' => 'H. Sofyan Hadi', 'telp' => '081234500021'],
            22 => ['nama' => 'Subagyo, S.H.', 'telp' => '081234500022'],
            23 => ['nama' => 'H. Mahfud Shidiq', 'telp' => '081234500023'],
            24 => ['nama' => 'Taufik Hidayat, M.Pd', 'telp' => '081234500024'],
            25 => ['nama' => 'H. Faisal Amir', 'telp' => '081234500025'],
            26 => ['nama' => 'Kurniawan Dwi, S.T.', 'telp' => '081234500026'],
            27 => ['nama' => 'H. Rudi Hartono', 'telp' => '081234500027'],
            28 => ['nama' => 'Mochamad Zaini', 'telp' => '081234500028'],
            29 => ['nama' => 'H. Hamzah Fansuri', 'telp' => '081234500029'],
            30 => ['nama' => 'Edi Sudrajat', 'telp' => '081234500030'],
            31 => ['nama' => 'H. Danang Wicaksono', 'telp' => '081234500031'],
            32 => ['nama' => 'Drs. Anwar Fuadi', 'telp' => '081234500032'],
            33 => ['nama' => 'H. Syaifullah', 'telp' => '081234500033'],
            34 => ['nama' => 'Bambang Irawan', 'telp' => '081234500034'],
            35 => ['nama' => 'H. Tri Handoko', 'telp' => '081234500035'],
            36 => ['nama' => 'Sudirman, S.Ag', 'telp' => '081234500036'],
            37 => ['nama' => 'H. Muchtar Luthfi', 'telp' => '081234500037'],
        ];

        $waliUsers = [];
        foreach ($waliTemplates as $wIndex => $wData) {
            $username = 'wali_' . sprintf('%04d', $wIndex);
            $waliUsers[$wIndex] = User::updateOrCreate(
                ['username' => $username],
                [
                    'password' => Hash::make('wali123'),
                    'nama_lengkap' => $wData['nama'],
                    'no_telp' => $wData['telp'],
                    'role' => 'wali_santri',
                    'is_active' => 1,
                ]
            );
        }

        // 7. Daftar 50 Santri Lengkap (SMP & SMA, Beragam Program, Relasi Sibling)
        $rawSantri = [
            // 1-4 Santri Demo Asli (dipertahankan)
            ['nis' => '2026001', 'nama' => 'Muhammad Rayhan Pratama', 'tingkat' => 'SMP', 'kelas' => 'VII A (Abu Bakar)', 'jk' => 'L', 'tgl' => '2012-05-14', 'prg' => 'PRG-TFZ', 'wali' => 1],
            ['nis' => '2026002', 'nama' => 'Fatimah Az-Zahra', 'tingkat' => 'SMP', 'kelas' => 'VIII B (Aisyah)', 'jk' => 'P', 'tgl' => '2011-08-20', 'prg' => 'PRG-ARB', 'wali' => 2],
            ['nis' => '2026003', 'nama' => 'Ali Zainal Abidin', 'tingkat' => 'SMA', 'kelas' => 'X MIPA (Umar bin Khattab)', 'jk' => 'L', 'tgl' => '2009-02-10', 'prg' => 'PRG-ING', 'wali' => 3],
            ['nis' => '2026004', 'nama' => 'Aisyah Putri Sulistyo', 'tingkat' => 'SMA', 'kelas' => 'XI IPS (Khadijah)', 'jk' => 'P', 'tgl' => '2008-11-25', 'prg' => 'PRG-TFZ', 'wali' => 1], // Kakak Rayhan (Wali 1)

            // 5-10: Sibling Santri untuk Validasi Relasi Wali
            ['nis' => '2026005', 'nama' => 'Muhammad Farhan Supriyadi', 'tingkat' => 'SMA', 'kelas' => 'X MIPA (Umar bin Khattab)', 'jk' => 'L', 'tgl' => '2009-04-12', 'prg' => 'PRG-ARB', 'wali' => 2], // Adik Fatimah (Wali 2)
            ['nis' => '2026006', 'nama' => 'Zahra Annisa Wibowo', 'tingkat' => 'SMP', 'kelas' => 'VII B (Fatimah)', 'jk' => 'P', 'tgl' => '2012-10-18', 'prg' => 'PRG-ING', 'wali' => 3], // Adik Ali (Wali 3)
            ['nis' => '2026007', 'nama' => 'Zaid bin Ahmad Dahlan', 'tingkat' => 'SMP', 'kelas' => 'VIII A (Utsman)', 'jk' => 'L', 'tgl' => '2011-03-15', 'prg' => 'PRG-TFZ', 'wali' => 4],
            ['nis' => '2026008', 'nama' => 'Maryam binti Ahmad Dahlan', 'tingkat' => 'SMA', 'kelas' => 'XI MIPA (Aisyah)', 'jk' => 'P', 'tgl' => '2008-07-22', 'prg' => 'PRG-TFZ', 'wali' => 4], // Saudara Zaid (Wali 4)
            ['nis' => '2026009', 'nama' => 'Ibrahim Suryono', 'tingkat' => 'SMA', 'kelas' => 'XII MIPA (Ali)', 'jk' => 'L', 'tgl' => '2007-01-10', 'prg' => 'PRG-ARB', 'wali' => 5], // Anak 1 Wali 5
            ['nis' => '2026010', 'nama' => 'Ismail Suryono', 'tingkat' => 'SMA', 'kelas' => 'X MIPA (Umar)', 'jk' => 'L', 'tgl' => '2009-09-09', 'prg' => 'PRG-ARB', 'wali' => 5], // Anak 2 Wali 5
            ['nis' => '2026011', 'nama' => 'Ishaq Suryono', 'tingkat' => 'SMP', 'kelas' => 'VII A (Abu Bakar)', 'jk' => 'L', 'tgl' => '2012-12-01', 'prg' => 'PRG-TFZ', 'wali' => 5], // Anak 3 Wali 5 (Tiga Bersaudara!)

            // 12-25: Santri SMP (Kelas VII, VIII, IX)
            ['nis' => '2026012', 'nama' => 'Abdullah Rasyid', 'tingkat' => 'SMP', 'kelas' => 'VII A (Abu Bakar)', 'jk' => 'L', 'tgl' => '2012-06-11', 'prg' => 'PRG-TFZ', 'wali' => 6],
            ['nis' => '2026013', 'nama' => 'Bilal Dedi Pratama', 'tingkat' => 'SMP', 'kelas' => 'VII B (Ali)', 'jk' => 'L', 'tgl' => '2012-08-19', 'prg' => 'PRG-ARB', 'wali' => 7],
            ['nis' => '2026014', 'nama' => 'Hasan Al-Banna Ridho', 'tingkat' => 'SMP', 'kelas' => 'VII B (Ali)', 'jk' => 'L', 'tgl' => '2012-03-05', 'prg' => 'PRG-ING', 'wali' => 8],
            ['nis' => '2026015', 'nama' => 'Husain Kurniawan', 'tingkat' => 'SMP', 'kelas' => 'VIII A (Utsman)', 'jk' => 'L', 'tgl' => '2011-04-14', 'prg' => 'PRG-TFZ', 'wali' => 9],
            ['nis' => '2026016', 'nama' => 'Hamzah Priyanto', 'tingkat' => 'SMP', 'kelas' => 'VIII A (Utsman)', 'jk' => 'L', 'tgl' => '2011-11-20', 'prg' => 'PRG-ARB', 'wali' => 10],
            ['nis' => '2026017', 'nama' => 'Khalid Wahyu', 'tingkat' => 'SMP', 'kelas' => 'VIII B (Zaid)', 'jk' => 'L', 'tgl' => '2011-09-17', 'prg' => 'PRG-ING', 'wali' => 11],
            ['nis' => '2026018', 'nama' => 'Salman Al-Farisi Santoso', 'tingkat' => 'SMP', 'kelas' => 'VIII B (Zaid)', 'jk' => 'L', 'tgl' => '2011-02-28', 'prg' => 'PRG-TFZ', 'wali' => 12],
            ['nis' => '2026019', 'nama' => 'Thariq Firman Utina', 'tingkat' => 'SMP', 'kelas' => 'IX A (Thalhah)', 'jk' => 'L', 'tgl' => '2010-05-15', 'prg' => 'PRG-ARB', 'wali' => 13],
            ['nis' => '2026020', 'nama' => 'Usamah bin Usman', 'tingkat' => 'SMP', 'kelas' => 'IX A (Thalhah)', 'jk' => 'L', 'tgl' => '2010-10-03', 'prg' => 'PRG-ING', 'wali' => 14],
            ['nis' => '2026021', 'nama' => 'Khadijah Rahman', 'tingkat' => 'SMP', 'kelas' => 'VII A (Aisyah)', 'jk' => 'P', 'tgl' => '2012-01-22', 'prg' => 'PRG-TFZ', 'wali' => 15],
            ['nis' => '2026022', 'nama' => 'Hafshah Syamsul', 'tingkat' => 'SMP', 'kelas' => 'VII B (Hafshah)', 'jk' => 'P', 'tgl' => '2012-07-08', 'prg' => 'PRG-ARB', 'wali' => 16],
            ['nis' => '2026023', 'nama' => 'Ummu Salamah Susilo', 'tingkat' => 'SMP', 'kelas' => 'VIII A (Khadijah)', 'jk' => 'P', 'tgl' => '2011-05-19', 'prg' => 'PRG-ING', 'wali' => 17],
            ['nis' => '2026024', 'nama' => 'Zainab Al-Rasyid', 'tingkat' => 'SMP', 'kelas' => 'VIII B (Maryam)', 'jk' => 'P', 'tgl' => '2011-12-12', 'prg' => 'PRG-TFZ', 'wali' => 18],
            ['nis' => '2026025', 'nama' => 'Ruqayyah Hakim', 'tingkat' => 'SMP', 'kelas' => 'IX B (Fatimah)', 'jk' => 'P', 'tgl' => '2010-08-30', 'prg' => 'PRG-ARB', 'wali' => 19],

            // 26-50: Santri SMA (Kelas X, XI, XII)
            ['nis' => '2026026', 'nama' => 'Ahmad Basri Hasan', 'tingkat' => 'SMA', 'kelas' => 'X MIPA 1 (Imam Bukhari)', 'jk' => 'L', 'tgl' => '2009-03-14', 'prg' => 'PRG-TFZ', 'wali' => 20],
            ['nis' => '2026027', 'nama' => 'Fahri Sofyan Hadi', 'tingkat' => 'SMA', 'kelas' => 'X MIPA 1 (Imam Bukhari)', 'jk' => 'L', 'tgl' => '2009-06-25', 'prg' => 'PRG-ARB', 'wali' => 21],
            ['nis' => '2026028', 'nama' => 'Naufal Subagyo', 'tingkat' => 'SMA', 'kelas' => 'X MIPA 2 (Imam Muslim)', 'jk' => 'L', 'tgl' => '2009-11-04', 'prg' => 'PRG-ING', 'wali' => 22],
            ['nis' => '2026029', 'nama' => 'Rifqi Mahfud', 'tingkat' => 'SMA', 'kelas' => 'X IPS (Ibnu Khaldun)', 'jk' => 'L', 'tgl' => '2009-01-18', 'prg' => 'PRG-TFZ', 'wali' => 23],
            ['nis' => '2026030', 'nama' => 'Danial Taufik', 'tingkat' => 'SMA', 'kelas' => 'X IPS (Ibnu Khaldun)', 'jk' => 'L', 'tgl' => '2009-08-21', 'prg' => 'PRG-ARB', 'wali' => 24],
            ['nis' => '2026031', 'nama' => 'Zulfikar Faisal Amir', 'tingkat' => 'SMA', 'kelas' => 'XI MIPA 1 (Al-Farabi)', 'jk' => 'L', 'tgl' => '2008-04-09', 'prg' => 'PRG-ING', 'wali' => 25],
            ['nis' => '2026032', 'nama' => 'Faris Kurniawan', 'tingkat' => 'SMA', 'kelas' => 'XI MIPA 2 (Ibnu Sina)', 'jk' => 'L', 'tgl' => '2008-09-16', 'prg' => 'PRG-TFZ', 'wali' => 26],
            ['nis' => '2026033', 'nama' => 'Haikal Rudi Hartono', 'tingkat' => 'SMA', 'kelas' => 'XI IPS (Al-Kindi)', 'jk' => 'L', 'tgl' => '2008-12-03', 'prg' => 'PRG-ARB', 'wali' => 27],
            ['nis' => '2026034', 'nama' => 'Ilham Mochamad Zaini', 'tingkat' => 'SMA', 'kelas' => 'XII MIPA (Al-Biruni)', 'jk' => 'L', 'tgl' => '2007-02-14', 'prg' => 'PRG-ING', 'wali' => 28],
            ['nis' => '2026035', 'nama' => 'Lukman Hamzah Fansuri', 'tingkat' => 'SMA', 'kelas' => 'XII IPS (Ibnu Battuta)', 'jk' => 'L', 'tgl' => '2007-06-29', 'prg' => 'PRG-TFZ', 'wali' => 29],
            ['nis' => '2026036', 'nama' => 'Salma Edi Sudrajat', 'tingkat' => 'SMA', 'kelas' => 'X MIPA 1 (Rabiah)', 'jk' => 'P', 'tgl' => '2009-05-11', 'prg' => 'PRG-ARB', 'wali' => 30],
            ['nis' => '2026037', 'nama' => 'Nabila Danang Wicaksono', 'tingkat' => 'SMA', 'kelas' => 'X MIPA 2 (Asma)', 'jk' => 'P', 'tgl' => '2009-10-27', 'prg' => 'PRG-ING', 'wali' => 31],
            ['nis' => '2026038', 'nama' => 'Hanifah Anwar Fuadi', 'tingkat' => 'SMA', 'kelas' => 'X IPS (Nusaibah)', 'jk' => 'P', 'tgl' => '2009-03-02', 'prg' => 'PRG-TFZ', 'wali' => 32],
            ['nis' => '2026039', 'nama' => 'Latifah Syaifullah', 'tingkat' => 'SMA', 'kelas' => 'XI MIPA (Khadijah)', 'jk' => 'P', 'tgl' => '2008-01-19', 'prg' => 'PRG-ARB', 'wali' => 33],
            ['nis' => '2026040', 'nama' => 'Safira Bambang Irawan', 'tingkat' => 'SMA', 'kelas' => 'XI MIPA (Khadijah)', 'jk' => 'P', 'tgl' => '2008-07-07', 'prg' => 'PRG-ING', 'wali' => 34],
            ['nis' => '2026041', 'nama' => 'Syifa Tri Handoko', 'tingkat' => 'SMA', 'kelas' => 'XI IPS (Aisyah)', 'jk' => 'P', 'tgl' => '2008-11-14', 'prg' => 'PRG-TFZ', 'wali' => 35],
            ['nis' => '2026042', 'nama' => 'Yasmin Sudirman', 'tingkat' => 'SMA', 'kelas' => 'XII MIPA (Fatimah)', 'jk' => 'P', 'tgl' => '2007-04-20', 'prg' => 'PRG-ARB', 'wali' => 36],
            ['nis' => '2026043', 'nama' => 'Zulfa Muchtar Luthfi', 'tingkat' => 'SMA', 'kelas' => 'XII IPS (Hafshah)', 'jk' => 'P', 'tgl' => '2007-09-08', 'prg' => 'PRG-ING', 'wali' => 37],
            ['nis' => '2026044', 'nama' => 'Nadira Rasyid', 'tingkat' => 'SMA', 'kelas' => 'X MIPA 1 (Rabiah)', 'jk' => 'P', 'tgl' => '2009-12-15', 'prg' => 'PRG-TFZ', 'wali' => 6], // Adik Abdullah (Wali 6 punya 2 anak)
            ['nis' => '2026045', 'nama' => 'Fadhil Dedi Pratama', 'tingkat' => 'SMA', 'kelas' => 'X IPS (Ibnu Khaldun)', 'jk' => 'L', 'tgl' => '2009-07-03', 'prg' => 'PRG-ARB', 'wali' => 7], // Kakak Bilal (Wali 7 punya 2 anak)
            ['nis' => '2026046', 'nama' => 'Najwa Ridho', 'tingkat' => 'SMA', 'kelas' => 'XI MIPA (Khadijah)', 'jk' => 'P', 'tgl' => '2008-05-18', 'prg' => 'PRG-ING', 'wali' => 8], // Kakak Hasan (Wali 8 punya 2 anak)
            ['nis' => '2026047', 'nama' => 'Tariq Hendra Kurniawan', 'tingkat' => 'SMA', 'kelas' => 'XII MIPA (Al-Biruni)', 'jk' => 'L', 'tgl' => '2007-08-12', 'prg' => 'PRG-TFZ', 'wali' => 9], // Kakak Husain (Wali 9 punya 2 anak)
            ['nis' => '2026048', 'nama' => 'Kayla Priyanto', 'tingkat' => 'SMP', 'kelas' => 'VII B (Hafshah)', 'jk' => 'P', 'tgl' => '2012-02-14', 'prg' => 'PRG-ARB', 'wali' => 10], // Adik Hamzah (Wali 10 punya 2 anak)
            ['nis' => '2026049', 'nama' => 'Rahmat Wahyu', 'tingkat' => 'SMA', 'kelas' => 'XI IPS (Al-Kindi)', 'jk' => 'L', 'tgl' => '2008-10-30', 'prg' => 'PRG-ING', 'wali' => 11], // Kakak Khalid (Wali 11 punya 2 anak)
            ['nis' => '2026050', 'nama' => 'Dzaki Santoso', 'tingkat' => 'SMA', 'kelas' => 'X MIPA 2 (Imam Muslim)', 'jk' => 'L', 'tgl' => '2009-06-17', 'prg' => 'PRG-TFZ', 'wali' => 12], // Kakak Salman (Wali 12 punya 2 anak)
        ];

        foreach ($rawSantri as $s) {
            $waliModel = $waliUsers[$s['wali']] ?? null;

            Santri::updateOrCreate(
                ['nis' => $s['nis']],
                [
                    'nisn' => '0087' . substr($s['nis'], 2) . rand(10, 99),
                    'nama_santri' => $s['nama'],
                    'tingkat' => $s['tingkat'],
                    'kelas' => $s['kelas'],
                    'jenis_kelamin' => $s['jk'],
                    'tgl_lahir' => $s['tgl'],
                    'id_program' => $s['prg'],
                    'id_wali' => $waliModel ? $waliModel->id_user : null,
                    'status_aktif' => 'aktif',
                ]
            );
        }

        // 8. Generate Tagihan & Transaksi Sampel untuk 50 Santri
        // Tagihan SPP Bulan Mei (Sebagian besar lunas sebagai bukti riwayat keuangan)
        $kwitansiCounter = 1;

        foreach ($rawSantri as $idx => $s) {
            $nis = $s['nis'];
            $nomSpp = 500000.00;

            // Scenario 1: Santri 1-25 sudah LUNAS SPP Bulan Mei
            if ($idx < 25) {
                $tMay = Tagihan::updateOrCreate(
                    ['nis' => $nis, 'id_jenis_tagihan' => $spp->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => 5],
                    [
                        'nominal_tagihan' => $nomSpp,
                        'total_terbayar' => $nomSpp,
                        'sisa_tagihan' => 0.00,
                        'jatuh_tempo' => '2026-05-10',
                        'status_tagihan' => 'lunas',
                    ]
                );

                $orderId = 'DMTQ-202605-' . sprintf('%04d', $tMay->id_tagihan);
                $metode = ($idx % 2 == 0) ? 'gateway' : 'tunai';
                $channel = ($metode == 'gateway') ? 'BCA Virtual Account' : 'Tunai Kasir Bendahara';

                $pemb = Pembayaran::updateOrCreate(
                    ['order_id' => $orderId],
                    [
                        'id_tagihan' => $tMay->id_tagihan,
                        'tgl_bayar' => now()->subDays(rand(1, 15)),
                        'jumlah_bayar' => $nomSpp,
                        'biaya_admin' => ($metode == 'gateway') ? 2500.00 : 0.00,
                        'total_bayar' => ($metode == 'gateway') ? 502500.00 : 500000.00,
                        'metode_bayar' => $metode,
                        'channel_bayar' => $channel,
                        'va_number' => ($metode == 'gateway') ? '8808' . rand(10000000, 99999999) : null,
                        'snap_token' => ($metode == 'gateway') ? 'TOKEN-DEMO-' . $tMay->id_tagihan : null,
                        'status_transaksi' => 'settlement',
                        'settlement_time' => now()->subDays(rand(1, 15)),
                        'verified_by' => 2, // Bendahara
                    ]
                );

                Kwitansi::updateOrCreate(
                    ['id_pembayaran' => $pemb->id_pembayaran],
                    [
                        'no_kwitansi' => 'KWT/' . date('Ym') . '/' . sprintf('%04d', $kwitansiCounter++),
                        'tgl_terbit' => now()->subDays(rand(1, 15)),
                    ]
                );
            }

            // Scenario 2: Santri 1-50 Tagihan SPP Bulan Juni (Periode berjalan: sebagian belum bayar, sebagian lunas)
            if ($idx < 15) {
                // Lunas
                $tJun = Tagihan::updateOrCreate(
                    ['nis' => $nis, 'id_jenis_tagihan' => $spp->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => 6],
                    [
                        'nominal_tagihan' => $nomSpp,
                        'total_terbayar' => $nomSpp,
                        'sisa_tagihan' => 0.00,
                        'jatuh_tempo' => '2026-06-10',
                        'status_tagihan' => 'lunas',
                    ]
                );

                $orderId = 'DMTQ-202606-' . sprintf('%04d', $tJun->id_tagihan);
                $pemb = Pembayaran::updateOrCreate(
                    ['order_id' => $orderId],
                    [
                        'id_tagihan' => $tJun->id_tagihan,
                        'tgl_bayar' => now()->subDays(rand(1, 4)),
                        'jumlah_bayar' => $nomSpp,
                        'biaya_admin' => 2500.00,
                        'total_bayar' => 502500.00,
                        'metode_bayar' => 'gateway',
                        'channel_bayar' => 'QRIS GoPay/ShopeePay',
                        'snap_token' => 'TOKEN-JUN-' . $tJun->id_tagihan,
                        'status_transaksi' => 'settlement',
                        'settlement_time' => now()->subDays(rand(1, 4)),
                        'verified_by' => 2,
                    ]
                );

                Kwitansi::updateOrCreate(
                    ['id_pembayaran' => $pemb->id_pembayaran],
                    [
                        'no_kwitansi' => 'KWT/' . date('Ym') . '/' . sprintf('%04d', $kwitansiCounter++),
                        'tgl_terbit' => now()->subDays(rand(1, 4)),
                    ]
                );
            } elseif ($idx < 30) {
                // Dicicil sebagian
                Tagihan::updateOrCreate(
                    ['nis' => $nis, 'id_jenis_tagihan' => $spp->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => 6],
                    [
                        'nominal_tagihan' => $nomSpp,
                        'total_terbayar' => 250000.00,
                        'sisa_tagihan' => 250000.00,
                        'jatuh_tempo' => '2026-06-10',
                        'status_tagihan' => 'dicicil',
                    ]
                );
            } else {
                // Belum bayar
                Tagihan::updateOrCreate(
                    ['nis' => $nis, 'id_jenis_tagihan' => $spp->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => 6],
                    [
                        'nominal_tagihan' => $nomSpp,
                        'total_terbayar' => 0.00,
                        'sisa_tagihan' => $nomSpp,
                        'jatuh_tempo' => '2026-06-10',
                        'status_tagihan' => 'belum_bayar',
                    ]
                );
            }

            // Tagihan Uang Pangkal untuk Santri Baru (Kelas VII & X)
            if (str_contains($s['kelas'], 'VII') || str_contains($s['kelas'], 'X')) {
                $statusPangkal = ($idx % 3 == 0) ? 'lunas' : (($idx % 3 == 1) ? 'dicicil' : 'belum_bayar');
                $terbayar = ($statusPangkal == 'lunas') ? 2500000.00 : (($statusPangkal == 'dicicil') ? 1000000.00 : 0.00);

                Tagihan::updateOrCreate(
                    ['nis' => $nis, 'id_jenis_tagihan' => $gedung->id_jenis_tagihan, 'tahun_ajaran' => '2025/2026', 'bulan' => null],
                    [
                        'nominal_tagihan' => 2500000.00,
                        'total_terbayar' => $terbayar,
                        'sisa_tagihan' => 2500000.00 - $terbayar,
                        'jatuh_tempo' => '2026-07-31',
                        'status_tagihan' => $statusPangkal,
                    ]
                );
            }
        }

        // 9. Catat Log Audit Seeder Initial
        ActivityLog::firstOrCreate(
            ['action' => 'SYSTEM_INIT'],
            [
                'username' => 'admin',
                'role' => 'admin',
                'description' => 'Sistem berhasil diinisialisasi dengan 50 santri, 37 akun wali, dan transaksi demo pembayaran.',
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
            ]
        );
    }
}
