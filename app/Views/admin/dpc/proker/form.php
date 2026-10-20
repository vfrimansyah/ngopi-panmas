<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($proker); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-<?= $isEdit ? 'pen' : 'plus' ?> me-2 text-gold"></i><?= $isEdit ? 'Edit' : 'Tambah' ?> Program Kerja
        </h4>
        <p class="text-muted mb-0 small"><?= $isEdit ? 'Perbarui target, KPI, dan informasi program kerja.' : 'Tambahkan rencana program kerja baru untuk bidang DPC.' ?></p>
    </div>
    <a href="<?= base_url('admin/dpc/proker') ?>" class="btn btn-sm btn-outline-secondary px-4">
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
        <form action="<?= base_url($isEdit ? 'admin/dpc/proker/update/' . $proker['id'] : 'admin/dpc/proker/store') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bidang DPC <span class="text-danger">*</span></label>
                    <?php $bidVal = old('bidang_kode', $isEdit ? $proker['bidang_kode'] : ($selectedBidang ?? '')); ?>
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
                    <label class="form-label fw-semibold">Nama Program Kerja <span class="text-danger">*</span></label>
                    <input type="text" name="nama_program" class="form-control" required
                           value="<?= old('nama_program', $isEdit ? $proker['nama_program'] : '') ?>"
                           placeholder="Contoh: SAKTI DPC / Sekolah Digital / Konsultan Keluarga">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Waktu / Frekuensi</label>
                    <input type="text" name="waktu_frekuensi" class="form-control"
                           value="<?= old('waktu_frekuensi', $isEdit ? $proker['waktu_frekuensi'] : '') ?>"
                           placeholder="Contoh: Per triwulan / 2x per tahun / Bulanan">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">PIC / Penanggung Jawab</label>
                    <input type="text" name="pic_nama" class="form-control" list="memberSuggestions"
                           value="<?= old('pic_nama', $isEdit ? $proker['pic_nama'] : '') ?>"
                           placeholder="Nama penanggung jawab program">
                    <datalist id="memberSuggestions">
                        <?php if (!empty($memberList)): ?>
                            <?php foreach ($memberList as $mName): ?>
                            <option value="<?= esc($mName) ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_prioritas" id="is_prioritas" value="1"
                               <?= old('is_prioritas', $isEdit ? $proker['is_prioritas'] : 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_prioritas">
                            <i class="fa-solid fa-star text-warning me-1"></i>Program Prioritas DPC
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Target Indikator Kinerja (KPI)</label>
                    <textarea name="kpi_target" class="form-control" rows="3"
                              placeholder="Indikator target capaian kuantitatif/kualitatif"><?= old('kpi_target', $isEdit ? $proker['kpi_target'] : '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Deskripsi / Ruang Lingkup</label>
                    <textarea name="deskripsi" class="form-control" rows="3"
                              placeholder="Penjelasan detail rencana pelaksanaan program"><?= old('deskripsi', $isEdit ? $proker['deskripsi'] : '') ?></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status Monev</label>
                    <?php $stVal = old('status_monev', $isEdit ? $proker['status_monev'] : 'belum'); ?>
                    <select name="status_monev" class="form-select">
                        <option value="belum" <?= $stVal === 'belum' ? 'selected' : '' ?>>Belum Mulai</option>
                        <option value="berjalan" <?= $stVal === 'berjalan' ? 'selected' : '' ?>>Sedang Berjalan</option>
                        <option value="selesai" <?= $stVal === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="terkendala" <?= $stVal === 'terkendala' ? 'selected' : '' ?>>Terkendala</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Persentase Capaian (%)</label>
                    <input type="number" name="persentase_progres" class="form-control" min="0" max="100"
                           value="<?= old('persentase_progres', $isEdit ? $proker['persentase_progres'] : '0') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Catatan Realisasi Terakhir</label>
                    <input type="text" name="realisasi_capaian" class="form-control"
                           value="<?= old('realisasi_capaian', $isEdit ? $proker['realisasi_capaian'] : '') ?>"
                           placeholder="Ringkasan capaian terkini">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-save me-2"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                </button>
                <a href="<?= base_url('admin/dpc/proker') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
