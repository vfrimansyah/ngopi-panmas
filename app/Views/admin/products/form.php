<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card admin-card border-0 p-4 bg-white" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="vintage-serif mb-0"><i class="fa-solid fa-box-open me-2 text-gold"></i><?= $product ? 'Edit Produk' : 'Tambah Produk Baru' ?></h5>
        <a href="<?= base_url('admin/products') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-2"></i>Kembali</a>
    </div>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger mb-4">
            <?php foreach (session('errors') as $error): ?>
                <?= esc($error) ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= $product ? base_url('admin/products/update/' . $product['id']) : base_url('admin/products/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="nama_produk" class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?= old('nama_produk', $product['nama_produk'] ?? $product['nama'] ?? '') ?>" placeholder="Masukkan nama produk" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="kategori_id" class="form-label fw-bold">Kategori Produk <span class="text-danger">*</span></label>
                <select class="form-select" id="kategori_id" name="kategori_id" required>
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('kategori_id', $product['kategori_id'] ?? $product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['nama_kategori'] ?? $cat['nama'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="member_id" class="form-label fw-bold">Produsen / Pemilik (Anggota) <span class="text-danger">*</span></label>
                <select class="form-select" id="member_id" name="member_id" required>
                    <option value="" disabled selected>-- Pilih Produsen --</option>
                    <?php if (!empty($members)): ?>
                        <?php foreach ($members as $mem): ?>
                            <option value="<?= $mem['id'] ?>" <?= old('member_id', $product['member_id'] ?? '') == $mem['id'] ? 'selected' : '' ?>>
                                <?= esc($mem['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="harga" class="form-label fw-bold">Harga Produk (Rp)</label>
                <input type="number" step="1" class="form-control" id="harga" name="harga" value="<?= old('harga', $product['harga'] ?? '') ?>" placeholder="Nominal harga">
            </div>
            <div class="col-md-4">
                <label for="satuan" class="form-label fw-bold">Satuan</label>
                <input type="text" class="form-control" id="satuan" name="satuan" value="<?= old('satuan', $product['satuan'] ?? 'Pcs') ?>" placeholder="Pcs / Pack / Kg">
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label fw-bold">Status Produk</label>
                <select class="form-select" id="status" name="status">
                    <option value="published" <?= old('status', $product['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Terbitkan (Published)</option>
                    <option value="pending" <?= old('status', $product['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                    <option value="rejected" <?= old('status', $product['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                    <option value="nonaktif" <?= old('status', $product['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="kontak_whatsapp" class="form-label fw-bold">Kontak WhatsApp <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="kontak_whatsapp" name="kontak_whatsapp" value="<?= old('kontak_whatsapp', $product['kontak_whatsapp'] ?? '') ?>" placeholder="Contoh: 081234567890" required>
            </div>
            <div class="col-md-6">
                <label for="link_eksternal" class="form-label fw-bold">Link Toko Eksternal (Optional)</label>
                <input type="url" class="form-control" id="link_eksternal" name="link_eksternal" value="<?= old('link_eksternal', $product['link_eksternal'] ?? '') ?>" placeholder="https://tokopedia.com/...">
            </div>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label fw-bold">Deskripsi Produk</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Tuliskan deskripsi lengkap mengenai produk ini"><?= old('deskripsi', $product['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
            <label for="gambar" class="form-label fw-bold">Gambar Produk</label>
            <input class="form-control" type="file" id="gambar" name="gambar">
            <small class="text-muted">Format: JPG, JPEG, PNG. Max: 2MB.</small>
        </div>

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
        <?php if ($imgSrc): ?>
            <div class="mb-4">
                <p class="fw-bold mb-2">Gambar Saat Ini:</p>
                <img src="<?= $imgSrc ?>" alt="Gambar produk" class="rounded img-thumbnail" style="max-height: 150px;">
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-coffee px-4 py-2"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Produk</button>
    </form>
</div>

<?= $this->endSection() ?>
