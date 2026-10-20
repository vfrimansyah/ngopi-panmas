<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\ArticleCategoryModel;

class IdeController extends BaseController
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
                                      ->where('tipe', 'ide');

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
            'title'            => 'Ide & Gagasan — Ngobrol Pikiran Indonesia',
            'metaDesc'         => 'Koleksi ide, gagasan, dan inisiatif dari anggota dan kontributor Ngobrol Pikiran Indonesia.',
            'ides'             => $builder->orderBy('published_at', 'DESC')->paginate(9),
            'pager'            => $this->articleModel->pager,
            'categories'       => $this->categoryModel->findAll(),
            'selectedCategory' => $categorySlug,
        ];

        return view('pages/ide/index', $data);
    }

    public function show($slug = null)
    {
        $ide = $this->articleModel->where('slug', $slug)
                                   ->where('tipe', 'ide')
                                   ->where('status', 'published')
                                   ->first();

        if (!$ide) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment views
        $this->articleModel->update($ide['id'], ['views_count' => ($ide['views_count'] ?? 0) + 1]);

        $data = [
            'title'      => $ide['judul'] . ' — Ngobrol Pikiran Indonesia',
            'ide'        => $ide,
            'recentIdes' => $this->articleModel->where('status', 'published')
                                                ->where('tipe', 'ide')
                                                ->where('id !=', $ide['id'])
                                                ->orderBy('published_at', 'DESC')
                                                ->limit(5)
                                                ->find(),
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('pages/ide/detail', $data);
    }

    public function create()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk mengajukan ide baru.');
        }

        $data = [
            'title'      => 'Tulis Ide Baru — Ngobrol Pikiran Indonesia',
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('pages/ide/create', $data);
    }

    public function store()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = [
            'judul'  => 'required|min_length[5]|max_length[255]',
            'konten' => 'required|min_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $judul = $this->request->getPost('judul');
        $slug  = url_title($judul, '-', true) . '-' . time();

        $db = \Config\Database::connect();
        $member = $db->table('members')->where('user_id', auth()->id())->get()->getRow('id');

        $data = [
            'judul'            => $judul,
            'slug'             => $slug,
            'tipe'             => 'ide',
            'kategori_id'      => $this->request->getPost('kategori_id') ?: null,
            'konten'           => $this->request->getPost('konten'),
            'author_member_id' => $member ?? null,
            'status'           => 'published',
            'published_at'     => date('Y-m-d H:i:s'),
        ];

        $this->articleModel->insert($data);

        return redirect()->to(base_url('ide/' . $slug))->with('message', 'Ide/gagasan berhasil dipublikasikan!');
    }
}
