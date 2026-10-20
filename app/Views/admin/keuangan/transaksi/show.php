<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<?php $isMasuk = $transaksi['tipe'] === 'pemasukan'; ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/keuangan/transaksi') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--dark-coffee)">
            <i class="fa-solid fa-file-invoice me-2 text-gold"></i><?= $title ?>
        </h4>
        <p class="text-muted mb-0 small">Detail transaksi dan jurnal terkait.</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="<?= base_url('admin/keuangan/transaksi/edit/' . $transaksi['id']) ?>" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-pen me-1"></i>Edit
        </a>
        <a href="<?= base_url('admin/keuangan/transaksi/delete/' . $transaksi['id']) ?>" class="btn btn-sm btn-outline-danger"
           onclick="return confirm('Hapus transaksi ini?')">
            <i class="fa-solid fa-trash me-1"></i>Hapus
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Detail Transaksi -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-left:4px solid <?= $isMasuk?'#10b981':'#ef4444' ?> !important;">
            <div class="card-header py-3" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-receipt me-2"></i>Detail Transaksi</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <div class="text-muted small">Nomor Transaksi</div>
                        <div class="fw-bold fs-5 font-monospace"><?= esc($transaksi['nomor_transaksi']) ?></div>
                    </div>
                    <span class="badge bg-<?= $isMasuk?'success':'danger' ?> fs-7 px-3 py-2">
                        <i class="fa-solid fa-arrow-<?= $isMasuk?'down':'up' ?> me-1"></i><?= $isMasuk?'Pemasukan':'Pengeluaran' ?>
                    </span>
                </div>

                <?php
                $rows = [
                    ['Tanggal',   date('d F Y', strtotime($transaksi['tanggal']))],
                    ['Jumlah',    '<span class="fw-bold fs-5 text-' . ($isMasuk?'success':'danger') . '">Rp ' . number_format($transaksi['jumlah'],0,',','.') . '</span>'],
                    ['Rekening',  esc($transaksi['nama_rekening'] ?? '—')],
                    ['Kategori',  esc($transaksi['kategori'] ?? '—')],
                    ['Keterangan',esc($transaksi['deskripsi'] ?? '—')],
                    ['Status',    '<span class="badge bg-' . ($transaksi['status']==='posted'?'success':'warning text-dark') . '">' . ucfirst($transaksi['status']) . '</span>'],
                ];
                foreach ($rows as [$l,$v]):
                ?>
                <div class="row mb-2 py-2 border-bottom">
                    <div class="col-4 text-muted small"><?= $l ?></div>
                    <div class="col-8 fw-semibold small"><?= $v ?></div>
                </div>
                <?php endforeach; ?>

                <?php if ($transaksi['bukti_file']): ?>
                <div class="mt-3">
                    <div class="text-muted small mb-2">Bukti Transaksi</div>
                    <?php
                    $ext = strtolower(pathinfo($transaksi['bukti_file'], PATHINFO_EXTENSION));
                    $url = base_url('uploads/keuangan/' . $transaksi['bukti_file']);
                    ?>
                    <?php if (in_array($ext, ['jpg','jpeg','png','webp'])): ?>
                    <img src="<?= $url ?>" class="img-fluid rounded shadow-sm" style="max-height:250px;">
                    <?php else: ?>
                    <a href="<?= $url ?>" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="fa-solid fa-file-pdf me-1"></i>Buka Bukti
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Jurnal Umum -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-book-open me-2"></i>Jurnal Terkait</h6>
            </div>
            <?php if ($jurnal): ?>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="fw-bold font-monospace small"><?= esc($jurnal['nomor_jurnal']) ?></div>
                        <div class="text-muted small"><?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?></div>
                    </div>
                    <a href="<?= base_url('admin/keuangan/jurnal/' . $jurnal['id']) ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                </div>
                <table class="table table-sm mb-0 no-datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Akun</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jurnal['details'] as $d): ?>
                        <tr>
                            <td class="small">
                                <div><?= esc($d['kode_akun'] ?? '—') ?></div>
                                <small class="text-muted"><?= esc($d['nama_akun'] ?? '—') ?></small>
                            </td>
                            <td class="text-end small <?= $d['debit']>0?'fw-bold text-success':'' ?>">
                                <?= $d['debit']>0 ? 'Rp '.number_format($d['debit'],0,',','.') : '—' ?>
                            </td>
                            <td class="text-end small <?= $d['kredit']>0?'fw-bold text-danger':'' ?>">
                                <?= $d['kredit']>0 ? 'Rp '.number_format($d['kredit'],0,',','.') : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card-body text-center text-muted py-4">
                <i class="fa-solid fa-book-open fa-2x mb-2 opacity-25 d-block"></i>Jurnal belum dibuat
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
