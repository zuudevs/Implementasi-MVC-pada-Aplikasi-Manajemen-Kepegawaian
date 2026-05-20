-- ============================================================
-- Database: db_kepegawaian
-- Deskripsi: Skema dan data awal untuk Sistem Informasi
--            Manajemen Kepegawaian (SIMKEP) - Kelompok 14
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_kepegawaian`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `db_kepegawaian`;

-- ── Tabel Pegawai ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pegawai` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nik`           VARCHAR(18)     NOT NULL UNIQUE COMMENT 'Nomor Induk Karyawan',
    `nama`          VARCHAR(100)    NOT NULL,
    `jabatan`       VARCHAR(100)    NOT NULL,
    `departemen`    VARCHAR(50)     NOT NULL,
    `gaji`          DECIMAL(15, 0)  NOT NULL DEFAULT 0,
    `tanggal_masuk` DATE            NOT NULL,
    `status`        ENUM('Aktif','Non-Aktif','Cuti') NOT NULL DEFAULT 'Aktif',
    `created_at`    DATETIME        NULL,
    `updated_at`    DATETIME        NULL,
    `deleted_at`    DATETIME        NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    INDEX `idx_status`    (`status`),
    INDEX `idx_departemen`(`departemen`),
    INDEX `idx_deleted_at`(`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seed Data ─────────────────────────────────────────────────────────────────
INSERT INTO `pegawai`
    (`nik`, `nama`, `jabatan`, `departemen`, `gaji`, `tanggal_masuk`, `status`, `created_at`)
VALUES
    ('10001001', 'Budi Santoso',          'Manajer IT',               'IT',          12000000, '2019-03-15', 'Aktif',     NOW()),
    ('10001002', 'Siti Rahayu',           'Staff HRD',                'HRD',          6500000, '2020-07-01', 'Aktif',     NOW()),
    ('10001003', 'Ahmad Fauzi',           'Senior Developer',         'IT',           9500000, '2018-11-20', 'Aktif',     NOW()),
    ('10001004', 'Dewi Kusuma',           'Kepala Keuangan',          'Keuangan',    14000000, '2017-05-10', 'Aktif',     NOW()),
    ('10001005', 'Rizky Pratama',         'Marketing Executive',      'Marketing',    7200000, '2021-01-15', 'Aktif',     NOW()),
    ('10001006', 'Nurul Hidayah',         'Staff Keuangan',           'Keuangan',     5800000, '2021-09-01', 'Aktif',     NOW()),
    ('10001007', 'Hendra Wijaya',         'Supervisor Operasional',   'Operasional',  8800000, '2016-04-25', 'Aktif',     NOW()),
    ('10001008', 'Lestari Putri',         'Legal Officer',            'Legal',        8200000, '2019-08-12', 'Cuti',      NOW()),
    ('10001009', 'Muhammad Arif',         'Junior Developer',         'IT',           5500000, '2022-03-01', 'Aktif',     NOW()),
    ('10001010', 'Ayu Wulandari',         'Staff Administrasi',       'HRD',          4800000, '2022-06-15', 'Aktif',     NOW()),
    ('10001011', 'Fajar Nugroho',         'Kepala Produksi',          'Produksi',    11500000, '2015-02-01', 'Aktif',     NOW()),
    ('10001012', 'Indah Permatasari',     'Digital Marketing',        'Marketing',    6800000, '2020-11-10', 'Non-Aktif', NOW()),
    ('10001013', 'Doni Kurniawan',        'Staff Logistik',           'Logistik',     5200000, '2021-07-20', 'Aktif',     NOW()),
    ('10001014', 'Rini Andriani',         'QA Engineer',              'IT',           7800000, '2019-12-05', 'Aktif',     NOW()),
    ('10001015', 'Wahyu Setiawan',        'Supervisor Produksi',      'Produksi',     9000000, '2017-09-18', 'Cuti',      NOW());
