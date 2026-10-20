<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDpcModuleTables extends Migration
{
    public function up()
    {
        // ── 1. Table: dpc_pengurus ──────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'institusi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'DPC',
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'bidang_kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'periode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '2025-2030',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('institusi');
        $this->forge->createTable('dpc_pengurus', true);

        // ── 2. Table: dpc_bidang ────────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'nama_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
            ],
            'pic_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'fokus' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'link_match' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'monev_keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reward_keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('dpc_bidang', true);

        // ── 3. Table: dpc_proker ────────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bidang_kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'nama_program' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'waktu_frekuensi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'kpi_target' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pic_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'target_capaian' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'realisasi_capaian' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_monev' => [
                'type'       => 'ENUM',
                'constraint' => ['belum', 'berjalan', 'selesai', 'terkendala'],
                'default'    => 'belum',
            ],
            'persentase_progres' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_prioritas' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('bidang_kode');
        $this->forge->createTable('dpc_proker', true);

        // ── 4. Table: dpc_rab ───────────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bidang_kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'nama_program' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'waktu_frekuensi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'anggaran' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'realisasi_anggaran' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('bidang_kode');
        $this->forge->createTable('dpc_rab', true);

        // ── 5. Table: dpc_monev_logs ────────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'proker_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'periode_evaluasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'tanggal_evaluasi' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'persentase_progres' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'capaian_kpi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kendala' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tindak_lanjut' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'evaluator_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('proker_id');
        $this->forge->createTable('dpc_monev_logs', true);

        // ────────────────────────────────────────────────────────────────────
        // SEEDING DATA
        // ────────────────────────────────────────────────────────────────────
        $now = date('Y-m-d H:i:s');

        // 1. Seed Pengurus (47 pengurus: 17 DPC + 30 DPRa)
        $pengurusData = [
            // DPC
            ['nama' => 'Ilham', 'institusi' => 'DPC', 'jabatan' => 'Ketua', 'bidang_kode' => 'BPH', 'sort_order' => 1],
            ['nama' => 'Verdian', 'institusi' => 'DPC', 'jabatan' => 'Sekretaris', 'bidang_kode' => 'BPH', 'sort_order' => 2],
            ['nama' => 'M Faisal Hakim', 'institusi' => 'DPC', 'jabatan' => 'Wk Sekretaris 1', 'bidang_kode' => 'BPH', 'sort_order' => 3],
            ['nama' => 'Asep', 'institusi' => 'DPC', 'jabatan' => 'Wk Sekretaris 2', 'bidang_kode' => 'BPH', 'sort_order' => 4],
            ['nama' => 'Reza', 'institusi' => 'DPC', 'jabatan' => 'Bendahara', 'bidang_kode' => 'BPH', 'sort_order' => 5],
            ['nama' => 'Rahmat', 'institusi' => 'DPC', 'jabatan' => 'Wk Bendahara 1', 'bidang_kode' => 'BPH', 'sort_order' => 6],
            ['nama' => 'Ali', 'institusi' => 'DPC', 'jabatan' => 'Wk Bendahara 2', 'bidang_kode' => 'BPH', 'sort_order' => 7],
            ['nama' => 'A Hajandono', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SKAP', 'sort_order' => 8],
            ['nama' => 'Kartika Sari', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'BIPEKA', 'sort_order' => 9],
            ['nama' => 'Munawir Sajali', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SKBN', 'sort_order' => 10],
            ['nama' => 'M Faisal Hakim', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SP3', 'sort_order' => 11],
            ['nama' => 'Sayid Nouval', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SPPM', 'sort_order' => 12],
            ['nama' => 'Efaningsih', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'KOMDIGI', 'sort_order' => 13],
            ['nama' => 'Juarti', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SPdK', 'sort_order' => 14],
            ['nama' => 'Nasrul', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SUMKraf', 'sort_order' => 15],
            ['nama' => 'Ibnu Nugroho', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SPUKB', 'sort_order' => 16],
            ['nama' => 'Suroto', 'institusi' => 'DPC', 'jabatan' => 'Ketua Bidang', 'bidang_kode' => 'SPPN', 'sort_order' => 17],

            // DPRa Depok
            ['nama' => 'Syamsul Ma’arif', 'institusi' => 'DPRa Depok', 'jabatan' => 'Ketua', 'bidang_kode' => null, 'sort_order' => 18],
            ['nama' => 'Nur Rahman Shalahudin', 'institusi' => 'DPRa Depok', 'jabatan' => 'Sekretaris', 'bidang_kode' => null, 'sort_order' => 19],
            ['nama' => 'Yus Rizal Sukarno', 'institusi' => 'DPRa Depok', 'jabatan' => 'Bendahara', 'bidang_kode' => null, 'sort_order' => 20],
            ['nama' => 'Siti Nuroniah', 'institusi' => 'DPRa Depok', 'jabatan' => 'UPKK', 'bidang_kode' => null, 'sort_order' => 21],
            ['nama' => 'Ridwan', 'institusi' => 'DPRa Depok', 'jabatan' => 'Kaderisasi', 'bidang_kode' => null, 'sort_order' => 22],

            // DPRa Depok Jaya
            ['nama' => 'Dewa Anggara Hadi', 'institusi' => 'DPRa Depok Jaya', 'jabatan' => 'Ketua', 'bidang_kode' => null, 'sort_order' => 23],
            ['nama' => 'Suhendi', 'institusi' => 'DPRa Depok Jaya', 'jabatan' => 'Sekretaris', 'bidang_kode' => null, 'sort_order' => 24],
            ['nama' => 'Totok Rusmanto', 'institusi' => 'DPRa Depok Jaya', 'jabatan' => 'Bendahara', 'bidang_kode' => null, 'sort_order' => 25],
            ['nama' => 'Ismanita', 'institusi' => 'DPRa Depok Jaya', 'jabatan' => 'UPKK', 'bidang_kode' => null, 'sort_order' => 26],
            ['nama' => 'Hanief Al Ikhlas', 'institusi' => 'DPRa Depok Jaya', 'jabatan' => 'Kaderisasi', 'bidang_kode' => null, 'sort_order' => 27],

            // DPRa Panmas
            ['nama' => 'Mardi', 'institusi' => 'DPRa Pancoran Mas', 'jabatan' => 'Ketua', 'bidang_kode' => null, 'sort_order' => 28],
            ['nama' => 'Winarto', 'institusi' => 'DPRa Pancoran Mas', 'jabatan' => 'Sekretaris', 'bidang_kode' => null, 'sort_order' => 29],
            ['nama' => 'Salman Al Farisi', 'institusi' => 'DPRa Pancoran Mas', 'jabatan' => 'Bendahara', 'bidang_kode' => null, 'sort_order' => 30],
            ['nama' => 'Dwi Setyo Rini', 'institusi' => 'DPRa Pancoran Mas', 'jabatan' => 'UPKK', 'bidang_kode' => null, 'sort_order' => 31],
            ['nama' => 'Krisna', 'institusi' => 'DPRa Pancoran Mas', 'jabatan' => 'Kaderisasi', 'bidang_kode' => null, 'sort_order' => 32],

            // DPRa Mampang
            ['nama' => 'Hermanto', 'institusi' => 'DPRa Mampang', 'jabatan' => 'Ketua', 'bidang_kode' => null, 'sort_order' => 33],
            ['nama' => 'Ismail', 'institusi' => 'DPRa Mampang', 'jabatan' => 'Sekretaris', 'bidang_kode' => null, 'sort_order' => 34],
            ['nama' => 'Amigos', 'institusi' => 'DPRa Mampang', 'jabatan' => 'Bendahara', 'bidang_kode' => null, 'sort_order' => 35],
            ['nama' => 'Kartini', 'institusi' => 'DPRa Mampang', 'jabatan' => 'UPKK', 'bidang_kode' => null, 'sort_order' => 36],
            ['nama' => 'A Hidayat', 'institusi' => 'DPRa Mampang', 'jabatan' => 'Kaderisasi', 'bidang_kode' => null, 'sort_order' => 37],

            // DPRa Rangkapan Jaya (Panja)
            ['nama' => 'Zulkarnaen', 'institusi' => 'DPRa Rangkapan Jaya', 'jabatan' => 'Ketua', 'bidang_kode' => null, 'sort_order' => 38],
            ['nama' => 'Hisyam Sanusi', 'institusi' => 'DPRa Rangkapan Jaya', 'jabatan' => 'Sekretaris', 'bidang_kode' => null, 'sort_order' => 39],
            ['nama' => 'Asep', 'institusi' => 'DPRa Rangkapan Jaya', 'jabatan' => 'Bendahara', 'bidang_kode' => null, 'sort_order' => 40],
            ['nama' => 'Andriyani', 'institusi' => 'DPRa Rangkapan Jaya', 'jabatan' => 'UPKK', 'bidang_kode' => null, 'sort_order' => 41],
            ['nama' => 'Hisyam Sanusi', 'institusi' => 'DPRa Rangkapan Jaya', 'jabatan' => 'Kaderisasi', 'bidang_kode' => null, 'sort_order' => 42],

            // DPRa Rangkapan Jaya Baru (Panjaru)
            ['nama' => 'Haliluloh', 'institusi' => 'DPRa Rangkapan Jaya Baru', 'jabatan' => 'Ketua', 'bidang_kode' => null, 'sort_order' => 43],
            ['nama' => 'Rosian Novianto', 'institusi' => 'DPRa Rangkapan Jaya Baru', 'jabatan' => 'Sekretaris', 'bidang_kode' => null, 'sort_order' => 44],
            ['nama' => 'Khairul Rakhman', 'institusi' => 'DPRa Rangkapan Jaya Baru', 'jabatan' => 'Bendahara', 'bidang_kode' => null, 'sort_order' => 45],
            ['nama' => 'Kholilah', 'institusi' => 'DPRa Rangkapan Jaya Baru', 'jabatan' => 'UPKK', 'bidang_kode' => null, 'sort_order' => 46],
            ['nama' => 'Muhammad Faisal', 'institusi' => 'DPRa Rangkapan Jaya Baru', 'jabatan' => 'Kaderisasi', 'bidang_kode' => null, 'sort_order' => 47],
        ];

        foreach ($pengurusData as &$p) {
            $p['periode'] = '2025-2030';
            $p['created_at'] = $now;
            $p['updated_at'] = $now;
        }
        $this->db->table('dpc_pengurus')->insertBatch($pengurusData);

        // 2. Seed 10 Bidang DPC
        $bidangData = [
            [
                'kode' => 'SKAP',
                'nama_bidang' => 'SKAP – Kaderisasi Anggota Partai',
                'pic_nama' => 'Agung Hajandono, SE',
                'fokus' => 'Penguatan kaderisasi & kualitas UPA (UPA naik level)',
                'link_match' => 'Potensi kader (skill/minat) ↔ kebutuhan peran wilayah (RT/RW/DKM/LPM/komunitas) ↔ penugasan via DPRa + dukungan Aleg',
                'monev_keterangan' => 'Rapat evaluasi UPA bulanan + rekap progres per triwulan',
                'reward_keterangan' => 'UPA Terbaik & UPA Most Improved per semester',
                'sort_order' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'SPUKB',
                'nama_bidang' => 'SPUKB – Pembinaan Umat & Kerukunan Beragama',
                'pic_nama' => 'H. Ibnu Nugroho Qomardjati',
                'fokus' => 'Jejaring tokoh/masjid + harmonisasi umat sebagai penguat lintas program',
                'link_match' => 'Simpul umat (tokoh/masjid/majelis) ↔ kebutuhan program DPC (RKI, AKAR, Join PKS, GEMA) ↔ kader/UPA pelaksana',
                'monev_keterangan' => 'Koordinasi bulanan dengan KOMDIGI (bank isu & publikasi) + laporan ringkas kegiatan',
                'reward_keterangan' => 'Simpul Umat Kolaboratif Terbaik per semester',
                'sort_order' => 2,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'SPPM',
                'nama_bidang' => 'SPPM – Pemuda, Pelajar & Mahasiswa',
                'pic_nama' => 'Khoirul Umam, S.Pd, M.T',
                'fokus' => 'Rekrutmen & pembinaan pemuda berbasis komunitas (Join PKS)',
                'link_match' => 'Minat pemuda ↔ komunitas/isu warga ↔ program layanan (AKAR/RKI/GEMA) ↔ pembinaan lanjutan SKAP',
                'monev_keterangan' => 'Evaluasi bulanan funnel (datang–follow-up–aktif) bareng KOMDIGI & SKAP',
                'reward_keterangan' => 'Komunitas Pemuda Teraktif per semester',
                'sort_order' => 3,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'SUMKraf',
                'nama_bidang' => 'SUMKraf – UMKM, Ekonomi Kreatif & Kader',
                'pic_nama' => 'Nasrul, S.Si',
                'fokus' => 'Ekonomi mandiri kader & UMKM naik kelas (GEMA)',
                'link_match' => 'UMKM/pencari kerja ↔ pelatihan/mentor ↔ pasar/komunitas ↔ simpul masjid/RW (SPUKB)',
                'monev_keterangan' => 'Rekap triwulan (omzet/kemajuan self-report + kendala)',
                'reward_keterangan' => 'UMKM Terinovatif & UMKM Terkonsisten per semester',
                'sort_order' => 4,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'SP3',
                'nama_bidang' => 'SP3 – Pemenangan Pemilu & Pilkada',
                'pic_nama' => 'Faisal Hakim',
                'fokus' => 'JAWARA: pemetaan basis, relawan, konsolidasi pemenangan (rapi & data-driven)',
                'link_match' => 'Basis wilayah DPRa ↔ isu & layanan (AKAR/RKI/GEMA) ↔ penguatan simpul relawan ↔ dukungan Aleg',
                'monev_keterangan' => 'Evaluasi triwulan data basis + progres relawan (bareng KOMDIGI)',
                'reward_keterangan' => 'DPRa Paling Rapi Data & Relawan Teladan per semester',
                'sort_order' => 5,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'SKBN',
                'nama_bidang' => 'SKBN – Kepanduan & Bela Negara',
                'pic_nama' => 'Munawir Sajali',
                'fokus' => 'API: kepemimpinan lapangan, disiplin kader, kesiapsiagaan kegiatan',
                'link_match' => 'Kader potensial (SKAP/SPPM/BIPEKA) ↔ kebutuhan koordinator kegiatan/relawan ↔ penugasan di program DPC',
                'monev_keterangan' => 'Evaluasi pasca-kegiatan (AAR: after action review) + rekap per triwulan',
                'reward_keterangan' => 'Kader Teladan Kepemimpinan Lapangan per semester',
                'sort_order' => 6,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'SPPN',
                'nama_bidang' => 'SPPN – Petani, Peternak & Nelayan',
                'pic_nama' => 'Suroto',
                'fokus' => 'Ketahanan pangan & basis komunitas produktif (PANGAN TANGGUH)',
                'link_match' => 'Warga butuh pangan ↔ UMKM pangan ↔ relawan komunitas ↔ venue masjid/RW',
                'monev_keterangan' => 'Monitoring tiap kegiatan + evaluasi triwulan demoplot/relawan',
                'reward_keterangan' => 'Komunitas Pangan Teraktif & Relawan Pangan Teladan per semester',
                'sort_order' => 7,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'BIPEKA',
                'nama_bidang' => 'BIPEKA – Perempuan & Keluarga',
                'pic_nama' => 'Kartika Sari, S.Pd.I',
                'fokus' => 'RKI: ketahanan keluarga kader & layanan keluarga warga',
                'link_match' => 'Kebutuhan keluarga ↔ narasumber (SPdK/SPUKB) ↔ venue komunitas ↔ follow-up via UPA/DPRa',
                'monev_keterangan' => 'Evaluasi setelah tiap kelas + rekap semester dampingan',
                'reward_keterangan' => 'Keluarga Teladan / UPA Ramah Keluarga per semester',
                'sort_order' => 8,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'KOMDIGI',
                'nama_bidang' => 'KOMDIGI – Komunikasi & Digital',
                'pic_nama' => 'Efaningsih, A.Md',
                'fokus' => 'TOTAL-PKS (data & laporan) + AKAR (bank isu & publikasi advokasi)',
                'link_match' => 'Input DPRa/UPA ↔ pengolahan data DPC ↔ tindak lanjut Aleg/seksi terkait ↔ publikasi yang menyejukkan & solutif',
                'monev_keterangan' => 'Rapat data bulanan (15–30 menit) + audit kerapihan dokumen per triwulan',
                'reward_keterangan' => 'DPRa/UPA Paling Tertib Data per semester',
                'sort_order' => 9,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'kode' => 'SPdK',
                'nama_bidang' => 'SPdK – Pendidikan & Kesehatan',
                'pic_nama' => 'Juati Nurmalasari, M.Pd',
                'fokus' => 'Penguatan layanan pendidikan & kesehatan sebagai wajah pelayanan DPC (sinkron RKI & AKAR)',
                'link_match' => 'Kebutuhan warga ↔ relawan/mitra (sekolah, puskesmas, tenaga kesehatan) ↔ venue masjid/RW ↔ publikasi KOMDIGI ↔ follow-up UPA/DPRa',
                'monev_keterangan' => 'Evaluasi tiap batch + rekap triwulan layanan',
                'reward_keterangan' => 'Relawan Pendidikan/Kesehatan Teladan per semester',
                'sort_order' => 10,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ];
        $this->db->table('dpc_bidang')->insertBatch($bidangData);

        // 3. Seed Program Kerja (Matriks 10 Bidang & 7 Prioritas)
        $prokerData = [
            // SKAP
            [
                'bidang_kode' => 'SKAP',
                'nama_program' => 'SAKTI DPC (Kelas Kader/Upgrade)',
                'deskripsi' => 'Penguatan kaderisasi & kelas peningkatan kapasitas kader',
                'waktu_frekuensi' => 'Per triwulan',
                'kpi_target' => '- Pelatihan kader/UPA: 4–6/tahun\n- Kader masuk peran strategis di RT/RW/DKM/LPM',
                'pic_nama' => 'Agung Hajandono, SE',
                'status_monev' => 'berjalan',
                'persentase_progres' => 35,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SKAP',
                'nama_program' => 'Training Pembina UPA',
                'deskripsi' => 'Materi, metode, dan evaluasi pembina UPA se-Kecamatan Panmas',
                'waktu_frekuensi' => '2x/tahun (semesteran)',
                'kpi_target' => '15–30 pembina terlatih + ≥70% UPA punya pembina terlatih',
                'pic_nama' => 'Agung Hajandono, SE',
                'status_monev' => 'berjalan',
                'persentase_progres' => 50,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'SKAP',
                'nama_program' => '“Gereget” Rekrutmen & Penguatan Anggota',
                'deskripsi' => 'Aktivasi rekrutmen anggota baru dan follow-up pembinaan',
                'waktu_frekuensi' => 'Sepanjang tahun',
                'kpi_target' => 'Target anggota baru pleno + follow-up ≥70%',
                'pic_nama' => 'Agung Hajandono, SE',
                'status_monev' => 'berjalan',
                'persentase_progres' => 40,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'SKAP',
                'nama_program' => 'Talent Pool Kader',
                'deskripsi' => 'Database kader siap peran strategis di lingkungan dan publik',
                'waktu_frekuensi' => 'Update per semester',
                'kpi_target' => '% UPA melakukan evaluasi rutin ≥70%',
                'pic_nama' => 'Agung Hajandono, SE',
                'status_monev' => 'belum',
                'persentase_progres' => 20,
                'is_prioritas' => 0,
            ],

            // SPUKB
            [
                'bidang_kode' => 'SPUKB',
                'nama_program' => 'Silaturahmi Ormas Islam & Non-Islam',
                'deskripsi' => 'Membangun jejaring dan kolaborasi dengan simpul ormas keagamaan',
                'waktu_frekuensi' => '2x/tahun',
                'kpi_target' => '≥10 simpul ormas + ≥3 kolaborasi program',
                'pic_nama' => 'H. Ibnu Nugroho Qomardjati',
                'status_monev' => 'berjalan',
                'persentase_progres' => 50,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'SPUKB',
                'nama_program' => 'Silaturahmi Tokoh Masyarakat',
                'deskripsi' => 'Safari silaturahim ke tokoh masyarakat, kiai, dan ustadz',
                'waktu_frekuensi' => '2x/tahun',
                'kpi_target' => '≥20 tokoh + ≥6 simpul kolaborasi',
                'pic_nama' => 'H. Ibnu Nugroho Qomardjati',
                'status_monev' => 'berjalan',
                'persentase_progres' => 45,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'SPUKB',
                'nama_program' => 'IKADI / Daurah Khutoba',
                'deskripsi' => 'Pelatihan dan standardisasi da’i/khatib siap terjun di masyarakat',
                'waktu_frekuensi' => '1x/tahun',
                'kpi_target' => 'Pool da’i siap khutbah (pleno)',
                'pic_nama' => 'H. Ibnu Nugroho Qomardjati',
                'status_monev' => 'belum',
                'persentase_progres' => 0,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'SPUKB',
                'nama_program' => 'JARING UMAT (Safari Masjid)',
                'deskripsi' => 'Safari silaturahim ke DKM dan jamaah masjid',
                'waktu_frekuensi' => '2–3 titik/bulan',
                'kpi_target' => 'Tokoh/simpul aktif terdata ≥50',
                'pic_nama' => 'H. Ibnu Nugroho Qomardjati',
                'status_monev' => 'berjalan',
                'persentase_progres' => 30,
                'is_prioritas' => 0,
            ],

            // SPPM
            [
                'bidang_kode' => 'SPPM',
                'nama_program' => 'Komunitas Skill Pemuda',
                'deskripsi' => 'Pelatihan konten kreatif, public speaking, leadership & volunteer pemuda',
                'waktu_frekuensi' => '1 event/bulan',
                'kpi_target' => 'Peserta baru masuk funnel ≥200/tahun',
                'pic_nama' => 'Khoirul Umam, S.Pd, M.T',
                'status_monev' => 'berjalan',
                'persentase_progres' => 25,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SPPM',
                'nama_program' => 'UPA Muda (Circle Pembinaan)',
                'deskripsi' => 'Follow-up peserta event menjadi circle & UPA muda',
                'waktu_frekuensi' => 'Minimal 1 gelombang/semester',
                'kpi_target' => 'Follow-up jadi circle/UPA muda ≥3–6 circle',
                'pic_nama' => 'Khoirul Umam, S.Pd, M.T',
                'status_monev' => 'belum',
                'persentase_progres' => 15,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SPPM',
                'nama_program' => 'Aksi Sosial Pemuda',
                'deskripsi' => 'Kolaborasi kepemudaan dalam aksi sosial kemasyarakatan',
                'waktu_frekuensi' => 'Per triwulan',
                'kpi_target' => 'Kader muda aktif jadi penggerak kegiatan wilayah',
                'pic_nama' => 'Khoirul Umam, S.Pd, M.T',
                'status_monev' => 'belum',
                'persentase_progres' => 0,
                'is_prioritas' => 0,
            ],

            // SUMKraf
            [
                'bidang_kode' => 'SUMKraf',
                'nama_program' => 'Pendataan UMKM & Kebutuhan Kerja',
                'deskripsi' => 'Database direktori UMKM kader & warga serta info kerja',
                'waktu_frekuensi' => 'Update triwulan (Apr–Des)',
                'kpi_target' => 'UMKM terdata ≥50, UMKM aktif ≥20',
                'pic_nama' => 'Nasrul, S.Si',
                'status_monev' => 'berjalan',
                'persentase_progres' => 30,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SUMKraf',
                'nama_program' => 'UMKM Naik Kelas (Mentoring Batch)',
                'deskripsi' => 'Mentoring bisnis, legalitas, permodalan & packaging produk',
                'waktu_frekuensi' => '3 batch/tahun',
                'kpi_target' => '3 batch mentoring selesai',
                'pic_nama' => 'Nasrul, S.Si',
                'status_monev' => 'belum',
                'persentase_progres' => 10,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SUMKraf',
                'nama_program' => 'Market Day / Bazaar & Kelas Branding',
                'deskripsi' => 'Bazaar produk UMKM & kelas digital branding bareng KOMDIGI',
                'waktu_frekuensi' => 'Per triwulan (4x)',
                'kpi_target' => 'Kolaborasi penjualan/event ≥4x',
                'pic_nama' => 'Nasrul, S.Si',
                'status_monev' => 'belum',
                'persentase_progres' => 0,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SUMKraf',
                'nama_program' => 'Warung Siape Aje',
                'deskripsi' => 'Pusat etalase dan perputaran ekonomi komunitas warga',
                'waktu_frekuensi' => 'Apr–Des',
                'kpi_target' => 'Aktivasi outlet & omzet berkelanjutan',
                'pic_nama' => 'Nasrul, S.Si',
                'status_monev' => 'berjalan',
                'persentase_progres' => 40,
                'is_prioritas' => 1,
            ],

            // SP3
            [
                'bidang_kode' => 'SP3',
                'nama_program' => 'Peta Basis per Kelurahan / RT-RW',
                'deskripsi' => 'Pemetaan data pemilih, TPS prioritas, dan peta kekuatan wilayah',
                'waktu_frekuensi' => 'Update triwulan (Mar–Mei)',
                'kpi_target' => '% basis prioritas terdata & update ≥80% area',
                'pic_nama' => 'Faisal Hakim',
                'status_monev' => 'berjalan',
                'persentase_progres' => 60,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SP3',
                'nama_program' => 'Konsolidasi Relawan & Saksi',
                'deskripsi' => 'Konsolidasi simpul relawan pemenangan dan saksi TPS',
                'waktu_frekuensi' => '2–3x/tahun (Mar–Des)',
                'kpi_target' => 'Kegiatan konsolidasi 2–3x, pembentukan relawan aktif',
                'pic_nama' => 'Faisal Hakim',
                'status_monev' => 'belum',
                'persentase_progres' => 20,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SP3',
                'nama_program' => 'Aktivasi Basis Lewat Layanan Warga',
                'deskripsi' => 'Sinkronisasi layanan AKAR, RKI, dan GEMA di wilayah prioritas',
                'waktu_frekuensi' => 'Bulanan (Mar–Des)',
                'kpi_target' => 'Aktivasi layanan rutin di 6 kelurahan',
                'pic_nama' => 'Faisal Hakim',
                'status_monev' => 'berjalan',
                'persentase_progres' => 30,
                'is_prioritas' => 0,
            ],

            // SKBN
            [
                'bidang_kode' => 'SKBN',
                'nama_program' => 'Leadership Camp',
                'deskripsi' => 'Pelatihan kepemimpinan lapangan dan ketahanan fisik kader',
                'waktu_frekuensi' => '2 gelombang/tahun',
                'kpi_target' => 'Peserta lulus camp siap jadi koordinator lapangan',
                'pic_nama' => 'Munawir Sajali',
                'status_monev' => 'belum',
                'persentase_progres' => 0,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SKBN',
                'nama_program' => 'Pelatihan Kedisiplinan & Protokoler Lapangan',
                'deskripsi' => 'Standardisasi SOP protokoler event dan pengamanan kegiatan',
                'waktu_frekuensi' => 'Per triwulan',
                'kpi_target' => 'Kegiatan yang pakai SOP & tim lapangan ≥70% event besar',
                'pic_nama' => 'Munawir Sajali',
                'status_monev' => 'berjalan',
                'persentase_progres' => 35,
                'is_prioritas' => 0,
            ],

            // SPPN
            [
                'bidang_kode' => 'SPPN',
                'nama_program' => 'Demoplot Urban Farming',
                'deskripsi' => 'Pembuatan kebun percontohan pertanian perkotaan',
                'waktu_frekuensi' => '3 titik/tahun',
                'kpi_target' => 'Demoplot aktif ≥3 titik',
                'pic_nama' => 'Suroto',
                'status_monev' => 'belum',
                'persentase_progres' => 15,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'SPPN',
                'nama_program' => 'Kelas Pangan Tangguh & Pangan Murah',
                'deskripsi' => 'Pelatihan kompos/budidaya & penjualan sembako berkah terjangkau',
                'waktu_frekuensi' => 'Bulanan (6–12x/tahun)',
                'kpi_target' => 'Kegiatan pangan berkah: 6–12 event',
                'pic_nama' => 'Suroto',
                'status_monev' => 'berjalan',
                'persentase_progres' => 25,
                'is_prioritas' => 0,
            ],

            // BIPEKA
            [
                'bidang_kode' => 'BIPEKA',
                'nama_program' => 'SELARAS / Parenting Class',
                'deskripsi' => 'Kelas edukasi parenting dan ketahanan keluarga sakinah',
                'waktu_frekuensi' => 'Per triwulan',
                'kpi_target' => 'Kelas keluarga: 4–6 batch, kepuasan ≥80%',
                'pic_nama' => 'Kartika Sari, S.Pd.I',
                'status_monev' => 'berjalan',
                'persentase_progres' => 30,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'BIPEKA',
                'nama_program' => 'Konsultan Keluarga (RKI)',
                'deskripsi' => 'Pendampingan dan konseling keluarga ringan terjadwal',
                'waktu_frekuensi' => '2x/bulan',
                'kpi_target' => '≥24 sesi/tahun, ≥50 keluarga terlayani, kepuasan ≥80%',
                'pic_nama' => 'Kartika Sari, S.Pd.I',
                'status_monev' => 'berjalan',
                'persentase_progres' => 40,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'BIPEKA',
                'nama_program' => 'Sekolah Pra Nikah',
                'deskripsi' => 'Pembekalan pranikah bagi pemuda dan calon pengantin',
                'waktu_frekuensi' => '1x/tahun',
                'kpi_target' => '≥40 peserta, ≥70% lulus, follow-up group',
                'pic_nama' => 'Kartika Sari, S.Pd.I',
                'status_monev' => 'belum',
                'persentase_progres' => 0,
                'is_prioritas' => 1,
            ],

            // KOMDIGI
            [
                'bidang_kode' => 'KOMDIGI',
                'nama_program' => 'TOTAL-PKS Lite & Dashboard Progres',
                'deskripsi' => 'Template database & laporan DPC–DPRa–UPA serta dashboard progres',
                'waktu_frekuensi' => 'Update bulanan',
                'kpi_target' => '% DPRa/UPA tertib laporan ≥70%',
                'pic_nama' => 'Efaningsih, A.Md',
                'status_monev' => 'berjalan',
                'persentase_progres' => 65,
                'is_prioritas' => 0,
            ],
            [
                'bidang_kode' => 'KOMDIGI',
                'nama_program' => 'Sekolah Digital',
                'deskripsi' => 'Pelatihan skill digital kader (desain, video, medsos, AI copy)',
                'waktu_frekuensi' => 'Apr–Des (9x)',
                'kpi_target' => '9 sesi sekolah digital, 50+ kader terampil digital',
                'pic_nama' => 'Efaningsih, A.Md',
                'status_monev' => 'berjalan',
                'persentase_progres' => 30,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'KOMDIGI',
                'nama_program' => 'AKAR: Bank Isu Warga & Publikasi Advokasi',
                'deskripsi' => 'Penyerapan aspirasi masalah warga & publikasi solusi advokasi',
                'waktu_frekuensi' => 'Minimal 1 konten/bulan (12/tahun)',
                'kpi_target' => 'Isu masuk & follow-up ≥60%, konten rutin ≥12/tahun',
                'pic_nama' => 'Efaningsih, A.Md',
                'status_monev' => 'berjalan',
                'persentase_progres' => 45,
                'is_prioritas' => 0,
            ],

            // SPdK
            [
                'bidang_kode' => 'SPdK',
                'nama_program' => 'Bimbel / Kelas Belajar Anak & Remaja',
                'deskripsi' => 'Bimbingan belajar gratis untuk anak & remaja warga',
                'waktu_frekuensi' => 'Apr–Mei, Juni',
                'kpi_target' => 'Peserta bimbel 100+ anak warga',
                'pic_nama' => 'Juati Nurmalasari, M.Pd',
                'status_monev' => 'berjalan',
                'persentase_progres' => 35,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'SPdK',
                'nama_program' => 'Layanan Cek Kesehatan & Donor Darah',
                'deskripsi' => 'Pemeriksaan kesehatan gratis di 6 titik kelurahan dan donor darah',
                'waktu_frekuensi' => '2–4x/tahun',
                'kpi_target' => 'Pelayanan kesehatan di 6 titik + 2x donor darah',
                'pic_nama' => 'Juati Nurmalasari, M.Pd',
                'status_monev' => 'berjalan',
                'persentase_progres' => 20,
                'is_prioritas' => 1,
            ],
            [
                'bidang_kode' => 'SPdK',
                'nama_program' => 'Beasiswa Pendidikan & Pendampingan BPJS',
                'deskripsi' => 'Beasiswa untuk 10 siswa dhuafa & advokasi BPJS 200 kader/warga',
                'waktu_frekuensi' => 'Apr–Des',
                'kpi_target' => '10 siswa penerima beasiswa & 200 advokasi BPJS',
                'pic_nama' => 'Juati Nurmalasari, M.Pd',
                'status_monev' => 'berjalan',
                'persentase_progres' => 30,
                'is_prioritas' => 1,
            ],
        ];

        foreach ($prokerData as &$pr) {
            $pr['created_at'] = $now;
            $pr['updated_at'] = $now;
        }
        $this->db->table('dpc_proker')->insertBatch($prokerData);

        // 4. Seed RAB Program Prioritas (22 Item, Total Rp 189.600.000)
        $rabData = [
            // KOMDIGI (Total: 36.500.000)
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Sekolah Digital 9x',                  'waktu_frekuensi' => 'Apr–Des',     'anggaran' => 13500000.00, 'realisasi_anggaran' => 3000000.00, 'keterangan' => 'Pelatihan berkala'],
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Liputan video kegiatan 20x',           'waktu_frekuensi' => 'Mar–Des',     'anggaran' => 2000000.00,  'realisasi_anggaran' => 500000.00,  'keterangan' => 'Dokumentasi & video editing'],
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Flyer hari besar 9x',                  'waktu_frekuensi' => '-',           'anggaran' => 1000000.00,  'realisasi_anggaran' => 300000.00,  'keterangan' => 'Desain ucapan & publikasi'],
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Press release kegiatan besar 9x',      'waktu_frekuensi' => '-',           'anggaran' => 1000000.00,  'realisasi_anggaran' => 200000.00,  'keterangan' => 'Media relation'],
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Konten isu trending 9x',               'waktu_frekuensi' => '-',           'anggaran' => 1000000.00,  'realisasi_anggaran' => 150000.00,  'keterangan' => 'Respon cepat isu'],
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Podcast 4x',                           'waktu_frekuensi' => '-',           'anggaran' => 8000000.00,  'realisasi_anggaran' => 2000000.00, 'keterangan' => 'Studio & narasumber'],
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Lomba video promosi produk',           'waktu_frekuensi' => 'Agustus',     'anggaran' => 5000000.00,  'realisasi_anggaran' => 0.00,       'keterangan' => 'Hadiah & juri'],
            ['bidang_kode' => 'KOMDIGI', 'nama_program' => 'Konten kreatif campaign 5x',           'waktu_frekuensi' => '-',           'anggaran' => 5000000.00,  'realisasi_anggaran' => 1000000.00, 'keterangan' => 'Campaign sosial'],

            // SPdK (Total: 100.600.000)
            ['bidang_kode' => 'SPdK',    'nama_program' => 'Bimbel',                               'waktu_frekuensi' => 'Apr–Mei, Juni','anggaran' => 12000000.00, 'realisasi_anggaran' => 4000000.00, 'keterangan' => 'Honor tutor & modul'],
            ['bidang_kode' => 'SPdK',    'nama_program' => 'Parenting 2x',                         'waktu_frekuensi' => '-',           'anggaran' => 7000000.00,  'realisasi_anggaran' => 0.00,       'keterangan' => 'Narasumber & konsumsi'],
            ['bidang_kode' => 'SPdK',    'nama_program' => 'Beasiswa 10 siswa',                   'waktu_frekuensi' => 'Apr–Des',     'anggaran' => 45000000.00, 'realisasi_anggaran' => 15000000.00,'keterangan' => 'Bantuan SPP & perlengkapan'],
            ['bidang_kode' => 'SPdK',    'nama_program' => 'Pelayanan kesehatan 6 titik',          'waktu_frekuensi' => '-',           'anggaran' => 18000000.00, 'realisasi_anggaran' => 6000000.00, 'keterangan' => 'Obat & alat cek kesehatan'],
            ['bidang_kode' => 'SPdK',    'nama_program' => 'Senam 6 titik',                        'waktu_frekuensi' => '-',           'anggaran' => 6000000.00,  'realisasi_anggaran' => 2000000.00, 'keterangan' => 'Instruktur & sound'],
            ['bidang_kode' => 'SPdK',    'nama_program' => 'Donor darah 2x',                       'waktu_frekuensi' => '-',           'anggaran' => 2600000.00,  'realisasi_anggaran' => 1300000.00, 'keterangan' => 'Kerjasama PMI'],
            ['bidang_kode' => 'SPdK',    'nama_program' => 'Pendamping BPJS 200 kader',            'waktu_frekuensi' => '-',           'anggaran' => 10000000.00, 'realisasi_anggaran' => 2500000.00, 'keterangan' => 'Advokasi & operasional'],

            // SUMKraf (Total: 34.500.000)
            ['bidang_kode' => 'SUMKraf', 'nama_program' => 'Warung Siape Aje',                      'waktu_frekuensi' => 'Apr–Des',     'anggaran' => 25000000.00, 'realisasi_anggaran' => 8000000.00, 'keterangan' => 'Modal awal & operasional'],
            ['bidang_kode' => 'SUMKraf', 'nama_program' => 'Pendataan UMKM',                       'waktu_frekuensi' => 'Apr–Des',     'anggaran' => 0.00,        'realisasi_anggaran' => 0.00,       'keterangan' => 'RAB menyusul*'],
            ['bidang_kode' => 'SUMKraf', 'nama_program' => 'UMKM Naik Kelas + Mentoring 3x',       'waktu_frekuensi' => '-',           'anggaran' => 1500000.00,  'realisasi_anggaran' => 500000.00,  'keterangan' => 'Honor mentor'],
            ['bidang_kode' => 'SUMKraf', 'nama_program' => 'Market Day 4x',                         'waktu_frekuensi' => '-',           'anggaran' => 8000000.00,  'realisasi_anggaran' => 2000000.00, 'keterangan' => 'Tenda & panggung bazaar'],

            // SP3 (Total: 18.000.000)
            ['bidang_kode' => 'SP3',     'nama_program' => 'Pemetaan Basis',                       'waktu_frekuensi' => 'Mar–Mei',     'anggaran' => 0.00,        'realisasi_anggaran' => 0.00,       'keterangan' => 'RAB menyusul*'],
            ['bidang_kode' => 'SP3',     'nama_program' => 'Konsolidasi Relawan',                  'waktu_frekuensi' => 'Mar–Des',     'anggaran' => 9000000.00,  'realisasi_anggaran' => 3000000.00, 'keterangan' => 'Konsumsi & venue'],
            ['bidang_kode' => 'SP3',     'nama_program' => 'Aktivasi Basis lewat layanan warga',   'waktu_frekuensi' => 'Mar–Des',     'anggaran' => 9000000.00,  'realisasi_anggaran' => 3000000.00, 'keterangan' => 'Operasional pelayanan'],
        ];

        foreach ($rabData as &$r) {
            $r['created_at'] = $now;
            $r['updated_at'] = $now;
        }
        $this->db->table('dpc_rab')->insertBatch($rabData);

        // 5. Seed Initial Monev Logs (Sample data for reporting)
        $sampleLogs = [
            [
                'proker_id' => 1,
                'periode_evaluasi' => 'Triwulan 1 2025',
                'tanggal_evaluasi' => date('Y-03-31'),
                'persentase_progres' => 35,
                'capaian_kpi' => 'Telah terlaksana 1 gelombang SAKTI DPC diikuti 45 kader dari 6 DPRa.',
                'kendala' => 'Jadwal sebagian kader bentrok dengan agenda pekerjaan.',
                'tindak_lanjut' => 'Menyelenggarakan sesi hybrid atau malam hari untuk materi lanjutan.',
                'evaluator_nama' => 'Sekretaris DPC',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'proker_id' => 5,
                'periode_evaluasi' => 'Triwulan 1 2025',
                'tanggal_evaluasi' => date('Y-03-31'),
                'persentase_progres' => 50,
                'capaian_kpi' => 'Silaturahmi ke 5 simpul ormas Islam di wilayah Panmas berhasil dilaksanakan.',
                'kendala' => 'Beberapa pengurus ormas perlu penyesuaian waktu pertemuan.',
                'tindak_lanjut' => 'Melanjutkan silaturahmi ke simpul non-Islam dan ormas kepemudaan.',
                'evaluator_nama' => 'Ketua DPC',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'proker_id' => 27,
                'periode_evaluasi' => 'Triwulan 1 2025',
                'tanggal_evaluasi' => date('Y-03-31'),
                'persentase_progres' => 65,
                'capaian_kpi' => 'Dashboard pelaporan digital dan bank isu warga telah online dan diuji coba.',
                'kendala' => 'Perlu edukasi pengisian form input data untuk sekretaris DPRa.',
                'tindak_lanjut' => 'Mengadakan coaching klinik input data di sekolah digital KOMDIGI.',
                'evaluator_nama' => 'Ketua Bidang KOMDIGI',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ];
        $this->db->table('dpc_monev_logs')->insertBatch($sampleLogs);
    }

    public function down()
    {
        $this->forge->dropTable('dpc_monev_logs', true);
        $this->forge->dropTable('dpc_rab', true);
        $this->forge->dropTable('dpc_proker', true);
        $this->forge->dropTable('dpc_bidang', true);
        $this->forge->dropTable('dpc_pengurus', true);
    }
}
