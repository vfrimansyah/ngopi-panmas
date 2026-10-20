<?php

namespace App\Controllers;

use App\Models\MemberProductModel;
use App\Models\ArticleModel;
use App\Models\EventModel;

class Home extends BaseController
{
    public function index(): string
    {
        $productModel = new MemberProductModel();
        $newsModel    = new ArticleModel();
        $eventModel   = new EventModel();
        $memberModel  = new \App\Models\MemberModel();
        $settingModel = new \App\Models\SettingModel();
        $db           = \Config\Database::connect();

        // Stats counters
        $stats = [
            'members'  => $db->table('members')->where('is_active', 1)->countAllResults(),
            'news'     => $db->table('articles')->where('status', 'published')->countAllResults(),
            'events'   => $db->table('events')->countAllResults(),
            'products' => $db->table('member_products')->where('status', 'published')->countAllResults(),
        ];

        // Featured news (most recent 6)
        $latestNews = $newsModel->where('status', 'published')
                                ->orderBy('published_at', 'DESC')
                                ->limit(6)
                                ->find();

        // Upcoming events (next 3)
        $upcomingEvents = $eventModel->where('tanggal_mulai >=', date('Y-m-d'))
                                     ->orderBy('tanggal_mulai', 'ASC')
                                     ->limit(3)
                                     ->find();

        // Featured products (4)
        $featuredProducts = $productModel->where('status', 'published')
                                          ->orderBy('created_at', 'DESC')
                                          ->limit(4)
                                          ->find();

        $data = [
            'title'           => 'Beranda — Ngobrol Pikiran Indonesia',
            'metaDesc'        => 'Ngobrol Pikiran Indonesia (NGOPI) — Dari Pikiran Menjadi Gerakan. Portal informasi anggota, berita terkini, agenda event, dan katalog produk.',
            'stats'           => $stats,
            'latestNews'      => $latestNews,
            'upcomingEvents'  => $upcomingEvents,
            'featuredProducts'=> $featuredProducts,
            // legacy keys for backward compat
            'products'        => $featuredProducts,
            'news'            => array_slice($latestNews, 0, 3),
            'events'          => $upcomingEvents,
        ];

        return view('pages/home', $data);
    }
}
