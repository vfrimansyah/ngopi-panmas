<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--dark-coffee)"><i class="fa-solid fa-book-open me-2 text-gold"></i>Jurnal Umum</h4>
        <p class="text-muted mb-0 small">Catatan double-entry dari setiap transaksi.</p>
    </div>
</div>

<!-- Filter Bulan/Tahun -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $filter['bulan'] == $m ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $filter['tahun'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-filter me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Total Debit / Kredit / Selisih -->
<?php $balance = $totalDebit - $totalKredit; ?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #10b981 !important;">
            <div class="card-body py-2 px-4">
                <div class="text-muted small">Total Debit</div>
                <div class="fw-bold text-success">Rp <?= number_format($totalDebit, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #ef4444 !important;">
            <div class="card-body py-2 px-4">
                <div class="text-muted small">Total Kredit</div>
                <div class="fw-bold text-danger">Rp <?= number_format($totalKredit, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid <?= abs($balance) < 0.01 ? '#10b981' : '#f97316' ?> !important;">
            <div class="card-body py-2 px-4">
                <div class="text-muted small">Selisih</div>
                <div class="fw-bold <?= abs($balance) < 0.01 ? 'text-success' : 'text-danger' ?>">
                    <?php if (abs($balance) < 0.01): ?>
                        <i class="fa-solid fa-circle-check me-1"></i>Seimbang
                    <?php else: ?>
                        Rp <?= number_format(abs($balance), 0, ',', '.') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Jurnal — no-datatable karena sudah ada filter GET -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 no-datatable">
                <thead style="background:var(--dark-coffee);color:var(--cream);">
                    <tr>
                        <th class="ps-4">No. Jurnal</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-end">Total Debit</th>
                        <th class="text-end">Total Kredit</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jurnals as $j):
                        // Data totals sudah disiapkan controller — tidak perlu query di view
                        $tot = $jurnalTotals[$j['id']] ?? ['d' => 0, 'k' => 0];
                    ?>
                    <tr>
                        <td class="ps-4 font-monospace fw-semibold" style="color:var(--dark-coffee)">
                            <?= esc($j['nomor_jurnal']) ?>
                        </td>
                        <td class="small"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                        <td class="small"><?= esc(substr($j['deskripsi'] ?? '—', 0, 60)) ?></td>
                        <td class="text-end text-success fw-semibold small">
                            Rp <?= number_format($tot['d'], 0, ',', '.') ?>
                        </td>
                        <td class="text-end text-danger fw-semibold small">
                            Rp <?= number_format($tot['k'], 0, ',', '.') ?>
                        </td>
                        <td class="text-center pe-4">
                            <a href="<?= base_url('admin/keuangan/jurnal/' . $j['id']) ?>" class="btn btn-sm btn-outline-info" title="Detail Jurnal">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($jurnals)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-book-open fa-2x mb-2 d-block opacity-25"></i>
                            Belum ada jurnal di periode ini
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
