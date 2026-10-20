<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\ArticleCategoryModel;

class NewsController extends BaseController
{
    protected $newsModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->newsModel = new ArticleModel();
        $this->categoryModel = new ArticleCategoryModel();
    }

    public function index()
    {
        $categorySlug = $this->request->getGet('kategori');

        $builder = $this->newsModel->where('status', 'published');

        if ($categorySlug) {
            $category = $this->categoryModel->where('nama_kategori', $categorySlug)->first();
            if ($category) {
                $builder->where('kategori_id', $category['id']);
            }
        }

        $data = [
            'title' => 'Berita — Ngobrol Pikiran Indonesia',
            'news' => $builder->orderBy('published_at', 'DESC')->paginate(9),
            'pager' => $this->newsModel->pager,
            'categories' => $this->categoryModel->findAll(),
            'selectedCategory' => $categorySlug,
        ];

        return view('pages/news/index', $data);
    }

    public function show($slug = null)
    {
        $news = $this->newsModel->where('slug', $slug)
                                ->where('status', 'published')
                                ->first();

        if (!$news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $news['judul'] . ' — Ngobrol Pikiran Indonesia',
            'news' => $news,
            'recentNews' => $this->newsModel->where('status', 'published')
                                             ->where('id !=', $news['id'])
                                             ->orderBy('published_at', 'DESC')
                                             ->limit(5)
                                             ->find(),
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('pages/news/detail', $data);
    }
}
