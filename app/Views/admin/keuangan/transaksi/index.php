<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--dark-coffee)"><i class="fa-solid fa-right-left me-2 text-gold"></i>Transaksi Keuangan</h4>
        <p class="text-muted mb-0 small">Daftar seluruh transaksi pemasukan dan pengeluaran.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/keuangan/transaksi/create?tipe=pemasukan') ?>" class="btn btn-sm btn-success fw-semibold px-3">
            <i class="fa-solid fa-arrow-down me-1"></i>Pemasukan
        </a>
        <a href="<?= base_url('admin/keuangan/transaksi/create?tipe=pengeluaran') ?>" class="btn btn-sm btn-danger fw-semibold px-3">
            <i class="fa-solid fa-arrow-up me-1"></i>Pengeluaran
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0"><i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #10b981 !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#10b98122;"><i class="fa-solid fa-arrow-down text-success"></i></div>
                <div><div class="text-muted small">Total Pemasukan</div><div class="fw-bold text-success">Rp <?= number_format($totalMasuk, 0, ',', '.') ?></div></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #ef4444 !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#ef444422;"><i class="fa-solid fa-arrow-up text-danger"></i></div>
                <div><div class="text-muted small">Total Pengeluaran</div><div class="fw-bold text-danger">Rp <?= number_format($totalKeluar, 0, ',', '.') ?></div></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?php $selisih = $totalMasuk - $totalKeluar; ?>
        <div class="card border-0 shadow-sm" style="border-left:4px solid <?= $selisih>=0?'#6366f1':'#f97316' ?> !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:<?= $selisih>=0?'#6366f1':'#f97316' ?>22;"><i class="fa-solid fa-scale-balanced" style="color:<?= $selisih>=0?'#6366f1':'#f97316' ?>"></i></div>
                <div><div class="text-muted small">Selisih</div><div class="fw-bold" style="color:<?= $selisih>=0?'#6366f1':'#f97316' ?>">Rp <?= number_format(abs($selisih), 0, ',', '.') ?></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">Tipe</label>
                <select name="tipe" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    <option value="pemasukan"  <?= ($filter['tipe']??'')==='pemasukan'  ?'selected':'' ?>>Pemasukan</option>
                    <option value="pengeluaran"<?= ($filter['tipe']??'')==='pengeluaran'?'selected':'' ?>>Pengeluaran</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Rekening</label>
                <select name="rekening_id" class="form-select form-select-sm">
                    <option value="">Semua Rekening</option>
                    <?php if (!empty($rekenings)): ?>
                        <?php foreach ($rekenings as $rek): ?>
                        <option value="<?= $rek['id'] ?>" <?= ($filter['rekening_id']??'') == $rek['id'] ? 'selected':'' ?>><?= esc($rek['nama_rekening']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Dari</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="<?= $filter['dari'] ?? '' ?>">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="<?= $filter['sampai'] ?? '' ?>">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    <?php for ($y = date('Y'); $y >= 2024; $y--): ?>
                    <option value="<?= $y ?>" <?= ($filter['tahun']??'') == $y ? 'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                <a href="<?= base_url('admin/keuangan/transaksi') ?>" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="transaksiTable">
                <thead style="background:var(--dark-coffee);color:var(--cream);">
                    <tr>
                        <th class="ps-4">No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th>Rekening</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksis as $t): ?>
                    <tr>
                        <td class="ps-4">
                            <a href="<?= base_url('admin/keuangan/transaksi/' . $t['id']) ?>" class="fw-semibold text-decoration-none font-monospace" style="color:var(--dark-coffee)">
                                <?= esc($t['nomor_transaksi']) ?>
                            </a>
                        </td>
                        <td class="small"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                        <td>
                            <?php if ($t['tipe']==='pemasukan'): ?>
                            <span class="badge bg-success"><i class="fa-solid fa-arrow-down me-1"></i>Pemasukan</span>
                            <?php else: ?>
                            <span class="badge bg-danger"><i class="fa-solid fa-arrow-up me-1"></i>Pengeluaran</span>
                            <?php endif; ?>
                        </td>
                        <td class="small">
                            <div><?= esc(substr($t['deskripsi'] ?? '—', 0, 50)) ?></div>
                            <?php if ($t['kategori']): ?><small class="text-muted"><?= esc($t['kategori']) ?></small><?php endif; ?>
                        </td>
                        <td class="small text-muted"><?= esc($t['nama_rekening'] ?? '—') ?></td>
                        <td class="text-end fw-bold <?= $t['tipe']==='pemasukan'?'text-success':'text-danger' ?>">
                            <?= $t['tipe']==='pemasukan'?'+':'-' ?> Rp <?= number_format($t['jumlah'],0,',','.') ?>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="<?= base_url('admin/keuangan/transaksi/'.$t['id']) ?>" class="btn btn-sm btn-outline-info" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                <?php if ($can_update ?? false): ?>
                                <a href="<?= base_url('admin/keuangan/transaksi/edit/'.$t['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                <?php endif; ?>
                                <?php if ($can_delete ?? false): ?>
                                <a href="<?= base_url('admin/keuangan/transaksi/delete/'.$t['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus transaksi ini? Jurnal terkait juga akan dihapus.')"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transaksis)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-25"></i>Belum ada transaksi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
