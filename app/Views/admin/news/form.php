<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card admin-card border-0 p-4 bg-white" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="vintage-serif mb-0"><i class="fa-solid fa-newspaper me-2 text-gold"></i><?= $news ? 'Edit Postingan Berita' : 'Tulis Berita Baru' ?></h5>
        <a href="<?= base_url('admin/news') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-2"></i>Kembali</a>
    </div>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach (session('errors') as $error): ?>
                <?= $error ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= $news ? base_url('admin/news/update/' . $news['id']) : base_url('admin/news/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="judul" class="form-label fw-bold">Judul Berita</label>
            <input type="text" class="form-control" id="judul" name="judul" value="<?= old('judul', $news['judul'] ?? '') ?>" placeholder="Masukkan judul berita" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="category_id" class="form-label fw-bold">Kategori Berita</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('category_id', $news['kategori_id'] ?? $news['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['nama_kategori'] ?? $cat['nama'] ?? '') ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label fw-bold">Status Publikasi</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft" <?= old('status', $news['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft (Simpan Sementara)</option>
                    <option value="published" <?= old('status', $news['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published (Terbitkan)</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="konten" class="form-label fw-bold">Konten / Isi Berita</label>
            <textarea class="form-control" id="konten" name="konten" rows="8" placeholder="Tuliskan isi berita lengkap di sini..." required><?= old('konten', $news['konten'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
            <label for="gambar" class="form-label fw-bold">Gambar Cover</label>
            <input class="form-control" type="file" id="gambar" name="gambar">
            <small class="text-muted">Format: JPG, JPEG, PNG. Max: 2MB.</small>
        </div>

        <?php 
        $existingCover = $news['foto_cover'] ?? $news['gambar'] ?? null;
        $imgSrc = null;
        if ($existingCover) {
            if (file_exists('uploads/news/' . $existingCover)) {
                $imgSrc = base_url('uploads/news/' . $existingCover);
            } elseif (file_exists('uploads/' . $existingCover)) {
                $imgSrc = base_url('uploads/' . $existingCover);
            }
        }
        ?>
        <?php if ($imgSrc): ?>
            <div class="mb-4">
                <p class="fw-bold mb-2">Gambar Sampul Saat Ini:</p>
                <img src="<?= $imgSrc ?>" alt="Gambar sampul saat ini" class="rounded img-thumbnail" style="max-height: 150px;">
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-coffee px-4 py-2"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Postingan</button>
    </form>
</div>

<?= $this->endSection() ?>
