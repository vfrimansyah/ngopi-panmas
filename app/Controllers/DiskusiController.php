<?php

namespace App\Controllers;

use App\Models\DiscussionModel;
use App\Models\DiscussionReplyModel;
use App\Models\ArticleCategoryModel;

class DiskusiController extends BaseController
{
    protected $discussionModel;
    protected $replyModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->discussionModel = new DiscussionModel();
        $this->replyModel      = new DiscussionReplyModel();
        $this->categoryModel   = new ArticleCategoryModel();
    }

    public function index()
    {
        $categorySlug = $this->request->getGet('kategori');
        $search       = $this->request->getGet('q');
        $sort         = $this->request->getGet('sort') ?? 'terbaru';

        $builder = $this->discussionModel->where('status', 'published');

        if ($categorySlug) {
            $category = $this->categoryModel->where('nama_kategori', $categorySlug)->first();
            if ($category) {
                $builder->where('kategori_id', $category['id']);
            }
        }

        if ($search) {
            $builder->like('judul', $search);
        }

        if ($sort === 'terpopuler') {
            $builder->orderBy('upvotes_count', 'DESC')->orderBy('views_count', 'DESC');
        } else {
            $builder->orderBy('is_pinned', 'DESC')->orderBy('created_at', 'DESC');
        }

        $data = [
            'title'            => 'Forum Diskusi — Ngobrol Pikiran Indonesia',
            'metaDesc'         => 'Forum diskusi terbuka komunitas Ngobrol Pikiran Indonesia. Sampaikan pandangan, tanyakan solusi, dan berdiskusi.',
            'discussions'      => $builder->paginate(10),
            'pager'            => $this->discussionModel->pager,
            'categories'       => $this->categoryModel->findAll(),
            'selectedCategory' => $categorySlug,
            'selectedSort'     => $sort,
        ];

        return view('pages/diskusi/index', $data);
    }

    public function show($slug = null)
    {
        $discussion = $this->discussionModel->where('slug', $slug)
                                             ->where('status', 'published')
                                             ->first();

        if (!$discussion) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment views count
        $this->discussionModel->update($discussion['id'], ['views_count' => ($discussion['views_count'] ?? 0) + 1]);

        // Get author info
        $db = \Config\Database::connect();
        $author = null;
        if (!empty($discussion['author_member_id'])) {
            $author = $db->table('members')->where('id', $discussion['author_member_id'])->get()->getRowArray();
        }

        // Get replies
        $replies = $db->table('discussion_replies')
                      ->select('discussion_replies.*, members.nama_lengkap, members.status_anggota')
                      ->join('members', 'members.id = discussion_replies.member_id', 'left')
                      ->where('discussion_replies.discussion_id', $discussion['id'])
                      ->orderBy('discussion_replies.is_best_answer', 'DESC')
                      ->orderBy('discussion_replies.created_at', 'ASC')
                      ->get()->getResultArray();

        $data = [
            'title'              => $discussion['judul'] . ' — Ngobrol Pikiran Indonesia',
            'discussion'         => $discussion,
            'author'             => $author,
            'replies'            => $replies,
            'recentDiscussions'  => $this->discussionModel->where('status', 'published')
                                                          ->where('id !=', $discussion['id'])
                                                          ->orderBy('created_at', 'DESC')
                                                          ->limit(5)
                                                          ->find(),
        ];

        return view('pages/diskusi/detail', $data);
    }

    public function create()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk memulai diskusi baru.');
        }

        $data = [
            'title'      => 'Mulai Diskusi Baru — Ngobrol Pikiran Indonesia',
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('pages/diskusi/create', $data);
    }

    public function store()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = [
            'judul'  => 'required|min_length[5]|max_length[255]',
            'konten' => 'required|min_length[15]',
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
            'kategori_id'      => $this->request->getPost('kategori_id') ?: null,
            'konten'           => $this->request->getPost('konten'),
            'author_member_id' => $member ?? null,
            'status'           => 'published',
        ];

        $this->discussionModel->insert($data);

        return redirect()->to(base_url('diskusi/' . $slug))->with('message', 'Diskusi baru berhasil dibuat!');
    }

    public function reply($discussionId = null)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk membalas diskusi.');
        }

        $konten = trim($this->request->getPost('konten'));
        if (empty($konten)) {
            return redirect()->back()->with('error', 'Isi balasan tidak boleh kosong.');
        }

        $db = \Config\Database::connect();
        $member = $db->table('members')->where('user_id', auth()->id())->get()->getRow('id');

        $this->replyModel->insert([
            'discussion_id' => $discussionId,
            'member_id'     => $member ?? null,
            'konten'        => $konten,
        ]);

        // Increment replies count on discussion
        $discussion = $this->discussionModel->find($discussionId);
        if ($discussion) {
            $this->discussionModel->update($discussionId, [
                'replies_count' => ($discussion['replies_count'] ?? 0) + 1
            ]);
            return redirect()->to(base_url('diskusi/' . $discussion['slug']))->with('message', 'Balasan berhasil dikirim!');
        }

        return redirect()->back();
    }

    public function vote($discussionId = null)
    {
        if (!auth()->loggedIn()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu']);
        }

        $discussion = $this->discussionModel->find($discussionId);
        if ($discussion) {
            $newUpvotes = ($discussion['upvotes_count'] ?? 0) + 1;
            $this->discussionModel->update($discussionId, ['upvotes_count' => $newUpvotes]);
            return $this->response->setJSON(['status' => 'success', 'upvotes' => $newUpvotes]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Diskusi tidak ditemukan']);
    }
}
