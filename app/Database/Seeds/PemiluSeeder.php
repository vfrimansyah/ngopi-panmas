<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PemiluSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Data Partai Politik Nasional Peserta Pemilu 2024
        $partaiData = [
            [
                'nama_partai'    => 'Partai Kebangkitan Bangsa (PKB)',
                'lambang_partai' => 'pkb.png',
                'tahun_berdiri'  => 1998,
                'azas_ideologi'  => 'Islam Moderat, Nasionalisme',
                'alamat'         => 'Jl. Raden Saleh Raya No.9, Senen, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Gerakan Indonesia Raya (Gerindra)',
                'lambang_partai' => 'gerindra.png',
                'tahun_berdiri'  => 2008,
                'azas_ideologi'  => 'Nasionalisme, Populisme',
                'alamat'         => 'Jl. Harsono RM No.54, Ragunan, Pasar Minggu, Jakarta Selatan',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Demokrasi Indonesia Perjuangan (PDI Perjuangan)',
                'lambang_partai' => 'pdip.png',
                'tahun_berdiri'  => 1999,
                'azas_ideologi'  => 'Nasionalisme (Marhaenisme), Pancasila',
                'alamat'         => 'Jl. Pangeran Diponegoro No.58, Menteng, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Golongan Karya (Golkar)',
                'lambang_partai' => 'golkar.png',
                'tahun_berdiri'  => 1964,
                'azas_ideologi'  => 'Nasionalisme, Konservatisme',
                'alamat'         => 'Jl. Anggrek Neli Murni No.11A, Palmerah, Jakarta Barat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai NasDem',
                'lambang_partai' => 'nasdem.png',
                'tahun_berdiri'  => 2011,
                'azas_ideologi'  => 'Nasionalisme, Sekularisme',
                'alamat'         => 'Jl. RP. Soeroso No.44-46, Gondangdia, Menteng, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Buruh',
                'lambang_partai' => 'buruh.png',
                'tahun_berdiri'  => 2021,
                'azas_ideologi'  => 'Sosialisme Demokratik, Pro-Buruh',
                'alamat'         => 'Jl. Raya Pondok Gede No. 84, Cipayung, Jakarta Timur',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Gelombang Rakyat Indonesia (Gelora)',
                'lambang_partai' => 'gelora.png',
                'tahun_berdiri'  => 2019,
                'azas_ideologi'  => 'Islam-Nasionalis, Pancasila',
                'alamat'         => 'Jl. HOS Cokroaminoto No. 19, Menteng, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Keadilan Sejahtera (PKS)',
                'lambang_partai' => 'pks.png',
                'tahun_berdiri'  => 1998,
                'azas_ideologi'  => 'Islamisme, Konservatisme',
                'alamat'         => 'Jl. TB Simatupang No.82, Pasar Minggu, Jakarta Selatan',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Kebangkitan Nusantara (PKN)',
                'lambang_partai' => 'pkn.png',
                'tahun_berdiri'  => 2021,
                'azas_ideologi'  => 'Nasionalisme, Gotong Royong',
                'alamat'         => 'Jl. Ki Mangunsarkoro No. 16, Menteng, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Hati Nurani Rakyat (Hanura)',
                'lambang_partai' => 'hanura.png',
                'tahun_berdiri'  => 2006,
                'azas_ideologi'  => 'Nasionalisme, Pancasila',
                'alamat'         => 'Jl. City Tower Lantai 18, Menteng, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Amanat Nasional (PAN)',
                'lambang_partai' => 'pan.png',
                'tahun_berdiri'  => 1998,
                'azas_ideologi'  => 'Nasionalisme, Religius (Modernis)',
                'alamat'         => 'Jl. Amil Buncit Raya No. 1A, Pancoran, Jakarta Selatan',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Demokrat',
                'lambang_partai' => 'demokrat.png',
                'tahun_berdiri'  => 2001,
                'azas_ideologi'  => 'Nasionalisme, Sentris',
                'alamat'         => 'Jl. Proklamasi No.41, Menteng, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Solidaritas Indonesia (PSI)',
                'lambang_partai' => 'psi.png',
                'tahun_berdiri'  => 2014,
                'azas_ideologi'  => 'Nasionalisme, Progresivisme, Sekularisme',
                'alamat'         => 'Jl. Wahid Hasyim No. 194, Tanah Abang, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Persatuan Pembangunan (PPP)',
                'lambang_partai' => 'ppp.png',
                'tahun_berdiri'  => 1973,
                'azas_ideologi'  => 'Islam Moderat, Pancasila',
                'alamat'         => 'Jl. Diponegoro No.60, Menteng, Jakarta Pusat',
                'cakupan'        => 'Nasional',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nama_partai'    => 'Partai Aceh (PA)',
                'lambang_partai' => 'partai_aceh.png',
                'tahun_berdiri'  => 2007,
                'azas_ideologi'  => 'Lokal, Otonomi Khusus Aceh',
                'alamat'         => 'Jl. Mr. Dr. Mohd Hasan No. 9, Lueng Bata, Banda Aceh',
                'cakupan'        => 'Lokal',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($partaiData as $partai) {
            $db->table('pemilu_partai')->insert($partai);
        }

        // 2. Data Peserta Pemilu 2024 (Mapping No Urut dan Ketua Umum)
        $pesertaData = [
            [
                'nama_partai'  => 'Partai Kebangkitan Bangsa (PKB)',
                'no_urut'      => 1,
                'ketua_partai' => 'Muhaimin Iskandar'
            ],
            [
                'nama_partai'  => 'Partai Gerakan Indonesia Raya (Gerindra)',
                'no_urut'      => 2,
                'ketua_partai' => 'Prabowo Subianto'
            ],
            [
                'nama_partai'  => 'Partai Demokrasi Indonesia Perjuangan (PDI Perjuangan)',
                'no_urut'      => 3,
                'ketua_partai' => 'Megawati Soekarnoputri'
            ],
            [
                'nama_partai'  => 'Partai Golongan Karya (Golkar)',
                'no_urut'      => 4,
                'ketua_partai' => 'Bahlil Lahadalia'
            ],
            [
                'nama_partai'  => 'Partai NasDem',
                'no_urut'      => 5,
                'ketua_partai' => 'Surya Paloh'
            ],
            [
                'nama_partai'  => 'Partai Buruh',
                'no_urut'      => 6,
                'ketua_partai' => 'Said Iqbal'
            ],
            [
                'nama_partai'  => 'Partai Gelombang Rakyat Indonesia (Gelora)',
                'no_urut'      => 7,
                'ketua_partai' => 'Anis Matta'
            ],
            [
                'nama_partai'  => 'Partai Keadilan Sejahtera (PKS)',
                'no_urut'      => 8,
                'ketua_partai' => 'Ahmad Syaikhu'
            ],
            [
                'nama_partai'  => 'Partai Kebangkitan Nusantara (PKN)',
                'no_urut'      => 9,
                'ketua_partai' => 'Anas Urbaningrum'
            ],
            [
                'nama_partai'  => 'Partai Hati Nurani Rakyat (Hanura)',
                'no_urut'      => 10,
                'ketua_partai' => 'Oesman Sapta Odang'
            ],
            [
                'nama_partai'  => 'Partai Amanat Nasional (PAN)',
                'no_urut'      => 12,
                'ketua_partai' => 'Zulkifli Hasan'
            ],
            [
                'nama_partai'  => 'Partai Demokrat',
                'no_urut'      => 14,
                'ketua_partai' => 'Agus Harimurti Yudhoyono'
            ],
            [
                'nama_partai'  => 'Partai Solidaritas Indonesia (PSI)',
                'no_urut'      => 15,
                'ketua_partai' => 'Kaesang Pangarep'
            ],
            [
                'nama_partai'  => 'Partai Persatuan Pembangunan (PPP)',
                'no_urut'      => 17,
                'ketua_partai' => 'Muhamad Mardiono'
            ],
            [
                'nama_partai'  => 'Partai Aceh (PA)',
                'no_urut'      => 21,
                'ketua_partai' => 'Muzakir Manaf'
            ]
        ];

        foreach ($pesertaData as $peserta) {
            $partai = $db->table('pemilu_partai')
                ->where('nama_partai', $peserta['nama_partai'])
                ->get()
                ->getRowArray();

            if ($partai) {
                $db->table('pemilu_peserta')->insert([
                    'tahun'        => 2024,
                    'partai_id'    => $partai['id'],
                    'no_urut'      => $peserta['no_urut'],
                    'ketua_partai' => $peserta['ketua_partai'],
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
