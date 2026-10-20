<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($pengurus); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-<?= $isEdit ? 'pen' : 'plus' ?> me-2 text-gold"></i><?= $isEdit ? 'Edit' : 'Tambah' ?> Data Pengurus
        </h4>
        <p class="text-muted mb-0 small"><?= $isEdit ? 'Perbarui informasi kepengurusan DPC / DPRa.' : 'Tambahkan susunan pengurus DPC / DPRa baru.' ?></p>
    </div>
    <a href="<?= base_url('admin/dpc/struktur') ?>" class="btn btn-sm btn-outline-secondary px-4">
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
        <form action="<?= base_url($isEdit ? 'admin/dpc/struktur/update/' . $pengurus['id'] : 'admin/dpc/struktur/store') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" list="memberSuggestions" required
                           value="<?= old('nama', $isEdit ? $pengurus['nama'] : '') ?>"
                           placeholder="Ketik nama atau pilih dari kader">
                    <datalist id="memberSuggestions">
                        <?php if (!empty($memberList)): ?>
                            <?php foreach ($memberList as $mName): ?>
                            <option value="<?= esc($mName) ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Institusi / Tingkat <span class="text-danger">*</span></label>
                    <?php $instVal = old('institusi', $isEdit ? $pengurus['institusi'] : 'DPC'); ?>
                    <select name="institusi" class="form-select" required>
                        <option value="DPC" <?= $instVal === 'DPC' ? 'selected' : '' ?>>DPC (Kecamatan Panmas)</option>
                        <option value="DPRa Depok" <?= $instVal === 'DPRa Depok' ? 'selected' : '' ?>>DPRa Depok</option>
                        <option value="DPRa Depok Jaya" <?= $instVal === 'DPRa Depok Jaya' ? 'selected' : '' ?>>DPRa Depok Jaya</option>
                        <option value="DPRa Pancoran Mas" <?= $instVal === 'DPRa Pancoran Mas' ? 'selected' : '' ?>>DPRa Pancoran Mas</option>
                        <option value="DPRa Mampang" <?= $instVal === 'DPRa Mampang' ? 'selected' : '' ?>>DPRa Mampang</option>
                        <option value="DPRa Rangkapan Jaya" <?= $instVal === 'DPRa Rangkapan Jaya' ? 'selected' : '' ?>>DPRa Rangkapan Jaya</option>
                        <option value="DPRa Rangkapan Jaya Baru" <?= $instVal === 'DPRa Rangkapan Jaya Baru' ? 'selected' : '' ?>>DPRa Rangkapan Jaya Baru</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="jabatan" class="form-control" required
                           value="<?= old('jabatan', $isEdit ? $pengurus['jabatan'] : '') ?>"
                           placeholder="Contoh: Ketua / Sekretaris / Bendahara / UPKK">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bidang / Divisi Terkait (Khusus DPC)</label>
                    <?php $bidVal = old('bidang_kode', $isEdit ? $pengurus['bidang_kode'] : ''); ?>
                    <select name="bidang_kode" class="form-select">
                        <option value="">-- Bukan Bidang / Non-DPC --</option>
                        <option value="BPH" <?= $bidVal === 'BPH' ? 'selected' : '' ?>>BPH (Badan Pengurus Harian)</option>
                        <?php if (!empty($bidangs)): ?>
                            <?php foreach ($bidangs as $b): ?>
                            <option value="<?= esc($b['kode']) ?>" <?= $bidVal === $b['kode'] ? 'selected' : '' ?>>
                                <?= esc($b['kode']) ?> – <?= esc($b['nama_bidang']) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">No HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="form-control"
                           value="<?= old('no_hp', $isEdit ? $pengurus['no_hp'] : '') ?>"
                           placeholder="Contoh: 08123456789">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control" min="1"
                           value="<?= old('sort_order', $isEdit ? $pengurus['sort_order'] : '10') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Periode Kepengurusan</label>
                    <input type="text" name="periode" class="form-control"
                           value="<?= old('periode', $isEdit ? $pengurus['periode'] : '2025-2030') ?>">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-save me-2"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                </button>
                <a href="<?= base_url('admin/dpc/struktur') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
