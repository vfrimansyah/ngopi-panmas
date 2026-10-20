<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/pemilu/partai') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)"><?= $title ?></h4>
                <small class="text-muted">Masukkan data detail partai politik.</small>
            </div>
        </div>

        <?php if (session('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach (session('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom" style="background: var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                    <i class="fa-solid fa-flag me-2"></i>Form Partai Politik
                </h6>
            </div>
            <div class="card-body p-4">
                <?php $isEdit = isset($partai); ?>
                <form action="<?= $isEdit ? base_url('admin/pemilu/partai/update/' . $partai['id']) : base_url('admin/pemilu/partai/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Partai <span class="text-danger">*</span></label>
                        <input type="text" name="nama_partai" class="form-control" value="<?= old('nama_partai', $isEdit ? $partai['nama_partai'] : '') ?>" required placeholder="Contoh: Partai Keadilan Sejahtera (PKS)">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun Berdiri</label>
                            <input type="number" name="tahun_berdiri" class="form-control" value="<?= old('tahun_berdiri', $isEdit ? $partai['tahun_berdiri'] : '') ?>" placeholder="Contoh: 1998">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cakupan Partai <span class="text-danger">*</span></label>
                            <select name="cakupan" class="form-select" required>
                                <option value="Nasional" <?= old('cakupan', $isEdit ? $partai['cakupan'] : '') === 'Nasional' ? 'selected' : '' ?>>Nasional</option>
                                <option value="Lokal" <?= old('cakupan', $isEdit ? $partai['cakupan'] : '') === 'Lokal' ? 'selected' : '' ?>>Lokal (Aceh)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Azas / Ideologi</label>
                        <input type="text" name="azas_ideologi" class="form-control" value="<?= old('azas_ideologi', $isEdit ? $partai['azas_ideologi'] : '') ?>" placeholder="Contoh: Islamisme, Pancasila">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Kantor Pusat</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap kantor pusat partai..."><?= old('alamat', $isEdit ? $partai['alamat'] : '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Lambang / Logo Partai</label>
                        <input type="file" name="lambang_partai" class="form-control" accept="image/*">
                        <?php if ($isEdit && !empty($partai['lambang_partai'])): ?>
                            <div class="mt-2">
                                <span class="text-muted d-block mb-1 small">Logo Saat Ini:</span>
                                <img src="<?= base_url('uploads/partai/' . $partai['lambang_partai']) ?>" alt="Logo" class="img-thumbnail" style="max-height: 80px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-4" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i>Simpan
                        </button>
                        <a href="<?= base_url('admin/pemilu/partai') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
