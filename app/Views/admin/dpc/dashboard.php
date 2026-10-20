<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-gauge-high me-2 text-gold"></i>Dashboard & Monev DPC PKS
        </h4>
        <p class="text-muted mb-0 small">Monitoring capaian program kerja, anggaran RAB, dan evaluasi berkala DPC Panmas.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/dpc/monev') ?>" class="btn btn-sm btn-gold px-3 fw-semibold">
            <i class="fa-solid fa-list-check me-2"></i>Buka Tools Monev
        </a>
    </div>
</div>

<!-- 4 Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #3d1f0f, #63402d); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Program Kerja</div>
                <div class="display-6 fw-bold my-1"><?= $totalProker ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-layer-group me-1"></i> Tersebar di 10 Bidang DPC</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Rata-rata Capaian</div>
                <div class="display-6 fw-bold my-1"><?= $avgProgres ?>%</div>
                <div class="small text-white-50"><i class="fa-solid fa-chart-line me-1"></i> Progres keseluruhan proker</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">🟢 Program Berjalan / Selesai</div>
                <div class="display-6 fw-bold my-1"><?= $prokerBerjalan + $prokerSelesai ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-circle-check me-1"></i> <?= $prokerSelesai ?> selesai, <?= $prokerBerjalan ?> berjalan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #d97706, #b45309); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Realisasi RAB Program</div>
                <div class="h3 fw-bold my-1">Rp <?= number_format($totalRealisasi, 0, ',', '.') ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-coins me-1"></i> <?= $persenAnggaran ?>% dari Rp <?= number_format($totalAnggaran, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-chart-column me-2 text-gold"></i>Rata-Rata Capaian Progres (%) per Bidang DPC</h6>
            </div>
            <div class="card-body">
                <canvas id="chartBidang" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-chart-pie me-2 text-gold"></i>Distribusi Status Monev</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <canvas id="chartStatus" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 10 Bidang Quick Cards -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header text-white py-3 border-0 d-flex justify-content-between align-items-center" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
        <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-sitemap me-2 text-gold"></i>10 Bidang DPC PKS Panmas & Capaian Monev</h6>
        <a href="<?= base_url('admin/dpc/proker') ?>" class="btn btn-sm btn-outline-light px-3">Lihat Matriks Proker</a>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            <?php foreach ($bidangs as $b): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border p-3" style="border-radius: 10px; background: #fafafa;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge px-2 py-1 fw-bold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                                    <?= esc($b['kode']) ?>
                                </span>
                                <h6 class="fw-bold text-dark mt-2 mb-1"><?= esc($b['nama_bidang']) ?></h6>
                                <small class="text-muted"><i class="fa-solid fa-user-tie me-1"></i> PIC: <?= esc($b['pic_nama'] ?: '-') ?></small>
                            </div>
                        </div>
                        <p class="small text-muted mb-2 text-truncate" title="<?= esc($b['fokus']) ?>">
                            <strong>Fokus:</strong> <?= esc($b['fokus'] ?: '-') ?>
                        </p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span>Progres Monev (<?= $b['total_proker'] ?> proker)</span>
                                <span class="<?= $b['avg_progres'] >= 70 ? 'text-success' : ($b['avg_progres'] >= 30 ? 'text-primary' : 'text-secondary') ?>">
                                    <?= $b['avg_progres'] ?>%
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar <?= $b['avg_progres'] >= 70 ? 'bg-success' : ($b['avg_progres'] >= 30 ? 'bg-primary' : 'bg-secondary') ?>" 
                                     role="progressbar" style="width: <?= $b['avg_progres'] ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Program Prioritas & Recent Evaluasi -->
<div class="row g-4 mb-4">
    <!-- Prioritas -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header text-white py-3 border-0 d-flex justify-content-between align-items-center" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-star me-2 text-gold"></i>Program Prioritas Pembinaan & Rekrutmen</h6>
                <a href="<?= base_url('admin/dpc/proker?mode=prioritas') ?>" class="btn btn-sm btn-outline-light">Detail</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Program</th>
                                <th>Bidang</th>
                                <th>Frekuensi / Waktu</th>
                                <th class="text-center">Progres</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($prioritasList)): ?>
                                <?php foreach ($prioritasList as $pr): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-dark"><?= esc($pr['nama_program']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= esc($pr['bidang_kode']) ?></span></td>
                                        <td class="small text-muted"><?= esc($pr['waktu_frekuensi'] ?: '-') ?></td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 14px; width: 70px; margin: auto;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pr['persentase_progres'] ?>%">
                                                    <small style="font-size: 0.65rem;"><?= $pr['persentase_progres'] ?>%</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $st = $pr['status_monev'];
                                            if ($st === 'selesai') echo '<span class="badge bg-success">Selesai</span>';
                                            elseif ($st === 'berjalan') echo '<span class="badge bg-primary">Berjalan</span>';
                                            elseif ($st === 'terkendala') echo '<span class="badge bg-danger">Terkendala</span>';
                                            else echo '<span class="badge bg-secondary">Belum</span>';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada program prioritas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header text-white py-3 border-0 d-flex justify-content-between align-items-center" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-clock-rotate-left me-2 text-gold"></i>Log Evaluasi Monev Terkini</h6>
                <a href="<?= base_url('admin/dpc/monev') ?>" class="btn btn-sm btn-outline-light">Semua Log</a>
            </div>
            <div class="card-body p-3">
                <?php if (!empty($recentLogs)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold small text-dark"><?= esc($log['nama_program'] ?: 'Program #' . $log['proker_id']) ?></span>
                                    <span class="badge bg-success-subtle text-success small"><?= $log['persentase_progres'] ?>%</span>
                                </div>
                                <div class="small text-muted mb-1"><?= esc($log['capaian_kpi'] ?: '-') ?></div>
                                <div class="d-flex justify-content-between small text-muted" style="font-size: 0.75rem;">
                                    <span><i class="fa-regular fa-calendar me-1"></i><?= esc($log['periode_evaluasi']) ?></span>
                                    <span>Oleh: <?= esc($log['evaluator_nama'] ?: 'Admin') ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted py-4">Belum ada catatan evaluasi monev.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Bar Bidang
new Chart(document.getElementById('chartBidang'), {
    type: 'bar',
    data: {
        labels: <?= $bidangLabels ?>,
        datasets: [{
            label: 'Capaian Progres (%)',
            data: <?= $bidangProgres ?>,
            backgroundColor: '#3d1f0f',
            borderColor: '#d4af37',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }
        }
    }
});

// Chart Doughnut Status
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: <?= $statusLabels ?>,
        datasets: [{
            data: <?= $statusValues ?>,
            backgroundColor: <?= $statusColors ?>,
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
<?= $this->endSection() ?>
