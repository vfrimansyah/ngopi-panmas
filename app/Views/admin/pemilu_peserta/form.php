<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/pemilu/peserta') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)"><?= $title ?></h4>
                <small class="text-muted">Hubungkan Partai dengan Pemilu tahun tertentu.</small>
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

        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom" style="background: var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                    <i class="fa-solid fa-list-ol me-2"></i>Form Peserta Pemilu
                </h6>
            </div>
            <div class="card-body p-4">
                <?php $isEdit = isset($peserta); ?>
                <form action="<?= $isEdit ? base_url('admin/pemilu/peserta/update/' . $peserta['id']) : base_url('admin/pemilu/peserta/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Partai Politik <span class="text-danger">*</span></label>
                        <?php if ($isEdit): ?>
                            <?php 
                                $selectedPartaiName = '';
                                foreach ($partai as $p) {
                                    if ($p['id'] == $peserta['partai_id']) {
                                        $selectedPartaiName = $p['nama_partai'] . ' (' . $p['cakupan'] . ')';
                                        break;
                                    }
                                }
                            ?>
                            <input type="text" class="form-control bg-light" value="<?= esc($selectedPartaiName) ?>" readonly>
                            <input type="hidden" name="partai_id" value="<?= $peserta['partai_id'] ?>">
                        <?php else: ?>
                            <select name="partai_id" class="form-select" required>
                                <option value="">-- Pilih Partai --</option>
                                <?php foreach ($partai as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= old('partai_id') == $p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['nama_partai']) ?> (<?= $p['cakupan'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun Pemilu <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="<?= old('tahun', $isEdit ? $peserta['tahun'] : '2024') ?>" required placeholder="Contoh: 2024">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Urut Partai <span class="text-danger">*</span></label>
                            <input type="number" name="no_urut" class="form-control" value="<?= old('no_urut', $isEdit ? $peserta['no_urut'] : '') ?>" required placeholder="Contoh: 8">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ketua Umum Partai (Saat Pemilu Tersebut) <span class="text-danger">*</span></label>
                        <input type="text" name="ketua_partai" id="ketua_partai" class="form-control" value="<?= old('ketua_partai', $isEdit ? $peserta['ketua_partai'] : '') ?>" required placeholder="Contoh: Ahmad Syaikhu">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-4" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i>Simpan
                        </button>
                        <a href="<?= base_url('admin/pemilu/peserta') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
