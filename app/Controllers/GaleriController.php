<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GalleryItemModel;
use App\Models\GalleryAlbumModel;

class GaleriController extends BaseController
{
    protected GalleryItemModel $itemModel;
    protected GalleryAlbumModel $albumModel;

    public function __construct()
    {
        $this->itemModel  = new GalleryItemModel();
        $this->albumModel = new GalleryAlbumModel();
    }

    public function index()
    {
        $tipe    = $this->request->getGet('tipe'); // 'image' or 'video' or null
        $albumId = $this->request->getGet('album');
        $search  = $this->request->getGet('q');

        $filters = [
            'tipe'     => $tipe,
            'album_id' => $albumId,
            'search'   => $search,
        ];

        $items  = $this->itemModel->getList($filters);
        $albums = $this->albumModel->getAlbumsWithStats();
        $stats  = $this->itemModel->getStats();

        // Selected Album detail if filtered
        $activeAlbum = null;
        if (!empty($albumId)) {
            $activeAlbum = $this->albumModel->find($albumId);
        }

        $data = [
            'title'       => 'Galeri & Dokumentasi Kegiatan - PKS Pancoran Mas',
            'items'       => $items,
            'albums'      => $albums,
            'stats'       => $stats,
            'filters'     => $filters,
            'activeAlbum' => $activeAlbum,
        ];

        return view('galeri/index', $data);
    }
}
