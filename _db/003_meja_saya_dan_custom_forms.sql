-- ====================================================
-- SQL UPDATE UNTUK SERVER (indonesiangopi.com)
-- Berisi tabel custom forms & menu Meja Saya + Form & Survei
-- ====================================================

-- 1. TABEL CUSTOM FORMS
CREATE TABLE IF NOT EXISTS `custom_forms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `header_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `closed_at` datetime DEFAULT NULL,
  `allow_multiple` tinyint(1) NOT NULL DEFAULT '1',
  `auto_link_member` tinyint(1) NOT NULL DEFAULT '1',
  `success_message` text COLLATE utf8mb4_general_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tambahan kolom jika tabel sudah terlanjur dibuat sebelumnya:
-- ALTER TABLE `custom_forms` ADD COLUMN IF NOT EXISTS `closed_at` datetime DEFAULT NULL AFTER `is_active`;
-- ALTER TABLE `custom_form_fields` ADD COLUMN IF NOT EXISTS `default_value` text DEFAULT NULL AFTER `field_options`;

-- 2. TABEL CUSTOM FORM FIELDS (PERTANYAAN)
CREATE TABLE IF NOT EXISTS `custom_form_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `field_label` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `field_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `field_type` enum('text','number','textarea','date','radio','checkbox','select') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'text',
  `field_options` text COLLATE utf8mb4_general_ci,
  `default_value` text COLLATE utf8mb4_general_ci DEFAULT NULL,
  `help_text` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `map_to_member_field` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_form_fields_form_id_foreign` (`form_id`),
  CONSTRAINT `custom_form_fields_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `custom_forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. TABEL CUSTOM FORM RESPONSES
CREATE TABLE IF NOT EXISTS `custom_form_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `respondent_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `respondent_phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `respondent_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_status` enum('auto_linked','manual_linked','unlinked') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unlinked',
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_form_responses_form_id_foreign` (`form_id`),
  KEY `custom_form_responses_member_id_foreign` (`member_id`),
  CONSTRAINT `custom_form_responses_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `custom_forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `custom_form_responses_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. TABEL CUSTOM FORM ANSWERS
CREATE TABLE IF NOT EXISTS `custom_form_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `response_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned NOT NULL,
  `answer_value` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `custom_form_answers_response_id_foreign` (`response_id`),
  KEY `custom_form_answers_field_id_foreign` (`field_id`),
  CONSTRAINT `custom_form_answers_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `custom_form_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `custom_form_answers_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `custom_form_responses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. SEEDING FORM SKRINING KESEHATAN
REPLACE INTO `custom_forms` (`id`, `title`, `slug`, `description`, `header_image`, `is_public`, `is_active`, `allow_multiple`, `auto_link_member`, `success_message`, `created_by`, `created_at`, `updated_at`) VALUES ('1', 'Skrining Kesehatan', 'skrining-kesehatan', 'Kuesioner kesiapan aktivitas fisik dan skrining kesehatan berkala anggota dan masyarakat (Physical Activity Readiness Questionnaire / PAR-Q). Formulir ini membantu memastikan keselamatan Anda sebelum mengikuti kegiatan atau program fisik.', NULL, '1', '1', '1', '1', 'Terima kasih atas partisipasi Anda! Data skrining kesehatan Anda telah berhasil disimpan dan dicocokkan dengan database kesehatan komunitas.', NULL, '2026-09-03 02:57:04', '2026-09-03 02:57:04');

REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('1', '1', 'Nama Lengkap', 'nama', 'text', NULL, 'Nama lengkap sesuai KTP / database anggota', '1', 'nama_lengkap', '1', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('2', '1', 'NIK', 'nik', 'text', NULL, 'Nomor Induk Kependudukan (16 digit)', '0', 'nik', '2', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('3', '1', 'Tanggal Lahir', 'tanggal_lahir', 'date', NULL, 'Pilih tanggal lahir Anda', '1', 'tanggal_lahir', '3', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('4', '1', 'Alamat Domisili', 'alamat', 'textarea', NULL, 'Alamat tempat tinggal saat ini (Kelurahan, RT/RW)', '1', 'alamat_lengkap', '4', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('5', '1', 'No HP / WhatsApp', 'no_hp', 'text', NULL, 'Nomor WhatsApp aktif untuk konfirmasi & pencocokan akun', '1', 'handphone', '5', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('6', '1', 'Kode Group UPA', 'kode_upa', 'text', NULL, '(Hanya Angka) contoh: 3201', '0', 'kode_upa', '6', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('7', '1', 'Lingkar Perut', 'lingkar_perut', 'number', NULL, 'Dalam satuan cm (contoh: 85)', '0', NULL, '7', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('8', '1', 'Berat Badan', 'berat_badan', 'number', NULL, 'Dalam satuan kg (contoh: 65)', '0', NULL, '8', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('9', '1', 'Tinggi Badan', 'tinggi_badan', 'number', NULL, 'Dalam satuan cm (contoh: 170)', '0', NULL, '9', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('10', '1', 'Apakah kamu pernah didiagnosa dokter memiliki masalah dengan jantungmu dan hanya boleh melakukan aktivitas fisik sesuai anjuran dokter?', 'masalah_jantung', 'radio', '[\"Ya\",\"Tidak\"]', 'Pilih salah satu', '1', NULL, '10', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('11', '1', 'Apakah kamu merasakan nyeri disekitar dada ketika sedang melakukan aktivitas fisik?', 'nyeri_dada_aktivitas', 'radio', '[\"Ya\",\"Tidak\"]', 'Pilih salah satu', '1', NULL, '11', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('12', '1', 'Dalam beberapa bulan kebelakang apakah kamu pernah merasakan sakit disekitar dada ketika tidak sedang melakukan aktivitas fisik?', 'nyeri_dada_istirahat', 'radio', '[\"Ya\",\"Tidak\"]', 'Pilih salah satu', '1', NULL, '12', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('13', '1', 'Apakah kamu pernah kehilangan keseimbangan karena pusing atau kamu pernah kehilangan kesadaran / pingsan?', 'pusing_pingsan', 'radio', '[\"Ya\",\"Tidak\"]', 'Pilih salah satu', '1', NULL, '13', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('14', '1', 'Apakah kamu memiliki masalah pada tulang atau sendi (contohnya punggung, lutut, dll) yang sewaktu2 menjadi parah ketika melakukan aktivitas fisik?', 'masalah_tulang_sendi', 'radio', '[\"Ya\",\"Tidak\"]', 'Pilih salah satu', '1', NULL, '14', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('15', '1', 'Apakah dokter saat ini meresepkan obat untuk tekanan darah atau masalah jantungmu?', 'obat_jantung_tensi', 'radio', '[\"Ya\",\"Tidak\"]', 'Pilih salah satu', '1', NULL, '15', '2026-09-03 02:57:04', '2026-09-03 02:57:04');
REPLACE INTO `custom_form_fields` (`id`, `form_id`, `field_label`, `field_name`, `field_type`, `field_options`, `help_text`, `is_required`, `map_to_member_field`, `sort_order`, `created_at`, `updated_at`) VALUES ('16', '1', 'Apakah kamu mengetahui alasan lain mengapa kamu tidak boleh melakukan aktivitas fisik?', 'alasan_lain_aktivitas', 'radio', '[\"Ya\",\"Tidak\"]', 'Pilih salah satu', '1', NULL, '16', '2026-09-03 02:57:04', '2026-09-03 02:57:04');

-- 6. MENDAFTARKAN MENU MEJA SAYA & FORM SURVEI KE SETTINGS_MENUS
REPLACE INTO `settings_menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`) VALUES ('59', NULL, 'Meja Saya', 'fa-solid fa-laptop-user', NULL, '2', '2026-09-02 01:56:23', '2026-09-02 01:56:23');
REPLACE INTO `settings_menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`) VALUES ('60', '59', 'Profile', 'fa-solid fa-id-card', 'admin/meja/profile', '1', '2026-09-02 01:56:23', '2026-09-02 01:56:23');
REPLACE INTO `settings_menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`) VALUES ('61', '59', 'Mutabaah', 'fa-solid fa-book-quran', 'admin/meja/mutabaah', '2', '2026-09-02 01:56:23', '2026-09-02 01:56:23');
REPLACE INTO `settings_menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`) VALUES ('62', '59', 'Iuran', 'fa-solid fa-hand-holding-dollar', 'admin/meja/iuran', '3', '2026-09-02 01:56:23', '2026-09-02 01:56:23');
REPLACE INTO `settings_menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`) VALUES ('63', NULL, 'Form & Survei', 'fa-solid fa-clipboard-question', NULL, '8', '2026-09-03 02:56:23', '2026-09-03 02:56:23');
REPLACE INTO `settings_menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`) VALUES ('64', '63', 'Kelola Form', 'fa-solid fa-list-check', 'admin/forms', '1', '2026-09-03 02:56:23', '2026-09-03 02:56:23');
REPLACE INTO `settings_menus` (`id`, `parent_id`, `title`, `icon`, `url`, `sort_order`, `created_at`, `updated_at`) VALUES ('65', '63', 'Buat Form Baru', 'fa-solid fa-plus', 'admin/forms/create', '2', '2026-09-03 02:56:23', '2026-09-03 02:56:23');

-- 7. SETTINGS MENU ROLES (HAK AKSES SEMUA GROUP)
INSERT IGNORE INTO `settings_menu_roles` (`menu_id`, `group_name`, `created_at`, `updated_at`)
SELECT m.id, g.group_name, NOW(), NOW()
FROM `settings_menus` m
CROSS JOIN (SELECT DISTINCT `group_name` FROM `settings_menu_roles`) g
WHERE m.id IN (59, 60, 61, 62, 63, 64, 65);
