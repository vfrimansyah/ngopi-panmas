-- ====================================================================
-- Script Migrasi Tambahan: Relasi ID Wilayah pada Lembaga Profiles
-- File: _db/006_add_region_ids_to_lembaga_profiles.sql
-- Database: ngopipanmas
-- ====================================================================

ALTER TABLE `lembaga_profiles` 
ADD COLUMN `province_id` CHAR(2) NULL AFTER `alamat_lengkap`,
ADD COLUMN `regency_id` CHAR(4) NULL AFTER `province_id`,
ADD COLUMN `district_id` CHAR(6) NULL AFTER `regency_id`,
ADD COLUMN `village_id` CHAR(10) NULL AFTER `district_id`;
