<?php

namespace App\Controllers;

use App\Models\MemberProductModel;
use App\Models\ProductCategoryModel;

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
        $categorySlug = $this->request->getGet('kategori');
        
        $builder = $this->productModel->where('status', 'published');

        if ($categorySlug) {
            $category = $this->categoryModel->where('nama_kategori', $categorySlug)->first();
            if ($category) {
                $builder->where('kategori_id', $category['id']);
            }
        }

        $data = [
            'title' => 'Katalog Produk — Ngobrol Pikiran Indonesia',
            'products' => $builder->orderBy('created_at', 'DESC')->paginate(12),
            'pager' => $this->productModel->pager,
            'categories' => $this->categoryModel->findAll(),
            'selectedCategory' => $categorySlug,
        ];

        return view('pages/products/index', $data);
    }

    public function show($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $memberModel = new \App\Models\MemberModel();

        $data = [
            'title' => $product['nama_produk'] . ' — Ngobrol Pikiran Indonesia',
            'product' => $product,
            'member' => $memberModel->find($product['member_id']),
            'relatedProducts' => $this->productModel->where('kategori_id', $product['kategori_id'])
                                                     ->where('id !=', $product['id'])
                                                     ->where('status', 'published')
                                                     ->limit(4)
                                                     ->find(),
        ];

        return view('pages/products/detail', $data);
    }
}
