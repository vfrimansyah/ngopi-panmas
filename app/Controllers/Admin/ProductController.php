<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberProductModel;
use App\Models\ProductCategoryModel;
use App\Models\MemberModel;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new MemberProductModel();
        $this->categoryModel = new ProductCategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Produk - Admin',
            'products' => $this->productModel->orderBy('created_at', 'DESC')->findAll(),
            'pager' => null,
        ];

        $perms = $this->getPagePermissions('products');
        $data['can_create'] = $perms['canCreate'];
        $data['can_update'] = $perms['canUpdate'];
        $data['can_delete'] = $perms['canDelete'];

        return view('admin/products/index', $data);
    }

    public function create()
    {
        return view('admin/products/form', [
            'title' => 'Tambah Produk - Admin',
            'product' => null,
            'categories' => $this->categoryModel->findAll(),
            'members' => (new MemberModel())->where('is_active', 1)->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'nama_produk' => 'required|min_length[3]|max_length[255]',
            'kategori_id' => 'required|integer',
            'member_id'   => 'required|integer',
            'harga'       => 'permit_empty|decimal',
            'kontak_whatsapp' => 'required|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $gambarName = null;

        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $gambarName = $gambar->getRandomName();
            $gambar->move('uploads/products', $gambarName);
        }

        $fotoUrls = $gambarName ? json_encode([$gambarName]) : json_encode([]);

        $this->productModel->save([
            'member_id'   => $this->request->getPost('member_id'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'harga'       => $this->request->getPost('harga') ?: 0,
            'satuan'      => $this->request->getPost('satuan') ?: 'Pcs',
            'foto_urls'   => $fotoUrls,
            'kontak_whatsapp' => $this->request->getPost('kontak_whatsapp'),
            'link_eksternal' => $this->request->getPost('link_eksternal'),
            'status'      => $this->request->getPost('status') ?? 'pending',
        ]);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/products/form', [
            'title' => 'Edit Produk - Admin',
            'product' => $product,
            'categories' => $this->categoryModel->findAll(),
            'members' => (new MemberModel())->where('is_active', 1)->findAll(),
        ]);
    }

    public function update($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'nama_produk' => 'required|min_length[3]|max_length[255]',
            'kategori_id' => 'required|integer',
            'member_id'   => 'required|integer',
            'harga'       => 'permit_empty|decimal',
            'kontak_whatsapp' => 'required|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $gambarName = null;

        // Try reading existing image from JSON array
        $existingPhotos = json_decode($product['foto_urls'] ?? '[]', true);
        $oldImageName = $existingPhotos[0] ?? null;

        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            if ($oldImageName && file_exists('uploads/products/' . $oldImageName)) {
                unlink('uploads/products/' . $oldImageName);
            }
            $gambarName = $gambar->getRandomName();
            $gambar->move('uploads/products', $gambarName);
            $fotoUrls = json_encode([$gambarName]);
        } else {
            $fotoUrls = $product['foto_urls'];
        }

        $this->productModel->update($id, [
            'member_id'   => $this->request->getPost('member_id'),
            'kategori_id' => $this->request->getPost('kategori_id'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'harga'       => $this->request->getPost('harga') ?: 0,
            'satuan'      => $this->request->getPost('satuan') ?: 'Pcs',
            'foto_urls'   => $fotoUrls,
            'kontak_whatsapp' => $this->request->getPost('kontak_whatsapp'),
            'link_eksternal' => $this->request->getPost('link_eksternal'),
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $existingPhotos = json_decode($product['foto_urls'] ?? '[]', true);
        foreach ($existingPhotos as $img) {
            if ($img && file_exists('uploads/products/' . $img)) {
                unlink('uploads/products/' . $img);
            }
        }

        $this->productModel->delete($id);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil dihapus.');
    }

    // Category management
    public function categories()
    {
        return view('admin/products/categories', [
            'title' => 'Kategori Produk - Admin',
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    public function storeCategory()
    {
        $rules = ['nama_kategori' => 'required|min_length[2]|max_length[100]'];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'is_active' => 1,
        ]);

        return redirect()->to('/admin/products/categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function deleteCategory($id = null)
    {
        $this->categoryModel->delete($id);
        return redirect()->to('/admin/products/categories')->with('success', 'Kategori berhasil dihapus.');
    }
}
