<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed UPA Regions (Skip because populated via migrate:legacy-data)

        // 2. Seed Programs
        $programs = [
            ['nama_program' => 'Kajian Ibadah & Fiqih', 'is_active' => 1],
            ['nama_program' => 'Pelatihan & Kemitraan UMKM', 'is_active' => 1],
            ['nama_program' => 'Bakti Sosial & Layanan Kesehatan', 'is_active' => 1],
            ['nama_program' => 'Pemberdayaan Wanita & Parenting', 'is_active' => 1],
            ['nama_program' => 'Mentoring Wirausaha Muda', 'is_active' => 1],
        ];
        $this->db->table('programs')->insertBatch($programs);
        echo "Programs seeded successfully.\n";

        // 3. Seed Product Categories (UMKM)
        $productCategories = [
            ['nama_kategori' => 'Kopi Biji & Bubuk', 'is_active' => 1],
            ['nama_kategori' => 'Makanan & Kuliner', 'is_active' => 1],
            ['nama_kategori' => 'Pakaian & Mode', 'is_active' => 1],
            ['nama_kategori' => 'Jasa Penyeduhan & Catering', 'is_active' => 1],
            ['nama_kategori' => 'Peralatan & Aksesoris Kopi', 'is_active' => 1],
        ];
        $this->db->table('product_categories')->insertBatch($productCategories);
        echo "Product categories seeded successfully.\n";

        // 4. Seed Article Categories
        $articleCategories = [
            ['nama_kategori' => 'Tips & Trik Kopi', 'tipe' => 'artikel'],
            ['nama_kategori' => 'Artikel Islami', 'tipe' => 'artikel'],
            ['nama_kategori' => 'Info Kegiatan Struktur', 'tipe' => 'berita'],
            ['nama_kategori' => 'Pengumuman Komunitas', 'tipe' => 'berita'],
        ];
        $this->db->table('article_categories')->insertBatch($articleCategories);
        echo "Article categories seeded successfully.\n";

        // 5. Seed Mutaba'ah Items
        $mutabaahItems = [
            ['nama_item' => 'Shalat Fardhu Berjamaah', 'kategori' => 'Ibadah Wajib', 'tipe_input' => 'boolean', 'urutan' => 1, 'is_active' => 1],
            ['nama_item' => 'Qiyamul Lail (Tahajjud)', 'kategori' => 'Shalat Sunnah', 'tipe_input' => 'boolean', 'urutan' => 2, 'is_active' => 1],
            ['nama_item' => 'Tilawah Al-Qur\'an (Halaman)', 'kategori' => 'Al-Qur\'an', 'tipe_input' => 'angka', 'urutan' => 3, 'is_active' => 1],
            ['nama_item' => 'Dzikir Pagi & Petang', 'kategori' => 'Dzikir', 'tipe_input' => 'boolean', 'urutan' => 4, 'is_active' => 1],
            ['nama_item' => 'Shaum Sunnah (Senin/Kamis)', 'kategori' => 'Shaum', 'tipe_input' => 'boolean', 'urutan' => 5, 'is_active' => 1],
            ['nama_item' => 'Olahraga (Menit)', 'kategori' => 'Kesehatan', 'tipe_input' => 'angka', 'urutan' => 6, 'is_active' => 1],
        ];
        $this->db->table('mutabaah_items')->insertBatch($mutabaahItems);
        echo "Mutaba'ah Items seeded successfully.\n";
    }
}
