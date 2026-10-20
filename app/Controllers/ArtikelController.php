<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\ArticleCategoryModel;

class ArtikelController extends BaseController
{
    protected $articleModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->articleModel  = new ArticleModel();
        $this->categoryModel = new ArticleCategoryModel();
    }

    public function index()
    {
        $categorySlug = $this->request->getGet('kategori');
        $search       = $this->request->getGet('q');

        $builder = $this->articleModel->where('status', 'published')
                                      ->where('tipe', 'artikel');

        if ($categorySlug) {
            $category = $this->categoryModel->where('nama_kategori', $categorySlug)->first();
            if ($category) {
                $builder->where('kategori_id', $category['id']);
            }
        }

        if ($search) {
            $builder->like('judul', $search);
        }

        $data = [
            'title'            => 'Artikel — Ngobrol Pikiran Indonesia',
            'metaDesc'         => 'Kumpulan artikel dan gagasan dari kontributor dan pengurus Ngobrol Pikiran Indonesia.',
            'artikels'         => $builder->orderBy('published_at', 'DESC')->paginate(9),
            'pager'            => $this->articleModel->pager,
            'categories'       => $this->categoryModel->findAll(),
            'selectedCategory' => $categorySlug,
        ];

        return view('pages/artikel/index', $data);
    }

    public function show($slug = null)
    {
        $artikel = $this->articleModel->where('slug', $slug)
                                       ->where('status', 'published')
                                       ->first();

        if (!$artikel) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment views
        $this->articleModel->update($artikel['id'], ['views_count' => ($artikel['views_count'] ?? 0) + 1]);

        $data = [
            'title'          => $artikel['judul'] . ' — Ngobrol Pikiran Indonesia',
            'artikel'        => $artikel,
            'recentArtikels' => $this->articleModel->where('status', 'published')
                                                    ->where('tipe', 'artikel')
                                                    ->where('id !=', $artikel['id'])
                                                    ->orderBy('published_at', 'DESC')
                                                    ->limit(5)
                                                    ->find(),
            'categories'     => $this->categoryModel->findAll(),
        ];

        return view('pages/artikel/detail', $data);
    }
}
