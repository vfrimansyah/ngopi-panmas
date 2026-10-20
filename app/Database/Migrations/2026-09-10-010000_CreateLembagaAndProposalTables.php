<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLembagaAndProposalTables extends Migration
{
    public function up()
    {
        // 1. TABEL LEMBAGA PROFILES (Yayasan / Lembaga / DKM / Organisasi Pemohon)
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'member_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nama_lembaga' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenis_lembaga' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'yayasan', // yayasan, dkm, majelis_taklim, lsm, paud, kelompok_masyarakat, lainnya
            ],
            'kategori_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true, // Pendidikan, Sosial Kemanusiaan, Keagamaan, Ekonomi/UMKM, dll.
            ],
            'alamat_lengkap' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kelurahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Pancoran Mas',
                'null'       => true,
            ],
            'kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Kota Depok',
                'null'       => true,
            ],
            'kode_pos' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'no_kontak' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'website' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // Pimpinan / Ketua Lembaga
            'nama_ketua' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nik_ketua' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'hp_ketua' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'npwp_ketua' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'nama_sekretaris' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_bendahara' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // Legalitas Dokumen
            'no_akta_notaris' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_notaris' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'tgl_akta' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'no_sk_kemenkumham' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'npwp_lembaga' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'nib' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            // Rekening Bank
            'nama_bank' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'no_rekening' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'atas_nama_rekening' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // File Uploads
            'file_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_ktp_ketua' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_npwp_ketua' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_npwp_lembaga' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_akta' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_sk_kemenkumham' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_nib' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_buku_rekening' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_domisili' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_susunan_pengurus' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('member_id');
        $this->forge->createTable('lembaga_profiles', true);

        // 2. TABEL PROPOSALS
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lembaga_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'member_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nomor_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tanggal_surat' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'perihal' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'Permohonan Bantuan Dana / Hibah',
            ],
            'judul_proposal' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'target_pengajuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pokir_dprd', // pokir_dprd, csr_perusahaan, lembaga_sosial, hibah_pemda, sponsor_umum
            ],
            'tujuan_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true, // Contoh: "Yth. Bpk. H. Anggota DPRD Kota Depok"
            ],
            'tujuan_instansi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true, // Contoh: "DPRD Kota Depok / PT. PLN (Persero) / Baznas"
            ],
            'tujuan_alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Narasi Proposal
            'latar_belakang' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'maksud_tujuan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deskripsi_kegiatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sasaran_penerima' => [
                'type' => 'TEXT',
                'null' => true, // Contoh: "150 Anak Yatim & Dhuafa di RW 05 Pancoran Mas"
            ],
            'waktu_pelaksanaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'lokasi_kegiatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'susunan_panitia' => [
                'type' => 'TEXT',
                'null' => true, // JSON atau text susunan tim pelaksana
            ],
            'penutup' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Anggaran
            'total_anggaran' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'terbilang_anggaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // Lampiran Checklist & Foto
            'selected_attachments' => [
                'type' => 'TEXT',
                'null' => true, // JSON checklist: ["ktp_ketua", "npwp_lembaga", "akta", "sk_kemenkumham", "nib", "buku_rekening"]
            ],
            'foto_dokumentasi' => [
                'type' => 'TEXT',
                'null' => true, // JSON array path foto rencana lokasi / kondisi fisik
            ],
            // Status Proposal
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'draft', // draft, diajukan, diverifikasi, disetujui, ditolak, selesai
            ],
            'catatan_admin' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('lembaga_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('member_id');
        $this->forge->addKey('target_pengajuan');
        $this->forge->addKey('status');
        $this->forge->createTable('proposals', true);

        // 3. TABEL PROPOSAL RABS (Rencana Anggaran Biaya)
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'proposal_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Umum', // Pengadaan Barang/Alat, Bahan & Material, Honor/Jasa, Konsumsi, Operasional, dsb.
            ],
            'uraian' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'volume' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 1.00,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Unit', // Unit, Paket, Orang, Hari, Pcs, Rim, Bulan, Titik, dsb.
            ],
            'harga_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('proposal_id');
        $this->forge->createTable('proposal_rabs', true);
    }

    public function down()
    {
        $this->forge->dropTable('proposal_rabs', true);
        $this->forge->dropTable('proposals', true);
        $this->forge->dropTable('lembaga_profiles', true);
    }
}
