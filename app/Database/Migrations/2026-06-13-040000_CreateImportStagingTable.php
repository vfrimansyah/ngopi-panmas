<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateImportStagingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'import_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processed', 'failed'],
                'default'    => 'pending',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_member_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'col_a' => [
                'type' => 'TEXT',
                'null' => true,
                // Timestamp
            ],
            'col_b' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama lengkap
            ],
            'col_c' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama pasangan (suami/istri)
            ],
            'col_d' => [
                'type' => 'TEXT',
                'null' => true,
                // Usia
            ],
            'col_e' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Anggota
            ],
            'col_f' => [
                'type' => 'TEXT',
                'null' => true,
                // Kode UPA
            ],
            'col_g' => [
                'type' => 'TEXT',
                'null' => true,
                // Status perkawinan
            ],
            'col_h' => [
                'type' => 'TEXT',
                'null' => true,
                // Alamat lengkap
            ],
            'col_i' => [
                'type' => 'TEXT',
                'null' => true,
                // RT
            ],
            'col_j' => [
                'type' => 'TEXT',
                'null' => true,
                // RW
            ],
            'col_k' => [
                'type' => 'TEXT',
                'null' => true,
                // Kelurahan
            ],
            'col_l' => [
                'type' => 'TEXT',
                'null' => true,
                // Kecamatan
            ],
            'col_m' => [
                'type' => 'TEXT',
                'null' => true,
                // Kota/Kabupaten
            ],
            'col_n' => [
                'type' => 'TEXT',
                'null' => true,
                // Pekerjaan Anda
            ],
            'col_o' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Instansi/Tempat Usaha
            ],
            'col_p' => [
                'type' => 'TEXT',
                'null' => true,
                // Alamat tempat kerja/usaha
            ],
            'col_q' => [
                'type' => 'TEXT',
                'null' => true,
                // Bidang pekerjaan
            ],
            'col_r' => [
                'type' => 'TEXT',
                'null' => true,
                // Pekerjaan pasangan
            ],
            'col_s' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki
            ],
            'col_t' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendapatan per bulan (pilih range)
            ],
            'col_u' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendapatan pasangan per bulan (pilih range)
            ],
            'col_v' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan terakhir
            ],
            'col_w' => [
                'type' => 'TEXT',
                'null' => true,
                // Sudah memiliki anak
            ],
            'col_x' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 1
            ],
            'col_y' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin Anak 1
            ],
            'col_z' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 1
            ],
            'col_aa' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 1
            ],
            'col_ab' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak 1 saat ini
            ],
            'col_ac' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 1
            ],
            'col_ad' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-2
            ],
            'col_ae' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 2
            ],
            'col_af' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 2
            ],
            'col_ag' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 2
            ],
            'col_ah' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 2
            ],
            'col_ai' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak 2 saat ini
            ],
            'col_aj' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 2
            ],
            'col_ak' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-3
            ],
            'col_al' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 3
            ],
            'col_am' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 3
            ],
            'col_an' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 3
            ],
            'col_ao' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 3
            ],
            'col_ap' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak 3 saat ini
            ],
            'col_aq' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 3
            ],
            'col_ar' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-4
            ],
            'col_as' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 4
            ],
            'col_at' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 4
            ],
            'col_au' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 4
            ],
            'col_av' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 4
            ],
            'col_aw' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak 4 saat ini
            ],
            'col_ax' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 4
            ],
            'col_ay' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-5
            ],
            'col_az' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 5
            ],
            'col_ba' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 5
            ],
            'col_bb' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 5
            ],
            'col_bc' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 5
            ],
            'col_bd' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak 5 saat ini
            ],
            'col_be' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 5
            ],
            'col_bf' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-6
            ],
            'col_bg' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 6
            ],
            'col_bh' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 6
            ],
            'col_bi' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 6
            ],
            'col_bj' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 6
            ],
            'col_bk' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak saat ini Anak 6
            ],
            'col_bl' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 6
            ],
            'col_bm' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-7
            ],
            'col_bn' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 7
            ],
            'col_bo' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 7
            ],
            'col_bp' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 7
            ],
            'col_bq' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 7
            ],
            'col_br' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak saat ini Anak 7
            ],
            'col_bs' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 7
            ],
            'col_bt' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-8
            ],
            'col_bu' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 8
            ],
            'col_bv' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 8
            ],
            'col_bw' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 8
            ],
            'col_bx' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 8
            ],
            'col_by' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak saat ini Anak 8
            ],
            'col_bz' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 8
            ],
            'col_ca' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-9
            ],
            'col_cb' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 9
            ],
            'col_cc' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin  Anak 9
            ],
            'col_cd' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir  Anak 9
            ],
            'col_ce' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan  Anak 9
            ],
            'col_cf' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak saat ini Anak 9
            ],
            'col_cg' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring Anak 9
            ],
            'col_ch' => [
                'type' => 'TEXT',
                'null' => true,
                // Input data anak ke-10
            ],
            'col_ci' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Lengkap Anak 10
            ],
            'col_cj' => [
                'type' => 'TEXT',
                'null' => true,
                // Jenis Kelamin
            ],
            'col_ck' => [
                'type' => 'TEXT',
                'null' => true,
                // Tanggal Lahir
            ],
            'col_cl' => [
                'type' => 'TEXT',
                'null' => true,
                // Status Pernikahan
            ],
            'col_cm' => [
                'type' => 'TEXT',
                'null' => true,
                // Pendidikan anak saat ini
            ],
            'col_cn' => [
                'type' => 'TEXT',
                'null' => true,
                // Mentoring
            ],
            'col_co' => [
                'type' => 'TEXT',
                'null' => true,
                // Apakah saat ini Anda aktif dalam organisasi masyarakat atau keagamaan
            ],
            'col_cp' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama organisasi (format : Organisasi 1 : ketua, organisasi 2 : anggota, dst)
            ],
            'col_cq' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama organisasi  (format : Organisasi 1 : ketua, organisasi 2 : anggota, dst)
            ],
            'col_cr' => [
                'type' => 'TEXT',
                'null' => true,
                // Apakah Anda sudah terdaftar sebagai pemilih tetap
            ],
            'col_cs' => [
                'type' => 'TEXT',
                'null' => true,
                // Seberapa sering Anda mengikuti berita politik
            ],
            'col_ct' => [
                'type' => 'TEXT',
                'null' => true,
                // Apa sumber utama informasi politik Anda
            ],
            'col_cu' => [
                'type' => 'TEXT',
                'null' => true,
                // Apa program utama yang menurut Anda harus dilakukan oleh struktur (pilih 3 program)
            ],
            'col_cv' => [
                'type' => 'TEXT',
                'null' => true,
                // Harapan Anda kepada struktur
            ],
            'col_cw' => [
                'type' => 'TEXT',
                'null' => true,
                // Apakah Anda bersedia menyukseskan program struktur
            ],
            'col_cx' => [
                'type' => 'TEXT',
                'null' => true,
                // Dalam hal apa Anda ingin berkontribusi
            ],
            'col_cy' => [
                'type' => 'TEXT',
                'null' => true,
                // Apakah Anda memiliki jaringan atau komunitas yang bisa diajak untuk mendukung program struktur
            ],
            'col_cz' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan Jaringan atau komunitas (tuliskan sebanyak-banyaknya pisahkan dengan koma)
            ],
            'col_da' => [
                'type' => 'TEXT',
                'null' => true,
                // Apakah Anda bersedia memberikan donasi untuk mendukung program struktur ini
            ],
            'col_db' => [
                'type' => 'TEXT',
                'null' => true,
                // Selain Iuran rutin, jika bersedia, dalam bentuk apa Anda ingin berdonasi lainnya
            ],
            'col_dc' => [
                'type' => 'TEXT',
                'null' => true,
                // Apakah anda selalu membayar kewajiban iuran rutin yang ditetapkan struktur
            ],
            'col_dd' => [
                'type' => 'TEXT',
                'null' => true,
                // Metode pembayaran apa yang Anda preferensikan untuk berdonasi
            ],
            'col_de' => [
                'type' => 'TEXT',
                'null' => true,
                // Apakah Anda ingin donasi Anda bersifat anonim
            ],
            'col_df' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Sekolah/Universitas  Anak 1
            ],
            'col_dg' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 1
            ],
            'col_dh' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 1  sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_di' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul Skripsi ( bila sedang mengerjakan skripsi)  Anak 1
            ],
            'col_dj' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Sekolah/Universitas  Anak 2
            ],
            'col_dk' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul Skripsi ( bila sedang mengerjakan skripsi)  Anak 2
            ],
            'col_dl' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 2
            ],
            'col_dm' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama Sekolah/Universitas  Anak 3
            ],
            'col_dn' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 3
            ],
            'col_do' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul Skripsi ( bila sedang mengerjakan skripsi)  Anak 3
            ],
            'col_dp' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama sekolah/Universitas  Anak 4
            ],
            'col_dq' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul skripsi (bila sedang mengerjakan skripsi)  Anak 4
            ],
            'col_dr' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 4
            ],
            'col_ds' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama sekolah/Universitas  Anak 5
            ],
            'col_dt' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 5
            ],
            'col_du' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul Skripsi (bila sedang mengerjakan skripsi)  Anak 5
            ],
            'col_dv' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama sekolah/Universitas  Anak 6
            ],
            'col_dw' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 6
            ],
            'col_dx' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul skripsi bila sedang mengerjakan skripsi)  Anak 6
            ],
            'col_dy' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama sekolah/Universitas  Anak 7
            ],
            'col_dz' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 7
            ],
            'col_ea' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul skripsi/Universitas  Anak 7
            ],
            'col_eb' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama sekolah/Universitas  Anak 8
            ],
            'col_ec' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 8
            ],
            'col_ed' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul skripsi (bila sedang mengerjakan skripsi)  Anak 8
            ],
            'col_ee' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama sekolah/Universitas  Anak 9
            ],
            'col_ef' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan  Anak 9
            ],
            'col_eg' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul skripsi (bila sedang mengerjakan skripsi)  Anak 9
            ],
            'col_eh' => [
                'type' => 'TEXT',
                'null' => true,
                // Nama sekolah/Universitas
            ],
            'col_ei' => [
                'type' => 'TEXT',
                'null' => true,
                // Jurusan
            ],
            'col_ej' => [
                'type' => 'TEXT',
                'null' => true,
                // Judul skripsi (bila sedang mengerjakan skripsi)
            ],
            'col_ek' => [
                'type' => 'TEXT',
                'null' => true,
                // Apa kendala anda dalam membayar iuran rutin
            ],
            'col_el' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 1
            ],
            'col_em' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 1
            ],
            'col_en' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 1
            ],
            'col_eo' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 2   sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_ep' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 2
            ],
            'col_eq' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 2
            ],
            'col_er' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 2
            ],
            'col_es' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila   Anak 3   sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_et' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 3
            ],
            'col_eu' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 3
            ],
            'col_ev' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 3
            ],
            'col_ew' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 4   sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_ex' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 4
            ],
            'col_ey' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 4
            ],
            'col_ez' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 4
            ],
            'col_fa' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 5  sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_fb' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 5
            ],
            'col_fc' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 5
            ],
            'col_fd' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 5
            ],
            'col_fe' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 6   sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_ff' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 6
            ],
            'col_fg' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 6
            ],
            'col_fh' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian  Anak 6   yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)
            ],
            'col_fi' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 7   sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_fj' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 7
            ],
            'col_fk' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi  Anak 7
            ],
            'col_fl' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 7
            ],
            'col_fm' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 8   sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_fn' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 8
            ],
            'col_fo' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 8
            ],
            'col_fp' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 8
            ],
            'col_fq' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila  Anak 9   sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_fr' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)  Anak 9
            ],
            'col_fs' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi   Anak 9
            ],
            'col_ft' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)  Anak 9
            ],
            'col_fu' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila sudah tidak sekolah/kuliah, apakah sudah bekerja ?
            ],
            'col_fv' => [
                'type' => 'TEXT',
                'null' => true,
                // Sebutkan pekerjaan (bila sudah bekerja)
            ],
            'col_fw' => [
                'type' => 'TEXT',
                'null' => true,
                // Hobi
            ],
            'col_fx' => [
                'type' => 'TEXT',
                'null' => true,
                // Keahlian yang dimiliki ? (pisahkan dengan koma bila lebih dari satu)
            ],
            'col_fy' => [
                'type' => 'TEXT',
                'null' => true,
                // Column 168
            ],
            'col_fz' => [
                'type' => 'TEXT',
                'null' => true,
                // Bila sudah tidak sekolah/kuliah, apakah sudah bekerja ? [Baris 2]
            ],
            'col_ga' => [
                'type' => 'TEXT',
                'null' => true,
                // Column 109
            ],
            'col_gb' => [
                'type' => 'TEXT',
                'null' => true,
                // 
            ],
            'col_gc' => [
                'type' => 'TEXT',
                'null' => true,
                // 
            ],
            'col_gd' => [
                'type' => 'TEXT',
                'null' => true,
                // 
            ],
            'col_ge' => [
                'type' => 'TEXT',
                'null' => true,
                // 
            ],
            'col_gf' => [
                'type' => 'TEXT',
                'null' => true,
                // 
            ],
            'col_gg' => [
                'type' => 'TEXT',
                'null' => true,
                // 
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
        $this->forge->createTable('import_staging');
    }

    public function down()
    {
        $this->forge->dropTable('import_staging');
    }
}
