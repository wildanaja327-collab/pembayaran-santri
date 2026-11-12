-- ========================================================
-- UPGRADE SISTEM: FITUR ADMIN LENGKAP & PROFESSIONAL
-- 1. Modifikasi tabel users (is_active, last_login_at)
-- 2. Pembuatan tabel activity_logs (Audit Trail)
-- 3. Pembuatan tabel tahun_ajaran (Master Siklus Akademik)
-- 4. Pembuatan tabel pengaturan (Konfigurasi Sistem & Header)
-- ========================================================

USE db_pembayaran_santri;

-- 1. Tambah kolom is_active & last_login_at pada tabel users (jika belum ada)
SET @exist_is_active := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'db_pembayaran_santri' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_active');
SET @sql := IF(@exist_is_active = 0, 'ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exist_last_login := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'db_pembayaran_santri' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_login_at');
SET @sql2 := IF(@exist_last_login = 0, 'ALTER TABLE users ADD COLUMN last_login_at DATETIME NULL AFTER is_active;', 'SELECT 1;');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- 2. Tabel activity_logs (Audit Trail Keamanan & Aktivitas)
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

-- 3. Tabel tahun_ajaran
CREATE TABLE IF NOT EXISTS tahun_ajaran (
    id_tahun_ajaran INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_tahun_ajaran VARCHAR(20) NOT NULL UNIQUE,
    semester ENUM('Ganjil', 'Genap') DEFAULT 'Ganjil',
    tgl_mulai DATE NULL,
    tgl_selesai DATE NULL,
    is_aktif TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Tabel pengaturan
CREATE TABLE IF NOT EXISTS pengaturan (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NULL,
    label VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) DEFAULT 'umum',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed Data Awal Tahun Ajaran
INSERT INTO tahun_ajaran (nama_tahun_ajaran, semester, tgl_mulai, tgl_selesai, is_aktif) VALUES
('2024/2025', 'Genap', '2025-01-01', '2025-06-30', 0),
('2025/2026', 'Ganjil', '2025-07-01', '2025-12-31', 0),
('2025/2026', 'Genap', '2026-01-01', '2026-06-30', 1)
ON DUPLICATE KEY UPDATE is_aktif = VALUES(is_aktif);

-- Seed Data Pengaturan Sistem
INSERT INTO pengaturan (setting_key, setting_value, label, kategori) VALUES
('pesantren_nama', 'Pondok Pesantren Darul Muttaqin Al Islami', 'Nama Pesantren', 'umum'),
('pesantren_alamat', 'Jl. Pesantren No. 1, Mulyojati 16 C, Metro Barat, Kota Metro, Lampung', 'Alamat Lengkap', 'umum'),
('pesantren_telepon', '0812-3456-7890 / 0725-12345', 'No. Telepon / WhatsApp', 'umum'),
('pesantren_email', 'info@darulmuttaqin.ac.id', 'Email Resmi', 'umum'),
('pimpinan_nama', 'KH. Muhammad Ridwan, Lc., M.Pd.I', 'Pimpinan / Pengasuh Pesantren', 'umum'),
('bendahara_nama', 'Ahmad Fauzi, S.E.', 'Nama Bendahara Resmi', 'umum'),
('kwitansi_footer', 'Bukti pembayaran sah ini diterbitkan secara otomatis oleh Sistem Informasi Keuangan Pesantren.', 'Catatan Kaki Kwitansi', 'kwitansi'),
('midtrans_mode', 'sandbox', 'Mode Gateway Midtrans (sandbox/production)', 'gateway')
ON DUPLICATE KEY UPDATE label = VALUES(label);

