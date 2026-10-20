<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-rectangle-list me-2 text-gold"></i>List Kegiatan
        </h4>
        <p class="text-muted mb-0 small">Daftar seluruh laporan kegiatan organisasi dengan fitur filter dan ekspor data.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/kegiatan/create') ?>" class="btn btn-sm btn-gold px-4">
        <i class="fa-solid fa-plus me-2"></i>Tambah Kegiatan
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Form -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="<?= esc($filters['from_date'] ?? '') ?>">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="<?= esc($filters['to_date'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Penyelenggara</label>
                <select name="penyelenggara" class="form-select form-select-sm">
                    <option value="">-- Semua --</option>
                    <?php
                    $penyelenggaraOptions = [];
                    if (!empty($dpraList)) {
                        foreach ($dpraList as $d) {
                            $penyelenggaraOptions[$d['nama_dpra']] = $d['nama_dpra'];
                        }
                    } else {
                        $penyelenggaraOptions = [
                            'DPRa Depok'               => 'DPRa Depok',
                            'DPRa Depok Jaya'          => 'DPRa Depok Jaya',
                            'DPRa Pancoran Mas'        => 'DPRa Pancoran Mas',
                            'DPRa Mampang'             => 'DPRa Mampang',
                            'DPRa Rangkapan Jaya'      => 'DPRa Rangkapan Jaya',
                            'DPRa Rangkapan Jaya Baru' => 'DPRa Rangkapan Jaya Baru',
                        ];
                    }
                    $penyelenggaraOptions = array_merge($penyelenggaraOptions, [
                        'DPC'            => 'DPC PKS Pancoran Mas',
                        'BPH'            => 'BPH',
                        'Bipeka'         => 'Bipeka',
                        'Bid Kepemudaan' => 'Bid Kepemudaan',
                        'BKO'            => 'BKO',
                        'Bid Kessos'     => 'Bid Kessos',
                        'Grup UPA'       => 'Grup UPA',
                    ]);
                    foreach ($penyelenggaraOptions as $val => $lbl):
                    ?>
                    <option value="<?= $val ?>" <?= ($filters['penyelenggara'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Jenis Kegiatan</label>
                <select name="jenis_kegiatan_id" class="form-select form-select-sm">
                    <option value="">-- Semua --</option>
                    <?php foreach ($jenisList as $j): ?>
                    <option value="<?= $j['id'] ?>" <?= ($filters['jenis_kegiatan_id'] ?? '') == $j['id'] ? 'selected' : '' ?>><?= esc($j['nama_kegiatan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1.5">
                <label class="form-label small fw-semibold mb-1">Lokasi (DPRa)</label>
                <select name="dpra" class="form-select form-select-sm">
                    <option value="">-- Semua --</option>
                    <?php foreach ($penyelenggaraOptions as $val => $lbl): if (strpos($val, 'DPRa') === 0): ?>
                    <option value="<?= $val ?>" <?= ($filters['dpra'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <div class="col-md-1.5">
                <label class="form-label small fw-semibold mb-1">Status Wilayah</label>
                <select name="status_kondisi" class="form-select form-select-sm">
                    <option value="">-- Semua --</option>
                    <option value="hijau" <?= ($filters['status_kondisi'] ?? '') === 'hijau' ? 'selected' : '' ?>>🟢 Hijau</option>
                    <option value="kuning" <?= ($filters['status_kondisi'] ?? '') === 'kuning' ? 'selected' : '' ?>>🟡 Kuning</option>
                    <option value="merah" <?= ($filters['status_kondisi'] ?? '') === 'merah' ? 'selected' : '' ?>>🔴 Merah</option>
                </select>
            </div>
            <div class="col-md-1 col-6">
                <label class="form-label small fw-semibold mb-1">RW</label>
                <input type="text" name="rw" class="form-control form-control-sm" placeholder="RW" value="<?= esc($filters['rw'] ?? '') ?>">
            </div>
            <div class="col-md-1 col-6 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">GO</button>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
            <a href="<?= base_url('admin/kegiatan/export-excel?' . http_build_query($filters)) ?>" class="btn btn-sm btn-success px-3">
                <i class="fa-solid fa-file-excel me-1"></i> Excel Export
            </a>
            <a href="<?= base_url('admin/kegiatan/list') ?>" class="btn btn-sm btn-outline-secondary px-3">Reset Filter</a>
        </div>
    </div>
</div>

<!-- Table List Kegiatan -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 170px;">Tanggal & Jam</th>
                        <th>Nama Kegiatan</th>
                        <th>Lokasi</th>
                        <th>Catatan</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($kegiatans)): ?>
                        <?php foreach ($kegiatans as $item): ?>
                            <tr>
                                <td class="ps-4 align-top py-3">
                                    <div class="fw-semibold text-dark">
                                        Tgl : <?= !empty($item['tanggal']) ? date('d M Y', strtotime($item['tanggal'])) : '-' ?>
                                        <br>
                                        <small class="text-muted">(<?= !empty($item['tanggal']) ? date('D', strtotime($item['tanggal'])) : '' ?>)</small>
                                    </div>
                                    <div class="small text-secondary mt-1">
                                        Jam : <?= esc($item['jam_mulai'] ?: '-') ?> - <?= esc($item['jam_selesai'] ?: '-') ?>
                                    </div>
                                    <?php if (!empty($item['flyer'])): ?>
                                        <a href="<?= base_url('uploads/kegiatan/' . $item['flyer']) ?>" target="_blank" class="btn btn-xs btn-outline-info mt-2">
                                            <i class="fa-solid fa-image me-1"></i> View Flyer
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="align-top py-3">
                                    <div class="fw-bold text-dark-coffee fs-6"><?= esc($item['nama_kegiatan']) ?></div>
                                    <div class="small text-muted mt-1">Oleh : <span class="badge bg-secondary-subtle text-secondary"><?= esc($item['penyelenggara'] ?: '-') ?></span></div>
                                    <div class="small text-muted">PIC : <span class="fw-semibold"><?= esc($item['pic'] ?: '-') ?></span></div>
                                    <div class="small text-muted">Jml Peserta : <span class="badge bg-info-subtle text-info fw-bold"><?= $item['jumlah_peserta'] ?></span></div>
                                </td>
                                <td class="align-top py-3">
                                    <div class="fw-semibold text-dark"><?= esc($item['lokasi_kegiatan'] ?: '-') ?></div>
                                    <div class="small text-muted mt-1">RW : <?= esc($item['rw'] ?: '0') ?>, RT : <?= esc($item['rt'] ?: '0') ?></div>
                                    <div class="small text-muted mb-1"><span class="badge bg-success-subtle text-success"><?= esc($item['dpra'] ?: '-') ?></span></div>
                                    <?php
                                    $st = $item['status_kondisi'] ?? 'hijau';
                                    if ($st === 'merah'):
                                    ?>
                                        <span class="badge bg-danger text-white px-2 py-1">🔴 Merah</span>
                                    <?php elseif ($st === 'kuning'): ?>
                                        <span class="badge bg-warning text-dark px-2 py-1">🟡 Kuning</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success px-2 py-1">🟢 Hijau</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-top py-3">
                                    <?php if (!empty($item['tokoh_hadir'])): ?>
                                        <div class="small"><strong class="text-dark">Tokoh Hadir :</strong> <?= esc($item['tokoh_hadir']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['tokoh_pks'])): ?>
                                        <div class="small"><strong class="text-dark">Tokoh PKS :</strong> <?= esc($item['tokoh_pks']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['catatan'])): ?>
                                        <div class="small text-muted mt-1"><?= esc($item['catatan']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['link_doc'])): ?>
                                        <div class="small mt-1">
                                            <a href="<?= esc($item['link_doc']) ?>" target="_blank" class="text-decoration-none"><i class="fa-solid fa-link me-1"></i> Link Doc</a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-top py-3 pe-4">
                                    <?php 
                                    $isOwner = !empty($is_full_admin) || ((int)($item['created_by'] ?? 0) === (int)($current_user_id ?? 0));
                                    ?>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="<?= base_url('admin/kegiatan/absensi/' . $item['id']) ?>" class="btn btn-sm btn-outline-success" title="Absensi">
                                            <i class="fa-solid fa-clipboard-check"></i>
                                        </a>
                                        <?php if (($can_update ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/kegiatan/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (($can_delete ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/kegiatan/delete/' . $item['id']) ?>" 
                                            class="btn btn-sm btn-outline-danger"
                                            title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data kegiatan ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada data kegiatan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-dark-coffee { color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
