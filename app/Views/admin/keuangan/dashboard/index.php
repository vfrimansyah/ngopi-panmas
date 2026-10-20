<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--dark-coffee)">
            <i class="fa-solid fa-coins me-2 text-gold"></i>Dashboard Keuangan
        </h4>
        <p class="text-muted mb-0 small">Ringkasan keuangan — <?= $bulanLabel ?></p>
    </div>
    <a href="<?= base_url('admin/keuangan/transaksi/create') ?>" class="btn btn-sm px-4 fw-semibold"
       style="background:#3d1f0f;color:#fff;border:none;">
        <i class="fa-solid fa-plus me-2"></i>Tambah Transaksi
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <?php
    $stats = [
        ['label'=>'Total Saldo',        'value'=>$totalSaldo,           'icon'=>'fa-wallet',        'color'=>'#6366f1','sub'=>'Semua rekening aktif'],
        ['label'=>'Pemasukan Bulan Ini','value'=>$pemasukanBulanIni,    'icon'=>'fa-arrow-trend-up','color'=>'#10b981','sub'=>$bulanLabel],
        ['label'=>'Pengeluaran Bln Ini','value'=>$pengeluaranBulanIni,  'icon'=>'fa-arrow-trend-down','color'=>'#ef4444','sub'=>$bulanLabel],
        ['label'=>'Laba / Rugi',        'value'=>$labaRugiBulanIni,     'icon'=>'fa-scale-balanced', 'color'=>$labaRugiBulanIni>=0?'#10b981':'#ef4444','sub'=>$bulanLabel],
    ];
    foreach ($stats as $s):
    ?>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid <?= $s['color'] ?> !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:48px;height:48px;background:<?= $s['color'] ?>22;">
                    <i class="fa-solid <?= $s['icon'] ?> fs-5" style="color:<?= $s['color'] ?>"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5" style="color:<?= $s['color'] ?>"><?= 'Rp ' . number_format($s['value'], 0, ',', '.') ?></div>
                    <div class="text-muted small"><?= $s['label'] ?></div>
                    <div class="text-muted" style="font-size:.65rem;"><?= $s['sub'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <!-- Grafik 6 Bulan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color:var(--gold);">
                    <i class="fa-solid fa-chart-bar me-2"></i>Arus Kas 6 Bulan Terakhir
                </h6>
            </div>
            <div class="card-body p-3">
                <canvas id="grafikKeuangan" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Saldo Rekening -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color:var(--gold);">
                    <i class="fa-solid fa-building-columns me-2"></i>Saldo Rekening
                </h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($rekenings as $r): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <div>
                            <div class="fw-semibold small"><?= esc($r['nama_rekening']) ?></div>
                            <small class="text-muted"><?= $r['tipe'] === 'bank' ? ($r['bank'] ?? 'Bank') : 'Kas' ?></small>
                        </div>
                        <span class="fw-bold <?= $r['saldo_berjalan'] >= 0 ? 'text-success' : 'text-danger' ?>"
                              style="font-size:.85rem;">
                            Rp <?= number_format($r['saldo_berjalan'], 0, ',', '.') ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item px-4 py-2 bg-light">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span style="color:#6366f1">Rp <?= number_format($totalSaldo, 0, ',', '.') ?></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Transaksi Terakhir -->
<div class="card border-0 shadow-sm">
    <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background:var(--dark-coffee);">
        <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-clock-rotate-left me-2"></i>Transaksi Terakhir</h6>
        <a href="<?= base_url('admin/keuangan/transaksi') ?>" class="btn btn-sm" style="background:#ffffff22;color:var(--gold);">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 no-datatable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nomor</th><th>Tanggal</th><th>Keterangan</th>
                        <th>Rekening</th><th class="text-end pe-4">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksiTerakhir as $t): ?>
                    <tr>
                        <td class="ps-4">
                            <a href="<?= base_url('admin/keuangan/transaksi/' . $t['id']) ?>" class="fw-semibold text-decoration-none" style="color:var(--dark-coffee)">
                                <?= esc($t['nomor_transaksi']) ?>
                            </a>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                        <td class="small"><?= esc(substr($t['deskripsi'] ?? '-', 0, 50)) ?></td>
                        <td class="small text-muted"><?= esc($t['nama_rekening'] ?? '-') ?></td>
                        <td class="text-end pe-4 fw-semibold <?= $t['tipe']==='pemasukan'?'text-success':'text-danger' ?>">
                            <?= $t['tipe']==='pemasukan'?'+':'-' ?> Rp <?= number_format($t['jumlah'],0,',','.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transaksiTerakhir)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4"><i class="fa-solid fa-inbox me-2"></i>Belum ada transaksi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const grafik = <?= json_encode($grafik) ?>;
new Chart(document.getElementById('grafikKeuangan'), {
    type: 'bar',
    data: {
        labels: grafik.map(g => g.label),
        datasets: [
            { label: 'Pemasukan', data: grafik.map(g => g.pemasukan),   backgroundColor: '#10b98155', borderColor: '#10b981', borderWidth: 2, borderRadius: 6 },
            { label: 'Pengeluaran', data: grafik.map(g => g.pengeluaran), backgroundColor: '#ef444455', borderColor: '#ef4444', borderWidth: 2, borderRadius: 6 },
        ]
    },
    options: {
        responsive: true, plugins: { legend: { position: 'top' } },
        scales: { y: { ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'K' } } }
    }
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
