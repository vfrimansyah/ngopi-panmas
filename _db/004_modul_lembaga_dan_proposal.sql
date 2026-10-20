-- ====================================================
-- SQL UPDATE UNTUK HOSTING (indonesiangopi.com)
-- MODUL: Bank Data Legalitas Yayasan & Generator Proposal (Pokir DPRD, CSR, Lembaga Sosial)
-- ====================================================

-- 1. TABEL LEMBAGA PROFILES (Yayasan / Lembaga / DKM / Organisasi Pemohon)
CREATE TABLE IF NOT EXISTS `lembaga_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `nama_lembaga` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_lembaga` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'yayasan',
  `kategori_bidang` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat_lengkap` text COLLATE utf8mb4_general_ci,
  `kelurahan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Pancoran Mas',
  `kota` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Kota Depok',
  `kode_pos` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_kontak` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_ketua` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nik_ketua` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hp_ketua` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `npwp_ketua` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_sekretaris` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_bendahara` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_akta_notaris` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_notaris` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_akta` date DEFAULT NULL,
  `no_sk_kemenkumham` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `npwp_lembaga` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nib` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_bank` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_rekening` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `atas_nama_rekening` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_ktp_ketua` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_npwp_ketua` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_npwp_lembaga` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_akta` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_sk_kemenkumham` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_nib` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_buku_rekening` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_domisili` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_susunan_pengurus` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. TABEL PROPOSALS
CREATE TABLE IF NOT EXISTS `proposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lembaga_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `nomor_surat` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_surat` date DEFAULT NULL,
  `perihal` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Permohonan Bantuan Dana / Hibah',
  `judul_proposal` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `target_pengajuan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pokir_dprd',
  `tujuan_nama` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tujuan_instansi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tujuan_alamat` text COLLATE utf8mb4_general_ci,
  `latar_belakang` text COLLATE utf8mb4_general_ci,
  `maksud_tujuan` text COLLATE utf8mb4_general_ci,
  `deskripsi_kegiatan` text COLLATE utf8mb4_general_ci,
  `sasaran_penerima` text COLLATE utf8mb4_general_ci,
  `waktu_pelaksanaan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lokasi_kegiatan` text COLLATE utf8mb4_general_ci,
  `susunan_panitia` text COLLATE utf8mb4_general_ci,
  `penutup` text COLLATE utf8mb4_general_ci,
  `total_anggaran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `terbilang_anggaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `selected_attachments` text COLLATE utf8mb4_general_ci,
  `foto_dokumentasi` text COLLATE utf8mb4_general_ci,
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `catatan_admin` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lembaga_id` (`lembaga_id`),
  KEY `user_id` (`user_id`),
  KEY `member_id` (`member_id`),
  KEY `target_pengajuan` (`target_pengajuan`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. TABEL PROPOSAL RABS (Rencana Anggaran Biaya)
CREATE TABLE IF NOT EXISTS `proposal_rabs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proposal_id` bigint unsigned NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Umum',
  `uraian` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `volume` decimal(10,2) NOT NULL DEFAULT '1.00',
  `satuan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Unit',
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proposal_id` (`proposal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. MENDAFTARKAN MENU KE SETTINGS_MENUS
-- Submenu Meja Saya (Yayasan & Legalitas + Proposal Saya)
INSERT INTO `settings_menus` (`parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`)
SELECT id, 'Yayasan & Legalitas', 'fa-solid fa-landmark', 'admin/meja/lembaga', 4, NOW(), NOW()
FROM `settings_menus`
WHERE `title` = 'Meja Saya' AND NOT EXISTS (
    SELECT 1 FROM `settings_menus` WHERE `url` = 'admin/meja/lembaga'
);

INSERT INTO `settings_menus` (`parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`)
SELECT id, 'Proposal Saya', 'fa-solid fa-file-signature', 'admin/meja/proposals', 5, NOW(), NOW()
FROM `settings_menus`
WHERE `title` = 'Meja Saya' AND NOT EXISTS (
    SELECT 1 FROM `settings_menus` WHERE `url` = 'admin/meja/proposals'
);

-- Menu Root Admin / BPH (Proposal & Pokir)
INSERT INTO `settings_menus` (`parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`)
SELECT NULL, 'Proposal & Pokir', 'fa-solid fa-file-invoice-dollar', 'admin/proposals', 9, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `settings_menus` WHERE `url` = 'admin/proposals'
);

-- 5. MEMBERIKAN HAK AKSES ROLE MENU
-- Akses Meja Saya untuk semua role aktif
INSERT IGNORE INTO `settings_menu_roles` (`menu_id`, `group_name`, `created_at`, `updated_at`)
SELECT m.id, g.group_name, NOW(), NOW()
FROM `settings_menus` m
CROSS JOIN (SELECT DISTINCT `group_name` FROM `settings_menu_roles`) g
WHERE m.url IN ('admin/meja/lembaga', 'admin/meja/proposals');

-- Akses Admin/BPH untuk monitoring Proposal & Pokir
INSERT IGNORE INTO `settings_menu_roles` (`menu_id`, `group_name`, `created_at`, `updated_at`)
SELECT id, 'admin', NOW(), NOW() FROM `settings_menus` WHERE `url` = 'admin/proposals';

INSERT IGNORE INTO `settings_menu_roles` (`menu_id`, `group_name`, `created_at`, `updated_at`)
SELECT id, 'bph', NOW(), NOW() FROM `settings_menus` WHERE `url` = 'admin/proposals';

INSERT IGNORE INTO `settings_menu_roles` (`menu_id`, `group_name`, `created_at`, `updated_at`)
SELECT id, 'superadmin', NOW(), NOW() FROM `settings_menus` WHERE `url` = 'admin/proposals';
