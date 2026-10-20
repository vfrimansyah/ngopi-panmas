<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-users-viewfinder me-2 text-gold"></i>Struktur Pengurus DPC & DPRa
        </h4>
        <p class="text-muted mb-0 small">Bagan organisasi dan susunan pengurus DPC PKS Panmas & 6 DPRa Kelurahan Periode 2025–2030.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/dpc/struktur/export-excel?institusi=' . urlencode($activeTab)) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-excel me-1"></i>Excel Export
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/dpc/struktur/create') ?>" class="btn btn-sm btn-gold px-4 fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Tambah Pengurus
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Tabs Navigation -->
<ul class="nav nav-pills mb-4 bg-white p-2 shadow-sm rounded-3">
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === 'dpc' ? 'active' : '' ?>" href="<?= base_url('admin/dpc/struktur?tab=dpc') ?>" style="<?= $activeTab === 'dpc' ? 'background-color: #3e2723 !important; color: #ffffff !important;' : 'color: #212529;' ?>">
            <i class="fa-solid fa-landmark me-1 text-gold"></i>Pengurus DPC
        </a>
    </li>
    <?php foreach ($institusiOptions as $inst): ?>
        <?php if ($inst !== 'DPC'): ?>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === $inst ? 'active' : '' ?>" href="<?= base_url('admin/dpc/struktur?tab=' . urlencode($inst)) ?>" style="<?= $activeTab === $inst ? 'background-color: #3e2723 !important; color: #ffffff !important;' : 'color: #212529;' ?>">
                    <i class="fa-solid fa-building me-1"></i><?= esc($inst) ?>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === 'all' ? 'active' : '' ?>" href="<?= base_url('admin/dpc/struktur?tab=all') ?>" style="<?= $activeTab === 'all' ? 'background-color: #3e2723 !important; color: #ffffff !important;' : 'color: #212529;' ?>">
            <i class="fa-solid fa-list me-1"></i>Semua (47)
        </a>
    </li>
</ul>

<?php if ($activeTab === 'dpc'): ?>
<!-- Visual Org Chart Card for DPC -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(180deg, #fafafa 0%, #f3f4f6 100%);">
    <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
        <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-sitemap me-2 text-gold"></i>Bagan Struktur Inti DPC PKS Pancoran Mas</h6>
    </div>
    <div class="card-body p-4">
        <!-- BPH Core Row Hierarchical -->
        <?php
        $ketua = null;
        $sekretaris = null;
        $wkSekretaris = [];
        $bendahara = null;
        $wkBendahara = [];

        foreach ($dpcBph as $bph) {
            $j = $bph['jabatan'];
            if (stripos($j, 'Ketua') !== false && stripos($j, 'Wk') === false) {
                $ketua = $bph;
            } elseif (stripos($j, 'Wk Sekretaris') !== false) {
                $wkSekretaris[] = $bph;
            } elseif (stripos($j, 'Sekretaris') !== false) {
                $sekretaris = $bph;
            } elseif (stripos($j, 'Wk Bendahara') !== false) {
                $wkBendahara[] = $bph;
            } elseif (stripos($j, 'Bendahara') !== false) {
                $bendahara = $bph;
            }
        }
        ?>
        <div class="text-center mb-4">
            <h6 class="text-uppercase fw-bold text-muted small mb-4" style="letter-spacing: 1px;">Badan Pengurus Harian (BPH)</h6>
            
            <!-- Top Level: KETUA -->
            <?php if ($ketua): ?>
            <div class="d-flex justify-content-center mb-4">
                <div class="card border-0 shadow-sm text-center p-3" style="width: 210px; border-radius: 12px; background: #ffffff; border-top: 5px solid var(--gold) !important;">
                    <div class="avatar mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: var(--gold)22; color: var(--dark-coffee);">
                        <i class="fa-solid fa-user-tie fs-4 text-gold"></i>
                    </div>
                    <div class="fw-bold text-dark text-truncate" style="font-size: 1.05rem;" title="<?= esc($ketua['nama']) ?>"><?= esc($ketua['nama']) ?></div>
                    <span class="badge px-3 py-1 mt-1 fw-bold" style="background: var(--dark-coffee); color: var(--cream); font-size: 0.75rem;"><?= esc($ketua['jabatan']) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Level 2: Two Columns (Sekretariat & Kebendaharaan) -->
            <div class="row justify-content-center g-4">
                <!-- Column 1: SEKRETARIAT -->
                <div class="col-md-6 col-lg-5">
                    <div class="p-3 rounded-3" style="background: rgba(212, 175, 55, 0.06); border: 1px dashed rgba(62, 39, 35, 0.2);">
                        <!-- Sekretaris Utama -->
                        <?php if ($sekretaris): ?>
                        <div class="card border-0 shadow-sm text-center p-3 mb-3 mx-auto" style="max-width: 200px; border-radius: 10px; background: #ffffff; border-top: 4px solid var(--dark-coffee) !important;">
                            <div class="avatar mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background: rgba(62, 39, 35, 0.1); color: var(--dark-coffee);">
                                <i class="fa-solid fa-file-pen fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark text-truncate" title="<?= esc($sekretaris['nama']) ?>"><?= esc($sekretaris['nama']) ?></div>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis small mt-1"><?= esc($sekretaris['jabatan']) ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Sub: Wakil Sekretaris -->
                        <?php if (!empty($wkSekretaris)): ?>
                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            <?php foreach ($wkSekretaris as $ws): ?>
                            <div class="card border-0 shadow-sm text-center p-2" style="width: 145px; border-radius: 8px; background: #ffffff; border-top: 3px solid #6c757d !important;">
                                <div class="fw-semibold text-dark small text-truncate" title="<?= esc($ws['nama']) ?>"><?= esc($ws['nama']) ?></div>
                                <small class="text-muted" style="font-size: 0.72rem;"><?= esc($ws['jabatan']) ?></small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 2: KEBENDAHARAAN -->
                <div class="col-md-6 col-lg-5">
                    <div class="p-3 rounded-3" style="background: rgba(212, 175, 55, 0.06); border: 1px dashed rgba(62, 39, 35, 0.2);">
                        <!-- Bendahara Utama -->
                        <?php if ($bendahara): ?>
                        <div class="card border-0 shadow-sm text-center p-3 mb-3 mx-auto" style="max-width: 200px; border-radius: 10px; background: #ffffff; border-top: 4px solid var(--dark-coffee) !important;">
                            <div class="avatar mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background: rgba(62, 39, 35, 0.1); color: var(--dark-coffee);">
                                <i class="fa-solid fa-coins fs-5 text-gold"></i>
                            </div>
                            <div class="fw-bold text-dark text-truncate" title="<?= esc($bendahara['nama']) ?>"><?= esc($bendahara['nama']) ?></div>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis small mt-1"><?= esc($bendahara['jabatan']) ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Sub: Wakil Bendahara -->
                        <?php if (!empty($wkBendahara)): ?>
                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            <?php foreach ($wkBendahara as $wb): ?>
                            <div class="card border-0 shadow-sm text-center p-2" style="width: 145px; border-radius: 8px; background: #ffffff; border-top: 3px solid #6c757d !important;">
                                <div class="fw-semibold text-dark small text-truncate" title="<?= esc($wb['nama']) ?>"><?= esc($wb['nama']) ?></div>
                                <small class="text-muted" style="font-size: 0.72rem;"><?= esc($wb['jabatan']) ?></small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4" style="opacity: 0.15;">

        <!-- 10 Bidang Row -->
        <div class="text-center">
            <h6 class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing: 1px;">Ketua Bidang / Divisi Pelayanan</h6>
            <div class="d-flex justify-content-center flex-wrap gap-2">
                <?php foreach ($dpcBidang as $bid): ?>
                    <div class="card border-0 shadow-sm text-center p-2" style="width: 145px; border-radius: 8px; background: #ffffff; border-top: 3px solid #3b82f6 !important;">
                        <span class="badge bg-primary-subtle text-primary fw-bold mb-1" style="font-size: 0.7rem;"><?= esc($bid['bidang_kode']) ?></span>
                        <div class="fw-bold text-dark small text-truncate" title="<?= esc($bid['nama']) ?>"><?= esc($bid['nama']) ?></div>
                        <small class="text-muted" style="font-size: 0.7rem;"><?= esc($bid['jabatan']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filter & Search Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="<?= esc($activeTab) ?>">
            <div class="col-md-8">
                <label class="form-label small fw-semibold mb-1">Pencarian Pengurus</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama / Jabatan / Bidang" value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-gold px-4 fw-semibold w-50">Cari</button>
                <a href="<?= base_url('admin/dpc/struktur?tab=' . urlencode($activeTab)) ?>" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Daftar Pengurus -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #3e2723 !important; color: #ffffff !important;">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Nama Pengurus</th>
                        <th>Institusi</th>
                        <th>Jabatan</th>
                        <th>Bidang</th>
                        <th>No. Kontak / WA</th>
                        <th>Periode</th>
                        <th class="text-center pe-4" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pengurus)): ?>
                        <?php foreach ($pengurus as $idx => $p): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa-solid fa-user me-2 text-gold"></i><?= esc($p['nama']) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $p['institusi'] === 'DPC' ? 'bg-warning text-dark' : 'bg-info-subtle text-info-emphasis' ?> px-2 py-1">
                                        <?= esc($p['institusi']) ?>
                                    </span>
                                </td>
                                <td class="fw-medium text-dark"><?= esc($p['jabatan']) ?></td>
                                <td>
                                    <?php if (!empty($p['bidang_kode'])): ?>
                                        <span class="badge bg-light text-dark border"><?= esc($p['bidang_kode']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= esc($p['no_hp'] ?: '-') ?></td>
                                <td class="small text-muted"><?= esc($p['periode'] ?: '2025-2030') ?></td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <a href="<?= base_url('admin/dpc/struktur/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/dpc/struktur/delete/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus data pengurus ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Data pengurus tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
