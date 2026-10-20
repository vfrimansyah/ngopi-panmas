<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-warehouse me-2 text-gold"></i>Dashboard Aset & Inventaris
        </h4>
        <p class="text-muted mb-0 small">Ringkasan statistik aset organisasi secara real-time.</p>
    </div>
</div>

<!-- 4 Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #3d1f0f, #63402d); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Jenis Barang</div>
                <div class="display-6 fw-bold my-1"><?= $totalJenis ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-boxes-stacked me-1"></i> Jenis item berbeda</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #6366f1, #3b82f6); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Unit Barang</div>
                <div class="display-6 fw-bold my-1"><?= $totalUnit ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-cubes me-1"></i> Jumlah seluruh unit</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">🟢 Kondisi Baik</div>
                <div class="display-6 fw-bold my-1"><?= $totalBaik ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-circle-check me-1"></i> Unit dalam kondisi baik</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">🔴 Rusak / Kurang Baik</div>
                <div class="display-6 fw-bold my-1"><?= $totalRusak ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-circle-xmark me-1"></i> Perlu penggantian</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-chart-bar me-2 text-gold"></i>Distribusi Aset per Lokasi</h6>
            </div>
            <div class="card-body">
                <canvas id="chartLokasi" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-chart-pie me-2 text-gold"></i>Distribusi Kondisi Aset</h6>
            </div>
            <div class="card-body">
                <canvas id="chartKondisi" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Assets Table -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
        <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-clock-rotate-left me-2 text-gold"></i>Aset Terakhir Diperbarui</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Lokasi</th>
                        <th>Penanggung Jawab</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentAssets)): ?>
                        <?php foreach ($recentAssets as $idx => $item): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                <td><span class="badge px-2 py-1 fw-semibold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;"><?= esc($item['kode_aset']) ?></span></td>
                                <td class="fw-semibold text-dark">
                                    <?= esc($item['nama_barang']) ?>
                                    <?php if (!empty($item['merk']) || !empty($item['model'])): ?>
                                        <small class="text-muted d-block fw-normal"><?= esc(trim(($item['merk'] ?? '') . ' ' . ($item['model'] ?? ''))) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= esc($item['lokasi'] ?: '-') ?></td>
                                <td class="small text-muted"><?= esc($item['penanggung_jawab'] ?: '-') ?></td>
                                <td class="text-center fw-bold"><?= $item['jumlah'] ?></td>
                                <td class="text-center">
                                    <?php
                                    $k = $item['kondisi'];
                                    if ($k === 'baik') echo '<span class="badge bg-success-subtle text-success px-2 py-1">🟢 Baik</span>';
                                    elseif ($k === 'kurang_baik') echo '<span class="badge bg-warning text-dark px-2 py-1">🟡 Kurang Baik</span>';
                                    elseif ($k === 'rusak') echo '<span class="badge bg-danger text-white px-2 py-1">🔴 Rusak</span>';
                                    else echo '<span class="badge bg-secondary text-white px-2 py-1">⚫ Hilang</span>';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data aset.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Lokasi
new Chart(document.getElementById('chartLokasi'), {
    type: 'bar',
    data: {
        labels: <?= $lokasiLabels ?>,
        datasets: [{
            label: 'Jumlah Unit',
            data: <?= $lokasiValues ?>,
            backgroundColor: ['#3d1f0f', '#d4af37', '#6366f1', '#10b981', '#ef4444', '#f59e0b'],
            borderRadius: 6,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Chart Kondisi
new Chart(document.getElementById('chartKondisi'), {
    type: 'doughnut',
    data: {
        labels: <?= $kondisiLabels ?>,
        datasets: [{
            data: <?= $kondisiValues ?>,
            backgroundColor: <?= $kondisiColors ?>,
            borderWidth: 2,
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
<?= $this->endSection() ?>
