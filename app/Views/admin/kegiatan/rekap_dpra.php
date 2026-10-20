<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-chart-column me-2 text-gold"></i>Rekap Per DPRa
        </h4>
        <p class="text-muted mb-0 small">Rekapitulasi sebaran dan intensitas kegiatan per DPRa serta analisis Blankspot RW.</p>
    </div>
    <a href="<?= base_url('admin/kegiatan/rekap-dpra/export?' . http_build_query(['from_date' => $fromDate, 'to_date' => $toDate])) ?>" class="btn btn-success px-4 fw-semibold">
        <i class="fa-solid fa-file-excel me-2"></i>Excel Export
    </a>
</div>

<!-- Metric Card (PDF Page 4 Top Visual) -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white" style="background: #0000ff; border-radius: 12px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2">Total RW : <?= $grandTotalRw ?></h5>
                <div class="fs-6">Jumlah Kegiatan : <?= $grandTotalKegiatan ?></div>
                <div class="fs-6">Rata2 Per RW : <?= $rataRataTotal ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: #f8fafc;">
            <div class="card-body p-3">
                <form method="GET" action="" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold mb-1">Dari Tanggal</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" value="<?= esc($fromDate) ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" value="<?= esc($toDate) ?>">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Table Rekap per DPRa -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th class="text-start">DPRa</th>
                        <th>Jml RW</th>
                        <th>Jml Kegiatan</th>
                        <th>Rata2/RW</th>
                        <th class="bg-danger text-white">Blankspot</th>
                        <th class="bg-danger text-white">% Blankspot</th>
                        <th class="bg-warning text-dark">Minim (1-5)</th>
                        <th class="bg-warning text-dark">% Minim</th>
                        <th class="bg-success text-white">Di atas 5</th>
                        <th class="bg-success text-white">% Di atas 5</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rekapRows)): ?>
                        <?php foreach ($rekapRows as $idx => $row): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $idx + 1 ?></td>
                                <td class="text-start fw-bold text-dark-coffee"><?= esc($row['dpra']) ?></td>
                                <td class="fw-semibold"><?= $row['jml_rw'] ?></td>
                                <td class="fw-bold text-primary"><?= $row['jml_kegiatan'] ?></td>
                                <td><span class="badge bg-secondary"><?= $row['rata_per_rw'] ?></span></td>
                                <td class="fw-bold text-danger"><?= $row['blankspot'] ?></td>
                                <td class="text-danger fw-semibold"><?= $row['pct_blankspot'] ?>%</td>
                                <td class="fw-bold text-warning"><?= $row['minim'] ?></td>
                                <td class="text-warning fw-semibold"><?= $row['pct_minim'] ?>%</td>
                                <td class="fw-bold text-success"><?= $row['di_atas_5'] ?></td>
                                <td class="text-success fw-semibold"><?= $row['pct_di_atas_5'] ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">Belum ada data rekap kegiatan DPRa.</td>
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
</style>

<?= $this->endSection() ?>
