<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card admin-card border-0 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="vintage-serif mb-0"><i class="fa-solid fa-store me-2 text-gold"></i>Kelola Produk UMKM Anggota</h5>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/products/create') ?>" class="btn btn-coffee btn-sm"><i class="fa-solid fa-plus me-2"></i>Tambah Produk</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table align-middle no-datatable">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $photos = json_decode($product['foto_urls'] ?? '[]', true);
                        $thumb = !empty($photos[0]) ? $photos[0] : null;
                        $imgSrc = null;
                        if ($thumb) {
                            if (file_exists('uploads/products/' . $thumb)) {
                                $imgSrc = base_url('uploads/products/' . $thumb);
                            } elseif (file_exists('uploads/' . $thumb)) {
                                $imgSrc = base_url('uploads/' . $thumb);
                            }
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="rounded border bg-light overflow-hidden" style="width: 50px; height: 50px;">
                                    <?php if ($imgSrc): ?>
                                        <img src="<?= $imgSrc ?>" alt="Gambar" class="w-100 h-100 object-fit-cover">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light text-muted">
                                            <i class="fa-solid fa-box-open"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong><?= esc($product['nama_produk'] ?? $product['nama'] ?? '') ?></strong><br>
                                <?php if (!empty($product['satuan'])): ?>
                                <small class="text-muted">Satuan: <?= esc($product['satuan']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><strong>Rp <?= number_format($product['harga'] ?? 0, 0, ',', '.') ?></strong></td>
                            <td>
                                <?php
                                $status = $product['status'] ?? 'pending';
                                $bgClass = match($status) {
                                    'published' => 'success',
                                    'pending'   => 'warning text-dark',
                                    'rejected'  => 'danger',
                                    default     => 'secondary',
                                };
                                ?>
                                <span class="badge bg-<?= $bgClass ?>"><?= esc(ucfirst($status)) ?></span>
                            </td>
                            <td>
                                <?php if ($can_update ?? false): ?>
                                <a href="<?= base_url('admin/products/edit/' . $product['id']) ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen-to-square"></i></a>
                                <?php endif; ?>
                                <?php if ($can_delete ?? false): ?>
                                <a href="<?= base_url('admin/products/delete/' . $product['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada produk terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        <?= $pager ? $pager->links() : '' ?>
    </div>
</div>

<?= $this->endSection() ?>
