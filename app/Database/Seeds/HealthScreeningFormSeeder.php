<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HealthScreeningFormSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Check if form already exists
        $existingForm = $db->table('custom_forms')->where('slug', 'skrining-kesehatan')->get()->getRowArray();
        if ($existingForm) {
            $formId = $existingForm['id'];
        } else {
            $db->table('custom_forms')->insert([
                'title'            => 'Skrining Kesehatan',
                'slug'             => 'skrining-kesehatan',
                'description'      => 'Kuesioner kesiapan aktivitas fisik dan skrining kesehatan berkala anggota dan masyarakat (Physical Activity Readiness Questionnaire / PAR-Q). Formulir ini membantu memastikan keselamatan Anda sebelum mengikuti kegiatan atau program fisik.',
                'is_public'        => 1,
                'is_active'        => 1,
                'allow_multiple'   => 1,
                'auto_link_member' => 1,
                'success_message'  => 'Terima kasih atas partisipasi Anda! Data skrining kesehatan Anda telah berhasil disimpan dan dicocokkan dengan database kesehatan komunitas.',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $formId = $db->insertID();
        }

        // Delete existing fields to re-seed cleanly
        $db->table('custom_form_fields')->where('form_id', $formId)->delete();

        $fields = [
            [
                'field_label'         => 'Nama Lengkap',
                'field_name'          => 'nama',
                'field_type'          => 'text',
                'field_options'       => null,
                'help_text'           => 'Nama lengkap sesuai KTP / database anggota',
                'is_required'         => 1,
                'map_to_member_field' => 'nama_lengkap',
                'sort_order'          => 1,
            ],
            [
                'field_label'         => 'NIK',
                'field_name'          => 'nik',
                'field_type'          => 'text',
                'field_options'       => null,
                'help_text'           => 'Nomor Induk Kependudukan (16 digit)',
                'is_required'         => 0,
                'map_to_member_field' => 'nik',
                'sort_order'          => 2,
            ],
            [
                'field_label'         => 'Tanggal Lahir',
                'field_name'          => 'tanggal_lahir',
                'field_type'          => 'date',
                'field_options'       => null,
                'help_text'           => 'Pilih tanggal lahir Anda',
                'is_required'         => 1,
                'map_to_member_field' => 'tanggal_lahir',
                'sort_order'          => 3,
            ],
            [
                'field_label'         => 'Alamat Domisili',
                'field_name'          => 'alamat',
                'field_type'          => 'textarea',
                'field_options'       => null,
                'help_text'           => 'Alamat tempat tinggal saat ini (Kelurahan, RT/RW)',
                'is_required'         => 1,
                'map_to_member_field' => 'alamat_lengkap',
                'sort_order'          => 4,
            ],
            [
                'field_label'         => 'No HP / WhatsApp',
                'field_name'          => 'no_hp',
                'field_type'          => 'text',
                'field_options'       => null,
                'help_text'           => 'Nomor WhatsApp aktif untuk konfirmasi & pencocokan akun',
                'is_required'         => 1,
                'map_to_member_field' => 'handphone',
                'sort_order'          => 5,
            ],
            [
                'field_label'         => 'Kode Group UPA',
                'field_name'          => 'kode_upa',
                'field_type'          => 'text',
                'field_options'       => null,
                'help_text'           => '(Hanya Angka) contoh: 3201',
                'is_required'         => 0,
                'map_to_member_field' => 'kode_upa',
                'sort_order'          => 6,
            ],
            [
                'field_label'         => 'Lingkar Perut',
                'field_name'          => 'lingkar_perut',
                'field_type'          => 'number',
                'field_options'       => null,
                'help_text'           => 'Dalam satuan cm (contoh: 85)',
                'is_required'         => 0,
                'map_to_member_field' => null,
                'sort_order'          => 7,
            ],
            [
                'field_label'         => 'Berat Badan',
                'field_name'          => 'berat_badan',
                'field_type'          => 'number',
                'field_options'       => null,
                'help_text'           => 'Dalam satuan kg (contoh: 65)',
                'is_required'         => 0,
                'map_to_member_field' => null,
                'sort_order'          => 8,
            ],
            [
                'field_label'         => 'Tinggi Badan',
                'field_name'          => 'tinggi_badan',
                'field_type'          => 'number',
                'field_options'       => null,
                'help_text'           => 'Dalam satuan cm (contoh: 170)',
                'is_required'         => 0,
                'map_to_member_field' => null,
                'sort_order'          => 9,
            ],
            [
                'field_label'         => 'Apakah kamu pernah didiagnosa dokter memiliki masalah dengan jantungmu dan hanya boleh melakukan aktivitas fisik sesuai anjuran dokter?',
                'field_name'          => 'masalah_jantung',
                'field_type'          => 'radio',
                'field_options'       => json_encode(['Ya', 'Tidak']),
                'help_text'           => 'Pilih salah satu',
                'is_required'         => 1,
                'map_to_member_field' => null,
                'sort_order'          => 10,
            ],
            [
                'field_label'         => 'Apakah kamu merasakan nyeri disekitar dada ketika sedang melakukan aktivitas fisik?',
                'field_name'          => 'nyeri_dada_aktivitas',
                'field_type'          => 'radio',
                'field_options'       => json_encode(['Ya', 'Tidak']),
                'help_text'           => 'Pilih salah satu',
                'is_required'         => 1,
                'map_to_member_field' => null,
                'sort_order'          => 11,
            ],
            [
                'field_label'         => 'Dalam beberapa bulan kebelakang apakah kamu pernah merasakan sakit disekitar dada ketika tidak sedang melakukan aktivitas fisik?',
                'field_name'          => 'nyeri_dada_istirahat',
                'field_type'          => 'radio',
                'field_options'       => json_encode(['Ya', 'Tidak']),
                'help_text'           => 'Pilih salah satu',
                'is_required'         => 1,
                'map_to_member_field' => null,
                'sort_order'          => 12,
            ],
            [
                'field_label'         => 'Apakah kamu pernah kehilangan keseimbangan karena pusing atau kamu pernah kehilangan kesadaran / pingsan?',
                'field_name'          => 'pusing_pingsan',
                'field_type'          => 'radio',
                'field_options'       => json_encode(['Ya', 'Tidak']),
                'help_text'           => 'Pilih salah satu',
                'is_required'         => 1,
                'map_to_member_field' => null,
                'sort_order'          => 13,
            ],
            [
                'field_label'         => 'Apakah kamu memiliki masalah pada tulang atau sendi (contohnya punggung, lutut, dll) yang sewaktu2 menjadi parah ketika melakukan aktivitas fisik?',
                'field_name'          => 'masalah_tulang_sendi',
                'field_type'          => 'radio',
                'field_options'       => json_encode(['Ya', 'Tidak']),
                'help_text'           => 'Pilih salah satu',
                'is_required'         => 1,
                'map_to_member_field' => null,
                'sort_order'          => 14,
            ],
            [
                'field_label'         => 'Apakah dokter saat ini meresepkan obat untuk tekanan darah atau masalah jantungmu?',
                'field_name'          => 'obat_jantung_tensi',
                'field_type'          => 'radio',
                'field_options'       => json_encode(['Ya', 'Tidak']),
                'help_text'           => 'Pilih salah satu',
                'is_required'         => 1,
                'map_to_member_field' => null,
                'sort_order'          => 15,
            ],
            [
                'field_label'         => 'Apakah kamu mengetahui alasan lain mengapa kamu tidak boleh melakukan aktivitas fisik?',
                'field_name'          => 'alasan_lain_aktivitas',
                'field_type'          => 'radio',
                'field_options'       => json_encode(['Ya', 'Tidak']),
                'help_text'           => 'Pilih salah satu',
                'is_required'         => 1,
                'map_to_member_field' => null,
                'sort_order'          => 16,
            ],
        ];

        foreach ($fields as $f) {
            $f['form_id']    = $formId;
            $f['created_at'] = date('Y-m-d H:i:s');
            $f['updated_at'] = date('Y-m-d H:i:s');
            $db->table('custom_form_fields')->insert($f);
        }

        echo "Seeded Skrining Kesehatan Form (ID: {$formId}) with " . count($fields) . " questions successfully.\n";
    }
}
