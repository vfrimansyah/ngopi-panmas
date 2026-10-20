<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-list-check me-2 text-gold"></i>Tools Monitoring & Evaluasi (Monev)
        </h4>
        <p class="text-muted mb-0 small">Alat pencatatan progres, evaluasi berkala (After Action Review), kendala lapangan, dan tindak lanjut program.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/dpc/monev/export-excel?' . http_build_query($filters)) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-excel me-1"></i>Export Laporan Monev
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Bidang DPC</label>
                <select name="bidang" class="form-select form-select-sm">
                    <option value="">-- Semua Bidang --</option>
                    <?php foreach ($bidangs as $b): ?>
                    <option value="<?= esc($b['kode']) ?>" <?= ($filters['bidang_kode'] ?? '') === $b['kode'] ? 'selected' : '' ?>>
                        <?= esc($b['kode']) ?> – <?= esc($b['nama_bidang']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status Capaian</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="belum" <?= ($filters['status_monev'] ?? '') === 'belum' ? 'selected' : '' ?>>Belum Mulai</option>
                    <option value="berjalan" <?= ($filters['status_monev'] ?? '') === 'berjalan' ? 'selected' : '' ?>>Sedang Berjalan</option>
                    <option value="selesai" <?= ($filters['status_monev'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="terkendala" <?= ($filters['status_monev'] ?? '') === 'terkendala' ? 'selected' : '' ?>>Terkendala</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Cari Program / PIC</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama program / KPI / PIC" value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">Filter</button>
                <a href="<?= base_url('admin/dpc/monev') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Monev Table with Quick Input Modal Trigger -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header text-white py-3 border-0 d-flex justify-content-between align-items-center" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
        <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-clipboard-check me-2 text-gold"></i>Matriks Evaluasi Kinerja Program Kerja</h6>
        <small class="text-white-50">Klik tombol "Input Monev" untuk memperbarui progres & catatan evaluasi</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Bidang</th>
                        <th>Program Kerja</th>
                        <th>PIC</th>
                        <th>Target KPI</th>
                        <th>Realisasi Terkini</th>
                        <th class="text-center" style="width: 140px;">Progres (%)</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center pe-4" style="width: 150px;">Tools Monev</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prokers)): ?>
                        <?php foreach ($prokers as $idx => $p): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                <td>
                                    <span class="badge px-2 py-1 fw-bold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                                        <?= esc($p['bidang_kode']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <?= esc($p['nama_program']) ?>
                                    <?php if ($p['is_prioritas']): ?>
                                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">★ Prioritas</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark" style="font-size: 0.92rem;"><?= esc($p['pic_nama'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <div class="text-dark" style="font-size: 0.92rem; line-height: 1.6;"><?= nl2br(esc($p['kpi_target'] ?: '-')) ?></div>
                                </td>
                                <td>
                                    <div class="text-secondary fw-medium" style="font-size: 0.92rem;"><?= esc($p['realisasi_capaian'] ?: '-') ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <div class="progress flex-grow-1" style="height: 14px;">
                                            <div class="progress-bar <?= $p['persentase_progres'] >= 100 ? 'bg-success' : ($p['persentase_progres'] >= 40 ? 'bg-primary' : 'bg-warning') ?>" 
                                                 role="progressbar" style="width: <?= $p['persentase_progres'] ?>%"></div>
                                        </div>
                                        <span class="fw-bold small" style="width: 35px;"><?= $p['persentase_progres'] ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $st = $p['status_monev'];
                                    if ($st === 'selesai') echo '<span class="badge bg-success">Selesai</span>';
                                    elseif ($st === 'berjalan') echo '<span class="badge bg-primary">Berjalan</span>';
                                    elseif ($st === 'terkendala') echo '<span class="badge bg-danger">Terkendala</span>';
                                    else echo '<span class="badge bg-secondary">Belum</span>';
                                    ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-gold fw-semibold" 
                                                data-bs-toggle="modal" data-bs-target="#modalMonev<?= $p['id'] ?>" title="Input Evaluasi & Progres">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Monev
                                        </button>
                                        <a href="<?= base_url('admin/dpc/monev/log/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Lihat Riwayat Log">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                        </a>
                                    </div>

                                    <!-- Modal Input Monev per Proker -->
                                    <div class="modal fade text-start" id="modalMonev<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header text-white" style="background: var(--dark-coffee);">
                                                    <h6 class="modal-title fw-bold" style="color: var(--cream);">
                                                        <i class="fa-solid fa-list-check me-2 text-gold"></i>Input Evaluasi Monev: <?= esc($p['nama_program']) ?>
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="<?= base_url('admin/dpc/monev/save-log') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="proker_id" value="<?= $p['id'] ?>">

                                                    <div class="modal-body p-4">
                                                        <div class="alert alert-light border mb-3">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <small class="text-muted d-block">Bidang & PIC:</small>
                                                                    <strong><?= esc($p['bidang_kode']) ?> – <?= esc($p['pic_nama'] ?: '-') ?></strong>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <small class="text-muted d-block">Target KPI:</small>
                                                                    <span class="small"><?= esc($p['kpi_target'] ?: '-') ?></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Periode Evaluasi <span class="text-danger">*</span></label>
                                                                <select name="periode_evaluasi" class="form-select" required>
                                                                    <option value="Bulanan - <?= date('F Y') ?>">Bulanan (<?= date('F Y') ?>)</option>
                                                                    <option value="Triwulan 1 <?= date('Y') ?>">Triwulan 1 (Jan–Mar <?= date('Y') ?>)</option>
                                                                    <option value="Triwulan 2 <?= date('Y') ?>">Triwulan 2 (Apr–Jun <?= date('Y') ?>)</option>
                                                                    <option value="Triwulan 3 <?= date('Y') ?>">Triwulan 3 (Jul–Sep <?= date('Y') ?>)</option>
                                                                    <option value="Triwulan 4 <?= date('Y') ?>">Triwulan 4 (Okt–Des <?= date('Y') ?>)</option>
                                                                    <option value="Semester 1 <?= date('Y') ?>">Semester 1 <?= date('Y') ?></option>
                                                                    <option value="Semester 2 <?= date('Y') ?>">Semester 2 <?= date('Y') ?></option>
                                                                    <option value="Pasca Kegiatan (AAR)">Pasca Kegiatan (After Action Review)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Tanggal Evaluasi <span class="text-danger">*</span></label>
                                                                <input type="date" name="tanggal_evaluasi" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Persentase Progres Capaian (%) <span class="text-danger">*</span></label>
                                                                <input type="number" name="persentase_progres" class="form-control" min="0" max="100" value="<?= $p['persentase_progres'] ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Status Program</label>
                                                                <select name="status_monev" class="form-select">
                                                                    <option value="berjalan" <?= $p['status_monev'] === 'berjalan' ? 'selected' : '' ?>>🟢 Sedang Berjalan</option>
                                                                    <option value="selesai" <?= $p['status_monev'] === 'selesai' ? 'selected' : '' ?>>✅ Selesai 100%</option>
                                                                    <option value="terkendala" <?= $p['status_monev'] === 'terkendala' ? 'selected' : '' ?>>🔴 Terkendala / Perlu Bantuan</option>
                                                                    <option value="belum" <?= $p['status_monev'] === 'belum' ? 'selected' : '' ?>>⚪ Belum Mulai</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Capaian KPI Riil / Hasil yang Dicapai</label>
                                                            <textarea name="capaian_kpi" class="form-control" rows="2" placeholder="Uraikan realisasi capaian peserta, frekuensi, atau output program..."><?= esc($p['realisasi_capaian']) ?></textarea>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Kendala / Hambatan Lapangan</label>
                                                                <textarea name="kendala" class="form-control" rows="2" placeholder="Tuliskan tantangan atau masalah yang dihadapi..."></textarea>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Rekomendasi / Tindak Lanjut</label>
                                                                <textarea name="tindak_lanjut" class="form-control" rows="2" placeholder="Langkah perbaikan atau follow-up selanjutnya..."></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label class="form-label fw-semibold">Nama Evaluator / PIC Pelapor</label>
                                                            <input type="text" name="evaluator_nama" class="form-control" value="<?= esc(auth()->user()->username ?? 'Pengurus DPC') ?>" placeholder="Nama penilai/pelapor">
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        <button type="submit" class="btn btn-gold fw-semibold px-4">
                                                            <i class="fa-solid fa-save me-1"></i>Simpan Catatan Monev
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Modal -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Data program kerja tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Riwayat Log Monev Terkini -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
        <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-history me-2 text-gold"></i>Riwayat Catatan Evaluasi & After Action Review (AAR)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Program Kerja</th>
                        <th>Periode</th>
                        <th>Tgl Evaluasi</th>
                        <th class="text-center">Progres</th>
                        <th>Capaian KPI</th>
                        <th>Kendala & Solusi</th>
                        <th>Evaluator</th>
                        <th class="text-center pe-4" style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $idx => $l): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                <td class="fw-semibold text-dark">
                                    <span class="badge bg-light text-dark border me-1"><?= esc($l['bidang_kode']) ?></span>
                                    <?= esc($l['nama_program']) ?>
                                </td>
                                <td class="small text-muted"><?= esc($l['periode_evaluasi']) ?></td>
                                <td class="small text-muted"><?= date('d/m/Y', strtotime($l['tanggal_evaluasi'])) ?></td>
                                <td class="text-center"><span class="badge bg-success-subtle text-success fw-bold"><?= $l['persentase_progres'] ?>%</span></td>
                                <td class="small text-dark"><?= esc($l['capaian_kpi'] ?: '-') ?></td>
                                <td class="small text-muted">
                                    <?php if (!empty($l['kendala'])): ?>
                                        <div class="text-danger mb-1"><strong>Kendala:</strong> <?= esc($l['kendala']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($l['tindak_lanjut'])): ?>
                                        <div class="text-primary"><strong>Tindak Lanjut:</strong> <?= esc($l['tindak_lanjut']) ?></div>
                                    <?php endif; ?>
                                    <?php if (empty($l['kendala']) && empty($l['tindak_lanjut'])) echo '-'; ?>
                                </td>
                                <td class="small text-muted"><?= esc($l['evaluator_nama'] ?: '-') ?></td>
                                <td class="text-center pe-4">
                                    <?php if ($can_delete ?? false): ?>
                                    <a href="<?= base_url('admin/dpc/monev/delete-log/' . $l['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus Log"
                                       onclick="return confirm('Hapus catatan evaluasi ini?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada riwayat catatan evaluasi monev.</td></tr>
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
