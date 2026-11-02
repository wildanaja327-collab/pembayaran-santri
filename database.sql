-- ========================================================
-- SISTEM INFORMASI MANAJEMEN KEUANGAN PEMBAYARAN SANTRI
-- PONDOK PESANTREN DARUL MUTTAQIN AL ISLAMI BERBASIS WEB
-- Skema Database Normalisasi 3NF (Enhanced & Defense-Ready)
-- ========================================================

CREATE DATABASE IF NOT EXISTS db_pembayaran_santri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pembayaran_santri;

-- --------------------------------------------------------
-- 1. Tabel users (Autentikasi Bendahara & Wali Santri)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    role ENUM('bendahara', 'wali_santri') NOT NULL,
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
-- 4. Tabel jenis_tagihan (Master Tarif Pembayaran)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS jenis_tagihan (
    id_jenis_tagihan INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_tagihan VARCHAR(100) NOT NULL,
    tipe ENUM('bulanan', 'insidental') NOT NULL,
    nominal_standar DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    keterangan VARCHAR(255) NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 5. Tabel tagihan (Piutang & Mendukung Cicilan / Bulk Generation)
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
-- 6. Tabel pembayaran (Transaksi Payment Gateway, Tunai & Admin Fee)
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
-- 7. Tabel kwitansi (Penerbitan Kwitansi Resmi)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS kwitansi (
    id_kwitansi INT(11) AUTO_INCREMENT PRIMARY KEY,
    no_kwitansi VARCHAR(50) NOT NULL UNIQUE,
    id_pembayaran INT(11) NOT NULL UNIQUE,
    tgl_terbit DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kwitansi_pembayaran FOREIGN KEY (id_pembayaran) REFERENCES pembayaran(id_pembayaran) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 8. Tabel notifikasi_wa (Log Notifikasi Pengingat Tagihan & Kwitansi)
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

-- Akun Bendahara Default: username 'bendahara', password 'admin123'
INSERT INTO users (username, password, nama_lengkap, no_telp, role) VALUES
('bendahara', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1v5j3O3hE3x6YqC5nZ.9b6c4.DkmW9a', 'Bendahara Darul Muttaqin', '081234567890', 'bendahara')
ON DUPLICATE KEY UPDATE username=VALUES(username);

-- Master Jenis Tagihan
INSERT INTO jenis_tagihan (nama_tagihan, tipe, nominal_standar, keterangan) VALUES
('Syahriah (SPP Bulanan)', 'bulanan', 500000.00, 'SPP dan operasional bulanan santri'),
('Biaya Munaqosyah', 'insidental', 350000.00, 'Biaya ujian munaqosyah kelulusan/hafalan'),
('Uang Makan & Asrama', 'bulanan', 400000.00, 'Biaya katering dan fasilitas asrama'),
('Uang Kitab & Pembelajaran', 'insidental', 250000.00, 'Pengadaan kitab kurikulum pesantren'),
('Uang Pangkal / Pembangunan', 'insidental', 3000000.00, 'Biaya pengembangan gedung santri baru (bisa dicicil)')
ON DUPLICATE KEY UPDATE nama_tagihan=VALUES(nama_tagihan);
