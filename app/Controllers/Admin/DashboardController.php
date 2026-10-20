<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\MemberProductModel;
use App\Models\ArticleModel;
use App\Models\EventModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $kelurahanStats = $db->table('members')
            ->select('members.kelurahan_id, COALESCE(cms_region04_villages.name, "Tidak Diketahui") AS kelurahan_name, 
                      SUM(CASE WHEN members.status_anggota = "Dewasa" THEN 1 ELSE 0 END) AS dewasa_count,
                      SUM(CASE WHEN members.status_anggota = "Madya" THEN 1 ELSE 0 END) AS madya_count,
                      SUM(CASE WHEN members.status_anggota = "Utama" THEN 1 ELSE 0 END) AS utama_count,
                      COUNT(members.id) AS total_count')
            ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
            ->whereIn('members.status_anggota', ['Dewasa', 'Madya', 'Utama'])
            ->groupBy('members.kelurahan_id, cms_region04_villages.name')
            ->orderBy('total_count', 'DESC')
            ->get()
            ->getResultArray();

        $usiaStats = $db->table('members')
            ->select('COALESCE(NULLIF(usia_range, ""), "Tidak Diketahui") AS usia_group, COUNT(id) AS total_count')
            ->whereIn('status_anggota', ['Dewasa', 'Madya', 'Utama'])
            ->groupBy('usia_range')
            ->get()
            ->getResultArray();

        $ageOrder = [
            '18-25 tahun' => 1,
            '26-35 tahun' => 2,
            '36-45 tahun' => 3,
            '46-55 tahun' => 4,
            '55 tahun' => 5,
            '55 - 60 tahun' => 6,
            'diatas 60 tahun' => 7,
            'Tidak Diketahui' => 8,
        ];

        usort($usiaStats, function ($a, $b) use ($ageOrder) {
            $orderA = $ageOrder[$a['usia_group']] ?? 99;
            $orderB = $ageOrder[$b['usia_group']] ?? 99;
            return $orderA <=> $orderB;
        });

        $pendapatanStats = $db->table('members')
            ->select('COALESCE(NULLIF(pendapatan_range, ""), "Tidak Diketahui") AS pendapatan_group, COUNT(id) AS total_count')
            ->whereIn('status_anggota', ['Dewasa', 'Madya', 'Utama'])
            ->groupBy('pendapatan_range')
            ->get()
            ->getResultArray();

        $incomeOrder = [
            '< Rp1.000.000' => 1,
            'Rp1.000.000 - Rp3.000.000' => 2,
            'Rp3.000.000 - Rp5.000.000' => 3,
            'Rp5.000.000 - Rp10.000.000' => 4,
            '> Rp10.000.000' => 5,
            'Tidak Diketahui' => 6,
        ];

        usort($pendapatanStats, function ($a, $b) use ($incomeOrder) {
            $orderA = $incomeOrder[$a['pendapatan_group']] ?? 99;
            $orderB = $incomeOrder[$b['pendapatan_group']] ?? 99;
            return $orderA <=> $orderB;
        });

        $pendidikanStats = $db->table('members')
            ->select('COALESCE(NULLIF(pendidikan_terakhir, ""), "Tidak Diketahui") AS pendidikan_group, COUNT(id) AS total_count')
            ->whereIn('status_anggota', ['Dewasa', 'Madya', 'Utama'])
            ->groupBy('pendidikan_terakhir')
            ->get()
            ->getResultArray();

        $eduOrder = [
            'SMP' => 1,
            'SMA/SMK' => 2,
            'D3' => 3,
            'S1' => 4,
            'S2' => 5,
            'S3' => 6,
            'S2/S3' => 5,
            'Tidak Diketahui' => 7,
        ];

        usort($pendidikanStats, function ($a, $b) use ($eduOrder) {
            $orderA = $eduOrder[$a['pendidikan_group']] ?? 99;
            $orderB = $eduOrder[$b['pendidikan_group']] ?? 99;
            return $orderA <=> $orderB;
        });

        // Children Tarbiyah stats (parents are Dewasa, Madya, Utama)
        $childTarbiyahStats = $db->table('members AS m')
            ->select('COALESCE(m.mengikuti_mentoring, 0) AS is_tarbiyah, COUNT(m.id) AS total_count')
            ->join('members AS f', 'f.id = m.father_member_id', 'left')
            ->join('members AS mo', 'mo.id = m.mother_member_id', 'left')
            ->groupStart()
                ->whereIn('f.status_anggota', ['Dewasa', 'Madya', 'Utama'])
                ->orWhereIn('mo.status_anggota', ['Dewasa', 'Madya', 'Utama'])
            ->groupEnd()
            ->groupStart()
                ->where('m.status_anggota', null)
                ->orWhere('m.status_anggota', '')
            ->groupEnd()
            ->groupBy('is_tarbiyah')
            ->get()
            ->getResultArray();

        $childTarbiyah = [
            'sudah' => 0,
            'belum' => 0
        ];
        foreach ($childTarbiyahStats as $row) {
            if ($row['is_tarbiyah'] == 1) {
                $childTarbiyah['sudah'] = (int) $row['total_count'];
            } else {
                $childTarbiyah['belum'] += (int) $row['total_count'];
            }
        }

        $data = [
            'title' => 'Dashboard — Admin Ngobrol Pikiran Indonesia',
            'totalMembers' => (new MemberModel())->where('status_anggota IS NOT NULL')->countAllResults(),
            'totalProducts' => (new MemberProductModel())->where('status', 'published')->countAllResults(),
            'totalNews' => (new ArticleModel())->where('status', 'published')->countAllResults(),
            'totalEvents' => (new EventModel())->countAllResults(),
            'recentMembers' => (new MemberModel())->where('status_anggota IS NOT NULL')->orderBy('created_at', 'DESC')->limit(5)->find(),
            'recentNews' => (new ArticleModel())->where('status', 'published')->orderBy('created_at', 'DESC')->limit(5)->find(),
            'statusCounts' => [
                'Dewasa' => (new MemberModel())->where('status_anggota', 'Dewasa')->countAllResults(),
                'Madya' => (new MemberModel())->where('status_anggota', 'Madya')->countAllResults(),
                'Utama' => (new MemberModel())->where('status_anggota', 'Utama')->countAllResults(),
            ],
            'kelurahanStats' => $kelurahanStats,
            'usiaStats' => $usiaStats,
            'pendapatanStats' => $pendapatanStats,
            'pendidikanStats' => $pendidikanStats,
            'childTarbiyah' => $childTarbiyah,
        ];

        return view('admin/dashboard', $data);
    }
}
