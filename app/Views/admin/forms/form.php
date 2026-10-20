<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php $isEdit = !empty($form); ?>

<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url('admin/forms') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark-coffee"><?= esc($title) ?></h4>
                        <small class="text-muted">Atur informasi umum, akses publik, dan pesan konfirmasi form.</small>
                    </div>
                </div>
            </div>

            <?php if (session('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>Terdapat kesalahan:
                    <ul class="mb-0 ps-3 small mt-1">
                        <?php foreach (session('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="<?= $isEdit ? base_url('admin/forms/' . $form['id'] . '/update') : base_url('admin/forms/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Formulir <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="formTitle" class="form-control" value="<?= old('title', $form['title'] ?? '') ?>" placeholder="Contoh: Skrining Kesehatan, Pendataan Hewan Qurban, dll." required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug URL (Tautan Web)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted small"><?= rtrim(base_url('form'), '/') ?>/</span>
                                <input type="text" name="slug" id="formSlug" class="form-control" value="<?= old('slug', $form['slug'] ?? '') ?>" placeholder="otomatis-dibuatkan-dari-judul" autocomplete="off">
                            </div>
                            <div id="slugFeedback" class="small mt-1"></div>
                            <div class="form-text small text-muted">Hanya gunakan huruf kecil, angka, dan tanda hubung (-). Dibuat otomatis saat Anda mengetik judul.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi / Petunjuk Pengisian</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Tuliskan petunjuk atau latar belakang formulir ini..."><?= old('description', $form['description'] ?? '') ?></textarea>
                        </div>

                        <?php 
                            $closedAtVal = !empty($form['closed_at']) ? date('Y-m-d\TH:i', strtotime($form['closed_at'])) : ''; 
                        ?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch p-3 bg-light rounded-3 border h-100">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="is_active" name="is_active" value="1" <?= old('is_active', $form['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="is_active">
                                        Status Formulir Aktif
                                    </label>
                                    <small class="d-block text-muted">Bisa dibuka / ditutup manual kapan saja.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <label class="form-label fw-semibold mb-1" for="closed_at">
                                        <i class="fa-regular fa-clock text-warning me-1"></i>Batas Akhir Pengisian (Deadline)
                                    </label>
                                    <input type="datetime-local" class="form-control" id="closed_at" name="closed_at" value="<?= old('closed_at', $closedAtVal) ?>">
                                    <small class="d-block text-muted mt-1">Opsional. Formulir otomatis ditutup jika waktu ini lewat. Kosongkan jika tanpa batas.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="auto_link_member" name="auto_link_member" value="1" <?= old('auto_link_member', $form['auto_link_member'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="auto_link_member">
                                        Auto-Link ke Anggota
                                    </label>
                                    <small class="d-block text-muted">Cocokkan otomatis nomor HP/NIK ke database anggota portal saat responden mengisi form.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pesan Sukses (Setelah Kirim Jawaban)</label>
                            <textarea name="success_message" rows="2" class="form-control" placeholder="Pesan yang tampil setelah responden berhasil mengirim jawaban"><?= old('success_message', $form['success_message'] ?? 'Terima kasih atas partisipasi Anda! Data Anda telah berhasil disimpan.') ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?= base_url('admin/forms') ?>" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-gold fw-semibold px-4">
                                <i class="fa-solid fa-save me-1"></i><?= $isEdit ? 'Simpan Perubahan' : 'Lanjut ke Form Builder' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    const titleInput = document.getElementById('formTitle');
    const slugInput  = document.getElementById('formSlug');
    const feedback   = document.getElementById('slugFeedback');
    const formId     = <?= (int)($form['id'] ?? 0) ?>;

    let isManualSlug = <?= $isEdit ? 'true' : 'false' ?>;
    let checkTimeout = null;

    function slugify(text) {
        return text.toString().toLowerCase()
            .trim()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/&/g, '-dan-')         // Replace & with 'dan'
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    function checkSlugAvailability(slug) {
        if (!slug) {
            feedback.innerHTML = '';
            return;
        }

        feedback.innerHTML = '<span class="text-muted"><i class="fa-solid fa-spinner fa-spin me-1"></i>Memeriksa ketersediaan URL...</span>';

        fetch(`<?= base_url('admin/forms/check-slug') ?>?slug=${encodeURIComponent(slug)}&id=${formId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'available') {
                    feedback.innerHTML = `<span class="text-success fw-semibold"><i class="fa-solid fa-circle-check me-1"></i>URL tersedia: <code>${data.slug}</code></span>`;
                } else if (data.status === 'taken') {
                    feedback.innerHTML = `
                        <div class="text-warning fw-semibold mt-1">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>URL "<code>${slug}</code>" sudah pernah ada!
                        </div>
                        <div class="text-muted small mt-1">
                            Sistem akan otomatis menggunakan: <span class="badge bg-secondary cursor-pointer" onclick="document.getElementById('formSlug').value='${data.slug}'; checkSlugAvailability('${data.slug}')">${data.slug} (klik untuk pakai)</span>
                        </div>
                    `;
                } else {
                    feedback.innerHTML = '';
                }
            })
            .catch(() => {
                feedback.innerHTML = '';
            });
    }

    titleInput.addEventListener('input', function() {
        if (!isManualSlug) {
            const autoSlug = slugify(this.value);
            slugInput.value = autoSlug;
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(() => checkSlugAvailability(autoSlug), 400);
        }
    });

    slugInput.addEventListener('input', function() {
        isManualSlug = true;
        const currentSlug = slugify(this.value);
        this.value = currentSlug;
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(() => checkSlugAvailability(currentSlug), 400);
    });

    // Check initial slug if editing
    if (slugInput.value) {
        checkSlugAvailability(slugInput.value);
    }
});
</script>
<?= $this->endSection() ?>
