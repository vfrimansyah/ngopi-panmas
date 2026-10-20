<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($rab); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-<?= $isEdit ? 'pen' : 'plus' ?> me-2 text-gold"></i><?= $isEdit ? 'Edit' : 'Tambah' ?> Item RAB Program
        </h4>
        <p class="text-muted mb-0 small"><?= $isEdit ? 'Perbarui alokasi atau input realisasi pengeluaran program.' : 'Tambahkan alokasi anggaran biaya program prioritas baru.' ?></p>
    </div>
    <a href="<?= base_url('admin/dpc/rab') ?>" class="btn btn-sm btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <ul class="mb-0 ps-3">
        <?php foreach (session()->getFlashdata('errors') as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <form action="<?= base_url($isEdit ? 'admin/dpc/rab/update/' . $rab['id'] : 'admin/dpc/rab/store') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bidang DPC <span class="text-danger">*</span></label>
                    <?php $bidVal = old('bidang_kode', $isEdit ? $rab['bidang_kode'] : ''); ?>
                    <select name="bidang_kode" class="form-select" required>
                        <option value="">-- Pilih Bidang --</option>
                        <?php foreach ($bidangs as $b): ?>
                        <option value="<?= esc($b['kode']) ?>" <?= $bidVal === $b['kode'] ? 'selected' : '' ?>>
                            <?= esc($b['kode']) ?> – <?= esc($b['nama_bidang']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nama Program Prioritas <span class="text-danger">*</span></label>
                    <input type="text" name="nama_program" class="form-control" required
                           value="<?= old('nama_program', $isEdit ? $rab['nama_program'] : '') ?>"
                           placeholder="Contoh: Sekolah Digital 9x / Beasiswa 10 siswa">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Waktu / Frekuensi</label>
                    <input type="text" name="waktu_frekuensi" class="form-control"
                           value="<?= old('waktu_frekuensi', $isEdit ? $rab['waktu_frekuensi'] : '') ?>"
                           placeholder="Contoh: Apr–Des / Mar–Mei">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Alokasi Anggaran (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="anggaran" class="form-control" min="0" step="1000" required
                           value="<?= old('anggaran', $isEdit ? (int)$rab['anggaran'] : '0') ?>"
                           placeholder="Contoh: 15000000">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Realisasi Pengeluaran (Rp)</label>
                    <input type="number" name="realisasi_anggaran" class="form-control" min="0" step="1000"
                           value="<?= old('realisasi_anggaran', $isEdit ? (int)$rab['realisasi_anggaran'] : '0') ?>"
                           placeholder="Contoh: 5000000">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Keterangan / Rincian Biaya</label>
                    <input type="text" name="keterangan" class="form-control"
                           value="<?= old('keterangan', $isEdit ? $rab['keterangan'] : '') ?>"
                           placeholder="Contoh: Honor narasumber, modul, konsumsi, dan sewa venue">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-save me-2"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                </button>
                <a href="<?= base_url('admin/dpc/rab') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
