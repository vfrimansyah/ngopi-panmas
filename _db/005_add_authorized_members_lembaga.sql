-- ====================================================================
-- Script Migrasi Tambahan: Hak Akses Pengurus Berwenang pada Lembaga
-- File: _db/005_add_authorized_members_lembaga.sql
-- Database: ngopipanmas
-- ====================================================================

-- 1. Tambah kolom authorized_members dan is_public ke lembaga_profiles
ALTER TABLE `lembaga_profiles` 
ADD COLUMN `authorized_members` TEXT NULL COMMENT 'JSON array ID anggota berwenang membuat proposal' AFTER `file_susunan_pengurus`,
ADD COLUMN `is_public` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 jika terbuka untuk seluruh anggota, 0 jika terbatas' AFTER `authorized_members`;
