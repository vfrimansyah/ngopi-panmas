<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-clock-rotate-left me-2 text-gold"></i>Riwayat Evaluasi Monev
        </h4>
        <p class="text-muted mb-0 small"><?= esc($proker['nama_program']) ?> (Bidang: <?= esc($proker['bidang_kode']) ?>)</p>
    </div>
    <a href="<?= base_url('admin/dpc/monev') ?>" class="btn btn-sm btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Monev
    </a>
</div>

<!-- Proker Summary Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Bidang & PIC</small>
                <div class="fw-bold text-dark"><?= esc($proker['bidang_kode']) ?> – <?= esc($proker['pic_nama'] ?: '-') ?></div>
            </div>
            <div class="col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Waktu / Frekuensi</small>
                <div class="fw-bold text-dark"><?= esc($proker['waktu_frekuensi'] ?: '-') ?></div>
            </div>
            <div class="col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Status Capaian</small>
                <div>
                    <?php
                    $st = $proker['status_monev'];
                    if ($st === 'selesai') echo '<span class="badge bg-success">Selesai (100%)</span>';
                    elseif ($st === 'berjalan') echo '<span class="badge bg-primary">Sedang Berjalan</span>';
                    elseif ($st === 'terkendala') echo '<span class="badge bg-danger">Terkendala</span>';
                    else echo '<span class="badge bg-secondary">Belum Mulai</span>';
                    ?>
                    <span class="fw-bold ms-1"><?= $proker['persentase_progres'] ?>%</span>
                </div>
            </div>
            <div class="col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Target KPI</small>
                <div class="small text-dark"><?= esc($proker['kpi_target'] ?: '-') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Evaluasi Logs -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
        <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-list-check me-2 text-gold"></i>Timeline Riwayat Monev & After Action Review</h6>
    </div>
    <div class="card-body p-4">
        <?php if (!empty($logs)): ?>
            <div class="timeline">
                <?php foreach ($logs as $l): ?>
                    <div class="border-start border-3 border-warning ps-3 mb-4 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-warning text-dark fw-bold"><?= esc($l['periode_evaluasi']) ?></span>
                            <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i><?= date('d M Y', strtotime($l['tanggal_evaluasi'])) ?> | Oleh: <?= esc($l['evaluator_nama'] ?: 'Admin') ?></small>
                        </div>
                        <div class="fw-bold text-success mb-2">Progres Capaian: <?= $l['persentase_progres'] ?>%</div>
                        <div class="p-3 bg-light rounded-2 mb-2">
                            <strong>Hasil / Capaian KPI:</strong>
                            <p class="mb-0 small text-dark"><?= nl2br(esc($l['capaian_kpi'] ?: '-')) ?></p>
                        </div>
                        <?php if (!empty($l['kendala'])): ?>
                            <div class="p-3 bg-danger-subtle rounded-2 mb-2">
                                <strong class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Kendala / Hambatan:</strong>
                                <p class="mb-0 small text-danger-emphasis"><?= nl2br(esc($l['kendala'])) ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($l['tindak_lanjut'])): ?>
                            <div class="p-3 bg-info-subtle rounded-2 mb-2">
                                <strong class="text-primary"><i class="fa-solid fa-lightbulb me-1"></i>Rekomendasi Tindak Lanjut:</strong>
                                <p class="mb-0 small text-primary-emphasis"><?= nl2br(esc($l['tindak_lanjut'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted py-5">Belum ada riwayat catatan evaluasi untuk program ini.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
