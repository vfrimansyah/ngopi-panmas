<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKeuanganTables extends Migration
{
    public function up()
    {
        // ─── 1. fin_accounts (Chart of Accounts) ───────────────────────────
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kode_akun'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama_akun'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'tipe_akun'      => ['type' => 'ENUM', 'constraint' => ['kas','bank','piutang','pendapatan','beban','modal','hutang'], 'default' => 'beban'],
            'normal_balance' => ['type' => 'ENUM', 'constraint' => ['debit','kredit'], 'default' => 'debit'],
            'deskripsi'      => ['type' => 'TEXT', 'null' => true],
            'parent_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_akun');
        $this->forge->createTable('fin_accounts');

        // ─── 2. fin_rekening (Rekening & Kas) ──────────────────────────────
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_rekening'   => ['type' => 'VARCHAR', 'constraint' => 150],
            'nomor_rekening'  => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
            'bank'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tipe'            => ['type' => 'ENUM', 'constraint' => ['kas','bank'], 'default' => 'kas'],
            'akun_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'saldo_awal'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'is_active'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('fin_rekening');

        // ─── 3. fin_transaksi ───────────────────────────────────────────────
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nomor_transaksi'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'tanggal'          => ['type' => 'DATE'],
            'tipe'             => ['type' => 'ENUM', 'constraint' => ['pemasukan','pengeluaran'], 'default' => 'pemasukan'],
            'kategori'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'deskripsi'        => ['type' => 'TEXT', 'null' => true],
            'jumlah'           => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'rekening_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'akun_debit_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'akun_kredit_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'bukti_file'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_by'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['draft','posted'], 'default' => 'posted'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nomor_transaksi');
        $this->forge->addKey('tanggal');
        $this->forge->createTable('fin_transaksi');

        // ─── 4. fin_jurnal (Jurnal header) ──────────────────────────────────
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nomor_jurnal'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'tanggal'       => ['type' => 'DATE'],
            'deskripsi'     => ['type' => 'TEXT', 'null' => true],
            'transaksi_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('fin_jurnal');

        // ─── 5. fin_jurnal_detail ────────────────────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'jurnal_id'  => ['type' => 'INT', 'unsigned' => true],
            'akun_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'deskripsi'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'debit'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'kredit'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jurnal_id', 'fin_jurnal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('fin_jurnal_detail');

        // ─── Seed: Chart of Accounts default ────────────────────────────────
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $accounts = [
            // KAS & BANK
            ['kode_akun'=>'1-001','nama_akun'=>'Kas Tunai',         'tipe_akun'=>'kas',        'normal_balance'=>'debit',  'deskripsi'=>'Uang tunai di tangan'],
            ['kode_akun'=>'1-002','nama_akun'=>'Bank BRI',          'tipe_akun'=>'bank',       'normal_balance'=>'debit',  'deskripsi'=>'Rekening Bank BRI'],
            ['kode_akun'=>'1-003','nama_akun'=>'Bank BNI',          'tipe_akun'=>'bank',       'normal_balance'=>'debit',  'deskripsi'=>'Rekening Bank BNI'],
            // PENDAPATAN
            ['kode_akun'=>'4-001','nama_akun'=>'Pendapatan Iuran',  'tipe_akun'=>'pendapatan', 'normal_balance'=>'kredit', 'deskripsi'=>'Pemasukan dari iuran anggota'],
            ['kode_akun'=>'4-002','nama_akun'=>'Pendapatan Donasi', 'tipe_akun'=>'pendapatan', 'normal_balance'=>'kredit', 'deskripsi'=>'Pemasukan dari donasi'],
            ['kode_akun'=>'4-003','nama_akun'=>'Pendapatan Usaha',  'tipe_akun'=>'pendapatan', 'normal_balance'=>'kredit', 'deskripsi'=>'Pemasukan dari kegiatan usaha'],
            ['kode_akun'=>'4-004','nama_akun'=>'Pendapatan Lainnya','tipe_akun'=>'pendapatan', 'normal_balance'=>'kredit', 'deskripsi'=>'Pemasukan lain-lain'],
            // BEBAN
            ['kode_akun'=>'5-001','nama_akun'=>'Beban Operasional', 'tipe_akun'=>'beban',      'normal_balance'=>'debit',  'deskripsi'=>'Pengeluaran operasional rutin'],
            ['kode_akun'=>'5-002','nama_akun'=>'Beban Kegiatan',    'tipe_akun'=>'beban',      'normal_balance'=>'debit',  'deskripsi'=>'Pengeluaran kegiatan/program'],
            ['kode_akun'=>'5-003','nama_akun'=>'Beban ATK',         'tipe_akun'=>'beban',      'normal_balance'=>'debit',  'deskripsi'=>'Alat tulis kantor & perlengkapan'],
            ['kode_akun'=>'5-004','nama_akun'=>'Beban Lainnya',     'tipe_akun'=>'beban',      'normal_balance'=>'debit',  'deskripsi'=>'Pengeluaran lain-lain'],
            // MODAL
            ['kode_akun'=>'3-001','nama_akun'=>'Modal Awal',        'tipe_akun'=>'modal',      'normal_balance'=>'kredit', 'deskripsi'=>'Modal awal organisasi'],
        ];

        foreach ($accounts as &$a) {
            $a['parent_id']  = null;
            $a['is_active']  = 1;
            $a['created_at'] = $now;
            $a['updated_at'] = $now;
        }
        $db->table('fin_accounts')->insertBatch($accounts);

        // Seed: Rekening default — Kas Tunai menggunakan akun 1-001
        $akunKas = $db->table('fin_accounts')->where('kode_akun', '1-001')->get()->getRowArray();
        $db->table('fin_rekening')->insert([
            'nama_rekening'  => 'Kas Tunai',
            'nomor_rekening' => null,
            'bank'           => null,
            'tipe'           => 'kas',
            'akun_id'        => $akunKas ? $akunKas['id'] : null,
            'saldo_awal'     => 0,
            'is_active'      => 1,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        // ─── Seed: Menu Keuangan di sidebar ─────────────────────────────────
        $lastParent = $db->table('settings_menus')->orderBy('sort_order', 'DESC')->limit(1)->get()->getRowArray();
        $nextSort   = $lastParent ? $lastParent['sort_order'] + 10 : 100;

        $db->table('settings_menus')->insert([
            'parent_id'  => null,
            'title'      => 'Keuangan',
            'icon'       => 'fa-solid fa-coins',
            'url'        => null,
            'sort_order' => $nextSort,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $parentId = $db->insertID();

        $subMenus = [
            ['title' => 'Dashboard',        'icon' => 'fa-solid fa-chart-pie',     'url' => 'admin/keuangan',          'sort' => 1],
            ['title' => 'Chart of Accounts','icon' => 'fa-solid fa-sitemap',        'url' => 'admin/keuangan/accounts', 'sort' => 2],
            ['title' => 'Transaksi',        'icon' => 'fa-solid fa-right-left',     'url' => 'admin/keuangan/transaksi','sort' => 3],
            ['title' => 'Jurnal Umum',      'icon' => 'fa-solid fa-book-open',      'url' => 'admin/keuangan/jurnal',   'sort' => 4],
            ['title' => 'Rekening & Kas',   'icon' => 'fa-solid fa-building-columns','url' => 'admin/keuangan/rekening','sort' => 5],
            ['title' => 'Laporan Laba Rugi','icon' => 'fa-solid fa-file-invoice',   'url' => 'admin/keuangan/laporan',  'sort' => 6],
        ];

        foreach ($subMenus as $sm) {
            $db->table('settings_menus')->insert([
                'parent_id'  => $parentId,
                'title'      => $sm['title'],
                'icon'       => $sm['icon'],
                'url'        => $sm['url'],
                'sort_order' => $sm['sort'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $childId = $db->insertID();

            // Assign ke role admin & bph (yang punya akses keuangan)
            foreach (['admin', 'bph'] as $group) {
                $db->table('settings_menu_roles')->insert([
                    'menu_id'    => $childId,
                    'group_name' => $group,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Hapus menu keuangan
        $parent = $db->table('settings_menus')->where('title', 'Keuangan')->where('parent_id IS NULL', null, false)->get()->getRowArray();
        if ($parent) {
            $children = $db->table('settings_menus')->where('parent_id', $parent['id'])->get()->getResultArray();
            foreach ($children as $c) {
                $db->table('settings_menu_roles')->where('menu_id', $c['id'])->delete();
                $db->table('settings_menus')->where('id', $c['id'])->delete();
            }
            $db->table('settings_menus')->where('id', $parent['id'])->delete();
        }

        $this->forge->dropTable('fin_jurnal_detail', true);
        $this->forge->dropTable('fin_jurnal', true);
        $this->forge->dropTable('fin_transaksi', true);
        $this->forge->dropTable('fin_rekening', true);
        $this->forge->dropTable('fin_accounts', true);
    }
}
