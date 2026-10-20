<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-chart-pie me-2 text-gold"></i>Rekap Per RW
        </h4>
        <p class="text-muted mb-0 small">Rekapitulasi rinci jumlah kegiatan pada masing-masing RW di setiap Kelurahan/DPRa.</p>
    </div>
    <a href="<?= base_url('admin/kegiatan/rekap-rw/export?' . http_build_query(['from_date' => $fromDate, 'to_date' => $toDate, 'dpra' => $selectedDpra])) ?>" class="btn btn-success px-4 fw-semibold">
        <i class="fa-solid fa-file-excel me-2"></i>Excel Export
    </a>
</div>

<!-- Filter Form -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-3 col-6">
                <label class="form-label small fw-semibold mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="<?= esc($fromDate) ?>">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="<?= esc($toDate) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Lokasi Kegiatan (DPRa)</label>
                <select name="dpra" class="form-select form-select-sm">
                    <option value="">-- Semua DPRa --</option>
                    <?php foreach ($dpraList as $label => $cfg): ?>
                    <option value="<?= $cfg['name'] ?>" <?= $selectedDpra === $cfg['name'] ? 'selected' : '' ?>><?= $cfg['name'] ?> (<?= $cfg['kelurahan'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">GO</button>
                <a href="<?= base_url('admin/kegiatan/rekap-rw') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Rekap per RW -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4">Kelurahan (Lokasi Kegiatan)</th>
                        <th style="width: 200px;">RW</th>
                        <th style="width: 250px;">Jml Kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rekapRwRows)): ?>
                        <?php foreach ($rekapRwRows as $row): ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-dark-coffee"><?= esc($row['dpra']) ?></td>
                                <td><span class="badge bg-secondary px-3 py-1 fs-6">RW <?= $row['rw'] ?></span></td>
                                <td>
                                    <?php if ($row['jml_kegiatan'] > 0): ?>
                                        <span class="badge bg-success px-3 py-2 fs-6 fw-bold"><?= $row['jml_kegiatan'] ?> Kegiatan</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1">0 (Blankspot)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada data rekap per RW.</td>
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
