<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/keuangan/jurnal') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--dark-coffee)"><?= $title ?></h4>
        <p class="text-muted mb-0 small"><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3" style="background:var(--dark-coffee);">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-book-open me-2"></i>Entri Jurnal</h6>
                    <span class="badge text-bg-light font-monospace"><?= esc($jurnal['nomor_jurnal']) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0 no-datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:40%">Akun</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end pe-4">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jurnal['details'] as $d): $isDebit = $d['debit'] > 0; ?>
                        <tr>
                            <td class="ps-4 <?= $isDebit?'':'ps-5' ?>">
                                <div class="fw-semibold small" style="<?= $isDebit?'':'padding-left:20px' ?>">
                                    <?= esc($d['kode_akun'] ?? '—') ?> — <?= esc($d['nama_akun'] ?? '—') ?>
                                </div>
                                <?php if ($d['deskripsi']): ?>
                                <small class="text-muted"><?= esc($d['deskripsi']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-semibold <?= $isDebit?'text-success':'' ?>">
                                <?= $isDebit ? 'Rp '.number_format($d['debit'],0,',','.') : '—' ?>
                            </td>
                            <td class="text-end pe-4 fw-semibold <?= !$isDebit?'text-danger':'' ?>">
                                <?= !$isDebit ? 'Rp '.number_format($d['kredit'],0,',','.') : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <?php
                        $totD = array_sum(array_column($jurnal['details'],'debit'));
                        $totK = array_sum(array_column($jurnal['details'],'kredit'));
                        ?>
                        <tr class="fw-bold">
                            <td class="ps-4">TOTAL</td>
                            <td class="text-end text-success">Rp <?= number_format($totD,0,',','.') ?></td>
                            <td class="text-end pe-4 text-danger">Rp <?= number_format($totK,0,',','.') ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="ps-4 py-2">
                                <?php if (abs($totD-$totK)<0.01): ?>
                                <span class="text-success small"><i class="fa-solid fa-circle-check me-1"></i>Jurnal seimbang</span>
                                <?php else: ?>
                                <span class="text-danger small"><i class="fa-solid fa-triangle-exclamation me-1"></i>Jurnal tidak seimbang!</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer bg-light py-2 px-4">
                <small class="text-muted"><?= esc($jurnal['deskripsi'] ?? '') ?></small>
            </div>
        </div>
        <?php if ($jurnal['transaksi_id']): ?>
        <div class="mt-3 text-center">
            <a href="<?= base_url('admin/keuangan/transaksi/' . $jurnal['transaksi_id']) ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-receipt me-1"></i>Lihat Transaksi Sumber
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
