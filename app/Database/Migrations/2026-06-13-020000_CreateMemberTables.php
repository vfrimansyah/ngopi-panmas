<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberTables extends Migration
{
    public function up()
    {
        // 1. members (Unified Person Model)
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'account_status' => [
                'type'       => 'ENUM',
                'constraint' => ['no_account', 'invited', 'active'],
                'default'    => 'no_account',
            ],
            'father_member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'mother_member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'spouse_member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'spouse_pairing_status' => [
                'type'       => 'ENUM',
                'constraint' => ['unmatched', 'suggested', 'confirmed', 'no_pair'],
                'default'    => 'unmatched',
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'null'       => true,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'usia_range' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status_anggota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kode_upa' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status_perkawinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'alamat_lengkap' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'kelurahan_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'kecamatan_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'kota_kabupaten_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'pekerjaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_instansi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'alamat_kerja' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'bidang_pekerjaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'keahlian' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pendapatan_range' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'pendidikan_terakhir' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'pendidikan_saat_ini' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'sekolah_universitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'jurusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'judul_skripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'mengikuti_mentoring' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
            ],
            'hobi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kode_upa', 'regions_upa', 'kode_upa', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kelurahan_id', 'cms_region04_villages', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kecamatan_id', 'cms_region03_districts', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kota_kabupaten_id', 'cms_region02_cities', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('members');

        // Self-referencing keys must be added manually in SQL to avoid circular constraint creation bugs in forge
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE members ADD CONSTRAINT fk_members_father FOREIGN KEY (father_member_id) REFERENCES members(id) ON DELETE SET NULL");
        $db->query("ALTER TABLE members ADD CONSTRAINT fk_members_mother FOREIGN KEY (mother_member_id) REFERENCES members(id) ON DELETE SET NULL");
        $db->query("ALTER TABLE members ADD CONSTRAINT fk_members_spouse FOREIGN KEY (spouse_member_id) REFERENCES members(id) ON DELETE SET NULL");

        // 2. member_organizations
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'nama_organisasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_organizations');

        // 3. member_program_interests
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'program_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_program_interests');

        // 4. member_political_profiles
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'status_pemilih_tetap' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'frekuensi_berita_politik' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'harapan_struktur' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_political_profiles');

        // 5. member_contributions
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'bersedia_sukseskan_program' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'catatan_kesediaan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'bidang_kontribusi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'jaringan_komunitas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_contributions');

        // 6. member_donation_preferences
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'kepatuhan_iuran' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kendala_iuran' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kesediaan_donasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'bentuk_donasi_lain' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'metode_donasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'donasi_anonim' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_donation_preferences');

        // 7. family_match_candidates
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_a_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'member_b_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'relation_type' => [
                'type'       => 'ENUM',
                'constraint' => ['spouse', 'parent_child'],
            ],
            'score' => [
                'type' => 'FLOAT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['suggested_high', 'suggested_low', 'confirmed', 'rejected'],
                'default'    => 'suggested_low',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_a_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('member_b_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('family_match_candidates');
    }

    public function down()
    {
        $this->forge->dropTable('family_match_candidates');
        $this->forge->dropTable('member_donation_preferences');
        $this->forge->dropTable('member_contributions');
        $this->forge->dropTable('member_political_profiles');
        $this->forge->dropTable('member_program_interests');
        $this->forge->dropTable('member_organizations');
        $this->forge->dropTable('members');
    }
}
