<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-gauge me-2 text-gold"></i>Dashboard Kegiatan
        </h4>
        <p class="text-muted mb-0 small">Ringkasan statistik dan grafik kegiatan organisasi per DPRa, Sie DPC, serta Jenis Kegiatan.</p>
    </div>
</div>

<!-- Top Section: 4 Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #3d1f0f, #63402d); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Kegiatan</div>
                <div class="display-6 fw-bold my-1"><?= $totalKegiatan ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-calendar-days me-1"></i> Seluruh Kegiatan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">🟢 Wilayah Hijau</div>
                <div class="display-6 fw-bold my-1"><?= $totalHijau ?? 0 ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-circle-check me-1"></i> Kondisi Aman</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">🟡 Wilayah Kuning</div>
                <div class="display-6 fw-bold my-1"><?= $totalKuning ?? 0 ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-triangle-exclamation me-1"></i> Perlu Perhatian</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">🔴 Wilayah Merah</div>
                <div class="display-6 fw-bold my-1"><?= $totalMerah ?? 0 ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-circle-xmark me-1"></i> Perlu Tindakan</div>
            </div>
        </div>
    </div>
</div>

<!-- Input Kegiatan Terakhir Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--cream);"><i class="fa-solid fa-clock-rotate-left me-2 text-gold"></i>Input Kegiatan Terakhir</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>Penyelenggara Kegiatan</th>
                                <th>Input Kegiatan Terakhir</th>
                                <th>Nama Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentInput)): ?>
                                <?php foreach ($recentInput as $idx => $item): ?>
                                    <tr>
                                        <td class="ps-3 text-muted"><?= $idx + 1 ?></td>
                                        <td><span class="badge bg-primary-subtle text-primary fw-semibold"><?= esc($item['penyelenggara'] ?: '-') ?></span></td>
                                        <td class="small text-muted"><?= !empty($item['updated_at']) ? date('d-M-Y', strtotime($item['updated_at'])) : '-' ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($item['nama_kegiatan']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada input kegiatan terbaru.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1: DPRa & Lokasi -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-chart-bar me-2 text-warning"></i>GRAFIK KEGIATAN DPRa</h6>
            </div>
            <div class="card-body">
                <canvas id="chartDpra" height="220"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-location-dot me-2 text-danger"></i>GRAFIK LOKASI KEGIATAN</h6>
            </div>
            <div class="card-body">
                <canvas id="chartLokasi" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2: Sie DPC -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-sitemap me-2 text-info"></i>GRAFIK KEGIATAN Sie DPC</h6>
            </div>
            <div class="card-body">
                <canvas id="chartSie" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 3: Jenis Kegiatan -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-list me-2 text-success"></i>GRAFIK JENIS KEGIATAN</h6>
            </div>
            <div class="card-body">
                <canvas id="chartJenis" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // 1. Chart DPRa
    const dpraData = <?= json_encode($dpraStats) ?>;
    new Chart(document.getElementById('chartDpra'), {
        type: 'bar',
        data: {
            labels: Object.keys(dpraData),
            datasets: [{
                label: 'Jumlah Kegiatan',
                data: Object.values(dpraData),
                backgroundColor: ['#f97316', '#3b82f6', '#10b981', '#a855f7', '#ec4899', '#eab308'],
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // 2. Chart Lokasi
    const lokasiData = <?= json_encode($lokasiStats) ?>;
    new Chart(document.getElementById('chartLokasi'), {
        type: 'bar',
        data: {
            labels: Object.keys(lokasiData),
            datasets: [{
                label: 'Jumlah Lokasi Kegiatan',
                data: Object.values(lokasiData),
                backgroundColor: '#3b82f6',
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // 3. Chart Sie DPC
    const sieData = <?= json_encode($sieStats) ?>;
    new Chart(document.getElementById('chartSie'), {
        type: 'bar',
        data: {
            labels: Object.keys(sieData),
            datasets: [{
                label: 'Jumlah Kegiatan',
                data: Object.values(sieData),
                backgroundColor: '#d946ef',
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // 4. Chart Jenis Kegiatan (Horizontal Bar)
    const jenisData = <?= json_encode($jenisStats) ?>;
    new Chart(document.getElementById('chartJenis'), {
        type: 'bar',
        data: {
            labels: Object.keys(jenisData),
            datasets: [{
                label: 'Jumlah Kegiatan',
                data: Object.values(jenisData),
                backgroundColor: '#10b981',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
});
</script>
<?= $this->endSection() ?>
