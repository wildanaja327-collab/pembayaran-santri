-- ========================================================
-- SISTEM INFORMASI MANAJEMEN KEUANGAN PEMBAYARAN SANTRI
-- PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI BERBASIS WEB
-- Skema Database Normalisasi 3NF (Enhanced & Defense-Ready)
-- ========================================================

CREATE DATABASE IF NOT EXISTS db_pembayaran_santri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pembayaran_santri;

-- --------------------------------------------------------
-- 1. Tabel users (Autentikasi Administrator, Bendahara & Wali Santri)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    role ENUM('admin', 'bendahara', 'wali_santri') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    remember_token VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 2. Tabel program (Program Unggulan Pesantren)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS program (
    id_program VARCHAR(10) PRIMARY KEY,
    nama_program VARCHAR(100) NOT NULL,
    keterangan TEXT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 3. Tabel santri (Data Santri SMP & SMA)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS santri (
    nis VARCHAR(20) PRIMARY KEY,
    nisn VARCHAR(20) UNIQUE NULL,
    nama_santri VARCHAR(100) NOT NULL,
    tingkat ENUM('SMP', 'SMA') NOT NULL,
    kelas VARCHAR(50) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    tgl_lahir DATE NULL,
    id_program VARCHAR(10) NOT NULL,
    id_wali INT(11) NULL,
    status_aktif ENUM('aktif', 'alumni', 'mutasi') DEFAULT 'aktif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_santri_program FOREIGN KEY (id_program) REFERENCES program(id_program) ON UPDATE CASCADE,
    CONSTRAINT fk_santri_wali FOREIGN KEY (id_wali) REFERENCES users(id_user) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 4. Tabel tahun_ajaran (Master Periode Akademik & Tagihan)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tahun_ajaran (
    id_tahun_ajaran INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_tahun_ajaran VARCHAR(20) NOT NULL,
    semester ENUM('Ganjil', 'Genap') DEFAULT 'Ganjil',
    tgl_mulai DATE NULL,
    tgl_selesai DATE NULL,
    is_aktif TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_tahun_semester (nama_tahun_ajaran, semester)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 5. Tabel jenis_tagihan (Master Tarif Pembayaran)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS jenis_tagihan (
    id_jenis_tagihan INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_tagihan VARCHAR(100) NOT NULL,
    tipe ENUM('bulanan', 'insidental') NOT NULL,
    nominal_standar DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    keterangan VARCHAR(255) NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 6. Tabel tagihan (Piutang & Mendukung Cicilan / Bulk Generation)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tagihan (
    id_tagihan INT(11) AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL,
    id_jenis_tagihan INT(11) NOT NULL,
    tahun_ajaran VARCHAR(10) NOT NULL,
    bulan TINYINT(2) NULL, -- 1 s.d 12 untuk Syahriah bulanan
    nominal_tagihan DECIMAL(12,2) NOT NULL,
    total_terbayar DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    sisa_tagihan DECIMAL(12,2) NOT NULL,
    jatuh_tempo DATE NULL,
    status_tagihan ENUM('belum_bayar', 'dicicil', 'menunggu_verifikasi', 'lunas') DEFAULT 'belum_bayar',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tagihan_santri FOREIGN KEY (nis) REFERENCES santri(nis) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tagihan_jenis FOREIGN KEY (id_jenis_tagihan) REFERENCES jenis_tagihan(id_jenis_tagihan) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 7. Tabel pembayaran (Transaksi Payment Gateway, Tunai & Admin Fee)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS pembayaran (
    id_pembayaran INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE,
    id_tagihan INT(11) NOT NULL,
    tgl_bayar DATETIME DEFAULT CURRENT_TIMESTAMP,
    jumlah_bayar DECIMAL(12,2) NOT NULL,
    biaya_admin DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_bayar DECIMAL(12,2) NOT NULL,
    metode_bayar ENUM('gateway', 'tunai') DEFAULT 'gateway',
    channel_bayar VARCHAR(50) NULL, -- bca_va, bri_va, mandiri_va, qris, tunai_kasir
    va_number VARCHAR(50) NULL,
    snap_token VARCHAR(255) NULL,
    status_transaksi ENUM('pending', 'settlement', 'expire', 'cancel', 'deny') DEFAULT 'pending',
    settlement_time DATETIME NULL,
    gateway_response TEXT NULL,
    verified_by INT(11) NULL,
    CONSTRAINT fk_pembayaran_tagihan FOREIGN KEY (id_tagihan) REFERENCES tagihan(id_tagihan) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pembayaran_verifier FOREIGN KEY (verified_by) REFERENCES users(id_user) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 8. Tabel kwitansi (Penerbitan Kwitansi Resmi)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS kwitansi (
    id_kwitansi INT(11) AUTO_INCREMENT PRIMARY KEY,
    no_kwitansi VARCHAR(50) NOT NULL UNIQUE,
    id_pembayaran INT(11) NOT NULL UNIQUE,
    tgl_terbit DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kwitansi_pembayaran FOREIGN KEY (id_pembayaran) REFERENCES pembayaran(id_pembayaran) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 9. Tabel activity_logs (Audit Trail Keamanan & Aktivitas Sistem)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NULL,
    username VARCHAR(50) NULL,
    role VARCHAR(20) NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_activity_created (created_at),
    INDEX idx_activity_action (action)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 10. Tabel pengaturan (Konfigurasi Dinamis & Identitas Lembaga)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NULL,
    label VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) DEFAULT 'umum',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 11. Tabel notifikasi_wa (Log Notifikasi Pengingat Tagihan & Kwitansi)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifikasi_wa (
    id_notifikasi INT(11) AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NULL,
    no_tujuan VARCHAR(20) NOT NULL,
    pesan TEXT NOT NULL,
    jenis_pesan ENUM('tagihan_baru', 'pengingat_tempo', 'pembayaran_sukses') NOT NULL,
    status_kirim ENUM('pending', 'terkirim', 'gagal') DEFAULT 'pending',
    tgl_kirim DATETIME DEFAULT CURRENT_TIMESTAMP,
    response_api TEXT NULL,
    CONSTRAINT fk_notif_santri FOREIGN KEY (nis) REFERENCES santri(nis) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- SEED DATA AWAL
-- --------------------------------------------------------
INSERT INTO program (id_program, nama_program, keterangan) VALUES
('PRG-TFZ', 'Program Tahfidzul Qur\'an', 'Fokus hafalan Al-Qur\'an'),
('PRG-ARB', 'Pendalaman Bahasa Arab', 'Fokus kemahiran lisan & tulisan bahasa Arab'),
('PRG-ING', 'Pendalaman Bahasa Inggris', 'Fokus kemahiran lisan & tulisan bahasa Inggris')
ON DUPLICATE KEY UPDATE nama_program=VALUES(nama_program);

-- Akun Default:
-- Admin: username 'admin', password 'admin123'
-- Bendahara: username 'bendahara', password 'bendahara123'
-- Wali Demo: username 'wali_0001', password 'wali123' (atau wali_2026001)

INSERT INTO pengaturan (setting_key, setting_value, label, kategori) VALUES
('pesantren_nama', 'Pondok Pesantren Darul Muttaqin Al Islami', 'Nama Pesantren', 'umum'),
('pesantren_alamat', 'Jl. Pesantren No. 1, Mulyojati 16 C, Metro Barat, Kota Metro, Lampung', 'Alamat Lengkap', 'umum'),
('pesantren_telepon', '0812-3456-7890 / 0725-12345', 'No. Telepon / WhatsApp', 'umum'),
('pesantren_email', 'info@darulmuttaqin.ac.id', 'Email Resmi', 'umum'),
('pimpinan_nama', 'KH. Muhammad Ridwan, Lc., M.Pd.I', 'Pimpinan / Pengasuh Pesantren', 'umum'),
('bendahara_nama', 'Ahmad Fauzi, S.E.', 'Nama Bendahara Resmi', 'umum'),
('kwitansi_footer', 'Bukti pembayaran sah ini diterbitkan secara otomatis oleh Sistem Informasi Keuangan Pesantren.', 'Catatan Kaki Kwitansi', 'kwitansi'),
('midtrans_mode', 'sandbox', 'Mode Gateway Midtrans (sandbox/production)', 'gateway')
ON DUPLICATE KEY UPDATE label=VALUES(label);
