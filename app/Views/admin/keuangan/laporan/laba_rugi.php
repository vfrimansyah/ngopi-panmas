<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--dark-coffee)"><i class="fa-solid fa-file-invoice me-2 text-gold"></i>Laporan Laba Rugi</h4>
        <p class="text-muted mb-0 small">Periode: <?= date('d/m/Y', strtotime($dari)) ?> — <?= date('d/m/Y', strtotime($sampai)) ?></p>
    </div>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-print me-1"></i>Cetak
    </button>
</div>

<!-- Filter Periode -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2 px-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">Dari</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="<?= $dari ?>">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="<?= $sampai ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter me-1"></i>Tampilkan</button>
            </div>
            <div class="col-auto ms-auto">
                <!-- Shortcut period -->
                <div class="btn-group btn-group-sm">
                    <?php
                    $shortcuts = [
                        'Bulan Ini'  => [date('Y-m-01'), date('Y-m-t')],
                        'Tahun Ini'  => [date('Y-01-01'), date('Y-12-31')],
                        'Bln Lalu'   => [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))],
                    ];
                    foreach ($shortcuts as $label => [$d, $s]):
                    ?>
                    <a href="?dari=<?= $d ?>&sampai=<?= $s ?>" class="btn btn-outline-secondary <?= ($dari===$d && $sampai===$s)?'active':'' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #10b981 !important;">
            <div class="card-body text-center py-4">
                <div class="text-muted small mb-1">Total Pendapatan</div>
                <div class="fw-bold fs-4 text-success">Rp <?= number_format($totalPendapatan,0,',','.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #ef4444 !important;">
            <div class="card-body text-center py-4">
                <div class="text-muted small mb-1">Total Beban</div>
                <div class="fw-bold fs-4 text-danger">Rp <?= number_format($totalBeban,0,',','.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid <?= $labaRugi>=0?'#6366f1':'#f97316' ?> !important;">
            <div class="card-body text-center py-4">
                <div class="text-muted small mb-1"><?= $labaRugi>=0?'Laba Bersih':'Rugi Bersih' ?></div>
                <div class="fw-bold fs-4" style="color:<?= $labaRugi>=0?'#6366f1':'#f97316' ?>">
                    <?= $labaRugi<0?'(':'', 'Rp '.number_format(abs($labaRugi),0,',','.'), $labaRugi<0?')':'' ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Laporan Detail -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" id="printArea">
            <!-- Header cetak -->
            <div class="card-header py-3 text-center d-none d-print-block">
                <h5>LAPORAN LABA RUGI</h5>
                <p class="mb-0">Periode: <?= date('d F Y', strtotime($dari)) ?> s/d <?= date('d F Y', strtotime($sampai)) ?></p>
            </div>

            <div class="card-body p-0">
                <!-- Pendapatan -->
                <table class="table mb-0 no-datatable">
                    <thead style="background:#10b98115;">
                        <tr>
                            <th class="ps-4 text-success fw-bold">PENDAPATAN</th>
                            <th class="text-end pe-4 text-success fw-bold">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendapatanRows as $r): ?>
                        <tr>
                            <td class="ps-4 small">
                                <span class="font-monospace text-muted me-2"><?= esc($r['kode_akun']) ?></span>
                                <?= esc($r['nama_akun']) ?>
                            </td>
                            <td class="text-end pe-4 text-success small">Rp <?= number_format($r['saldo'],0,',','.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pendapatanRows)): ?>
                        <tr><td colspan="2" class="ps-4 text-muted small fst-italic">Belum ada pendapatan di periode ini</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td class="ps-4">Total Pendapatan</td>
                            <td class="text-end pe-4 text-success">Rp <?= number_format($totalPendapatan,0,',','.') ?></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Beban -->
                <table class="table mb-0 no-datatable">
                    <thead style="background:#ef444415;">
                        <tr>
                            <th class="ps-4 text-danger fw-bold">BEBAN</th>
                            <th class="text-end pe-4 text-danger fw-bold">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bebanRows as $r): ?>
                        <tr>
                            <td class="ps-4 small">
                                <span class="font-monospace text-muted me-2"><?= esc($r['kode_akun']) ?></span>
                                <?= esc($r['nama_akun']) ?>
                            </td>
                            <td class="text-end pe-4 text-danger small">Rp <?= number_format($r['saldo'],0,',','.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($bebanRows)): ?>
                        <tr><td colspan="2" class="ps-4 text-muted small fst-italic">Belum ada beban di periode ini</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td class="ps-4">Total Beban</td>
                            <td class="text-end pe-4 text-danger">Rp <?= number_format($totalBeban,0,',','.') ?></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Laba / Rugi -->
                <div class="p-4" style="background:<?= $labaRugi>=0?'#6366f115':'#f9731615' ?>;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0" style="color:<?= $labaRugi>=0?'#6366f1':'#f97316' ?>">
                            <?= $labaRugi>=0?'LABA BERSIH':'RUGI BERSIH' ?>
                        </h5>
                        <h4 class="fw-bold mb-0" style="color:<?= $labaRugi>=0?'#6366f1':'#f97316' ?>">
                            <?= $labaRugi<0?'(' : '' ?>Rp <?= number_format(abs($labaRugi),0,',','.') ?><?= $labaRugi<0?')':'' ?>
                        </h4>
                    </div>
                    <small class="text-muted">Periode: <?= date('d F Y', strtotime($dari)) ?> s/d <?= date('d F Y', strtotime($sampai)) ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    #sidebar-wrapper, nav.navbar, .btn, form, .card-header:not(.d-print-block) { display: none !important; }
    .d-print-block { display: block !important; }
    #page-content-wrapper { margin: 0 !important; }
}
</style>
<?= $this->endSection() ?>
