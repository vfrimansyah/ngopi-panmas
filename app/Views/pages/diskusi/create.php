<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-plus-circle"></i>
                Mulai Diskusi Baru
            </div>
            <h1 class="page-hero-title">Buat Topik Diskusi Baru</h1>
            <p class="page-hero-desc">
                Sampaikan pertanyaan, topik obrolan, atau pembahasan menarik untuk didiskusikan bersama komunitas.
            </p>
        </div>
    </div>
</section>

<!-- Form Content -->
<section style="padding:50px 0 70px; background:#fff;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:30px;">
                    
                    <?php if (session('errors')): ?>
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            <?php foreach (session('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form action="<?= base_url('diskusi/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="judul" class="form-label fw-bold text-dark-coffee">Judul / Topik Diskusi <span class="text-danger">*</span></label>
                            <input type="text" name="judul" id="judul" class="form-control" placeholder="Contoh: Bagaimana pendapat Anda tentang penerapan efisiensi energi bagi UMKM?" value="<?= old('judul') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="kategori_id" class="form-label fw-bold text-dark-coffee">Kategori Diskusi</label>
                            <select name="kategori_id" id="kategori_id" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= old('kategori_id') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['nama_kategori']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="konten" class="form-label fw-bold text-dark-coffee">Detail Pembahasan <span class="text-danger">*</span></label>
                            <textarea name="konten" id="konten" class="form-control" rows="8" placeholder="Uraikan pertanyaan atau konteks diskusi secara jelas..." required><?= old('konten') ?></textarea>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="<?= base_url('diskusi') ?>" class="btn-coffee-outline">Batal</a>
                            <button type="submit" class="btn-gold" id="btn-submit-diskusi">
                                <i class="fa-solid fa-paper-plane me-1"></i>Mulai Diskusi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
