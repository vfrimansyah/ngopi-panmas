<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row g-4">
    <!-- Categories List -->
    <div class="col-md-7">
        <div class="card admin-card border-0 p-4 bg-white">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-tags me-2 text-gold"></i>Daftar Kategori Produk</h5>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><strong><?= esc($cat['nama_kategori'] ?? $cat['nama'] ?? '') ?></strong></td>
                                    <td><span class="text-muted small"><?= url_title($cat['nama_kategori'] ?? '', '-', true) ?></span></td>
                                    <td>
                                        <a href="<?= base_url('admin/products/categories/delete/' . $cat['id']) ?>" onclick="return confirm('Hapus kategori ini?')" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada kategori ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Category Form -->
    <div class="col-md-5">
        <div class="card admin-card border-0 p-4 bg-white">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-plus me-2 text-gold"></i>Tambah Kategori</h5>
            
            <form action="<?= base_url('admin/products/categories/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama_kategori" class="form-label fw-bold">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Kopi Bubuk" required>
                </div>

                <button type="submit" class="btn btn-coffee w-100 py-2"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Kategori</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
