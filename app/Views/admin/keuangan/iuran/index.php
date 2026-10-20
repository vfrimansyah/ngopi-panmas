<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--dark-coffee)">
            <i class="fa-solid fa-hand-holding-dollar me-2 text-gold"></i>Status Iuran & Donasi Anggota
        </h4>
        <p class="text-muted mb-0 small">
            Pemantauan status pembayaran ITB, IWB (sesuai kemampuan), dan Infaq DPC (opsional) khusus <strong>Anggota Pelopor (Dewasa, Madya, Utama)</strong> bulan 
            <strong><?= $bulanNames[$bulan] ?> <?= $tahun ?></strong>.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/keuangan/iuran/export?' . http_build_query($_GET)) ?>" class="btn btn-sm btn-outline-success fw-semibold px-3">
            <i class="fa-solid fa-file-excel me-1"></i> Export Excel (CSV)
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-2-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #4f46e5 !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-indigo-subtle" style="width:42px;height:42px;background:#e0e7ff;">
                    <i class="fa-solid fa-users text-indigo" style="color:#4f46e5;"></i>
                </div>
                <div>
                    <div class="text-muted small">Anggota Pelopor</div>
                    <div class="fw-bold fs-5 text-dark"><?= number_format($summary['total_anggota']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #10b981 !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#d1fae5;">
                    <i class="fa-solid fa-check-double text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">Lunas ITB</div>
                    <div class="fw-bold fs-6 text-success">
                        <?= $summary['lunas_itb_count'] ?> Anggota
                    </div>
                    <div class="small text-muted">Rp <?= number_format($summary['total_itb_amount'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #06b6d4 !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#cffafe;">
                    <i class="fa-solid fa-hand-holding-hand text-info"></i>
                </div>
                <div>
                    <div class="text-muted small">Lunas IWB</div>
                    <div class="fw-bold fs-6 text-info">
                        <?= $summary['lunas_iwb_count'] ?> Anggota
                    </div>
                    <div class="small text-muted">Rp <?= number_format($summary['total_iwb_amount'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f59e0b !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#fef3c7;">
                    <i class="fa-solid fa-heart text-warning"></i>
                </div>
                <div>
                    <div class="text-muted small">Infaq DPC</div>
                    <div class="fw-bold fs-6 text-warning">
                        <?= $summary['lunas_infaq_count'] ?> Donatur
                    </div>
                    <div class="small text-muted">Rp <?= number_format($summary['total_infaq_amount'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #ef4444 !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#fee2e2;">
                    <i class="fa-solid fa-clock-rotate-left text-danger"></i>
                </div>
                <div>
                    <div class="text-muted small">Belum Bayar</div>
                    <div class="fw-bold fs-5 text-danger"><?= number_format($summary['belum_bayar_count']) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3 px-4">
        <form method="GET" action="<?= base_url('admin/keuangan/iuran') ?>" class="row g-2 align-items-end">
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <?php for ($y = date('Y') + 1; $y >= date('Y') - 3; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    <?php foreach ($bulanNames as $m => $name): ?>
                        <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Kode UPA</label>
                <select name="kode_upa" class="form-select form-select-sm">
                    <option value="">-- Semua UPA --</option>
                    <?php foreach ($upaList as $upa): ?>
                        <option value="<?= esc($upa['kode_upa']) ?>" <?= ($filters['kode_upa'] ?? '') === $upa['kode_upa'] ? 'selected' : '' ?>>
                            <?= esc($upa['kode_upa']) ?> - <?= esc($upa['nama_upa'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Jenjang</label>
                <select name="status_anggota" class="form-select form-select-sm">
                    <option value="">-- Semua Jenjang --</option>
                    <option value="Utama" <?= ($filters['status_anggota'] ?? '') === 'Utama' ? 'selected' : '' ?>>Utama</option>
                    <option value="Dewasa" <?= ($filters['status_anggota'] ?? '') === 'Dewasa' ? 'selected' : '' ?>>Dewasa</option>
                    <option value="Madya" <?= ($filters['status_anggota'] ?? '') === 'Madya' ? 'selected' : '' ?>>Madya</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Status Pembayaran</label>
                <select name="status_pembayaran" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="lunas_itb" <?= ($filters['status_pembayaran'] ?? '') === 'lunas_itb' ? 'selected' : '' ?>>ITB Lunas</option>
                    <option value="belum_itb" <?= ($filters['status_pembayaran'] ?? '') === 'belum_itb' ? 'selected' : '' ?>>ITB Belum</option>
                    <option value="lunas_iwb" <?= ($filters['status_pembayaran'] ?? '') === 'lunas_iwb' ? 'selected' : '' ?>>IWB Lunas</option>
                    <option value="belum_iwb" <?= ($filters['status_pembayaran'] ?? '') === 'belum_iwb' ? 'selected' : '' ?>>IWB Belum</option>
                    <option value="lunas_semua" <?= ($filters['status_pembayaran'] ?? '') === 'lunas_semua' ? 'selected' : '' ?>>Lunas Semua</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small fw-semibold mb-1">Cari Nama</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-12 text-end mt-2">
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fa-solid fa-filter me-1"></i> Terapkan Filter
                </button>
                <a href="<?= base_url('admin/keuangan/iuran') ?>" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Form Bulk Action & Data Table -->
<form method="POST" action="<?= base_url('admin/keuangan/iuran/bulk-update') ?>" id="bulkForm">
    <?= csrf_field() ?>
    <input type="hidden" name="tahun" value="<?= $tahun ?>">
    <input type="hidden" name="bulan" value="<?= $bulan ?>">

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <select name="bulk_action" class="form-select form-select-sm w-auto">
                    <option value="">-- Aksi Massal (Bulk Action) --</option>
                    <option value="itb_lunas">Tandai ITB Lunas</option>
                    <option value="iwb_lunas">Tandai IWB Lunas</option>
                    <option value="infaq_lunas">Tandai Infaq DPC Lunas</option>
                    <option value="semua_lunas">Tandai Lunas Semua (ITB+IWB+Infaq)</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold" onclick="return confirm('Apakah Anda yakin ingin memperbarui status anggota yang dipilih?')">
                    <i class="fa-solid fa-check me-1"></i> Terapkan
                </button>
            </div>
            <div class="text-muted small">
                Menampilkan <strong><?= count($report) ?></strong> Anggota
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:var(--dark-coffee);color:var(--cream);">
                        <tr>
                            <th class="ps-3 text-center" style="width:40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th style="width:50px;">No</th>
                            <th>Nama Anggota</th>
                            <th>UPA</th>
                            <th class="text-center">ITB</th>
                            <th class="text-center">IWB</th>
                            <th class="text-center">Infaq DPC</th>
                            <th class="text-end">Total Bayar</th>
                            <th class="text-center">Tgl Bayar</th>
                            <th class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">Tidak ada data anggota ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($report as $row): 
                                $totalBayar = (float)$row['nominal_itb'] + (float)$row['nominal_iwb'] + (float)$row['nominal_infaq'];
                            ?>
                                <tr>
                                    <td class="ps-3 text-center">
                                        <input type="checkbox" name="member_ids[]" value="<?= $row['member_id'] ?>" class="form-check-input member-checkbox">
                                    </td>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark mb-0"><?= esc($row['nama_lengkap']) ?></div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <?php if ($row['jenis_kelamin'] === 'L'): ?>
                                                <span class="badge bg-primary-subtle text-primary" style="font-size:0.68rem;"><i class="fa-solid fa-mars me-1"></i>L</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger" style="font-size:0.68rem;"><i class="fa-solid fa-venus me-1"></i>P</span>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($row['status_anggota'])): ?>
                                                <span class="badge bg-warning text-dark" style="font-size:0.68rem;"><?= esc($row['status_anggota']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary mb-1"><?= esc($row['kode_upa'] ?? '-') ?></span>
                                    </td>
                                    <!-- ITB -->
                                    <td class="text-center">
                                        <?php if ($row['status_itb'] === 'lunas'): ?>
                                            <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>LUNAS</span>
                                            <?php if ((float)$row['nominal_itb'] > 0): ?>
                                                <div class="small text-muted mt-1">Rp <?= number_format($row['nominal_itb'], 0, ',', '.') ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i>BELUM</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- IWB -->
                                    <td class="text-center">
                                        <?php if ($row['status_iwb'] === 'lunas'): ?>
                                            <span class="badge bg-info px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>LUNAS</span>
                                            <?php if ((float)$row['nominal_iwb'] > 0): ?>
                                                <div class="small text-muted mt-1">Rp <?= number_format($row['nominal_iwb'], 0, ',', '.') ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border px-2 py-1"><i class="fa-solid fa-minus me-1"></i>BELUM</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- Infaq DPC -->
                                    <td class="text-center">
                                        <?php if ($row['status_infaq'] === 'lunas' || (float)$row['nominal_infaq'] > 0): ?>
                                            <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-heart me-1"></i>ADA</span>
                                            <div class="small text-muted mt-1">Rp <?= number_format($row['nominal_infaq'], 0, ',', '.') ?></div>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border px-2 py-1">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- Total Bayar -->
                                    <td class="text-end fw-bold text-dark">
                                        Rp <?= number_format($totalBayar, 0, ',', '.') ?>
                                    </td>
                                    <!-- Tgl Bayar -->
                                    <td class="text-center small text-muted">
                                        <?= !empty($row['tanggal_bayar']) ? date('d/m/Y', strtotime($row['tanggal_bayar'])) : '-' ?>
                                    </td>
                                    <!-- Aksi -->
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-dark btn-edit-iuran"
                                            data-member-id="<?= $row['member_id'] ?>"
                                            data-nama="<?= esc($row['nama_lengkap']) ?>"
                                            data-status-itb="<?= $row['status_itb'] ?>"
                                            data-nominal-itb="<?= (float)$row['nominal_itb'] ?>"
                                            data-status-iwb="<?= $row['status_iwb'] ?>"
                                            data-nominal-iwb="<?= (float)$row['nominal_iwb'] ?>"
                                            data-status-infaq="<?= $row['status_infaq'] ?>"
                                            data-nominal-infaq="<?= (float)$row['nominal_infaq'] ?>"
                                            data-tanggal-bayar="<?= $row['tanggal_bayar'] ?? date('Y-m-d') ?>"
                                            data-catatan="<?= esc($row['catatan'] ?? '') ?>">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Status
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<!-- Modal Edit Status Pembayaran -->
<div class="modal fade" id="modalEditIuran" tabindex="-1" aria-labelledby="modalEditIuranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="<?= base_url('admin/keuangan/iuran/update') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                <input type="hidden" name="bulan" value="<?= $bulan ?>">
                <input type="hidden" name="member_id" id="edit_member_id">

                <div class="modal-header text-white" style="background:var(--dark-coffee);">
                    <h5 class="modal-title fw-bold" id="modalEditIuranLabel">
                        <i class="fa-solid fa-pen-to-square text-gold me-2"></i>Edit Status Iuran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded border bg-light">
                        <div class="small text-muted">Anggota:</div>
                        <div class="fw-bold fs-6 text-dark" id="edit_nama_anggota">-</div>
                        <div class="small text-muted mt-1">Periode: <strong><?= $bulanNames[$bulan] ?> <?= $tahun ?></strong></div>
                    </div>

                    <!-- ITB -->
                    <div class="card mb-3 border-light-subtle shadow-sm">
                        <div class="card-header bg-success-subtle py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success"><i class="fa-solid fa-shield-check me-1"></i>1. ITB (Iuran Terstruktur/Tetap)</span>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <label class="form-label small mb-1 fw-semibold">Status ITB</label>
                                    <select name="status_itb" id="edit_status_itb" class="form-select form-select-sm">
                                        <option value="belum">Belum Lunas</option>
                                        <option value="lunas">LUNAS</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small mb-1 fw-semibold">Nominal ITB (Rp)</label>
                                    <input type="number" step="500" name="nominal_itb" id="edit_nominal_itb" class="form-control form-control-sm" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- IWB -->
                    <div class="card mb-3 border-light-subtle shadow-sm">
                        <div class="card-header bg-info-subtle py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-info"><i class="fa-solid fa-hand-holding-heart me-1"></i>2. IWB (Sesuai Kemampuan)</span>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <label class="form-label small mb-1 fw-semibold">Status IWB</label>
                                    <select name="status_iwb" id="edit_status_iwb" class="form-select form-select-sm">
                                        <option value="belum">Belum Lunas</option>
                                        <option value="lunas">LUNAS</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small mb-1 fw-semibold">Nominal IWB (Rp)</label>
                                    <input type="number" step="500" name="nominal_iwb" id="edit_nominal_iwb" class="form-control form-control-sm" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Infaq DPC -->
                    <div class="card mb-3 border-light-subtle shadow-sm">
                        <div class="card-header bg-warning-subtle py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark"><i class="fa-solid fa-heart me-1 text-warning"></i>3. Infaq DPC (Opsional)</span>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <label class="form-label small mb-1 fw-semibold">Status Infaq</label>
                                    <select name="status_infaq" id="edit_status_infaq" class="form-select form-select-sm">
                                        <option value="belum">Belum/Tidak</option>
                                        <option value="lunas">Ada Infaq</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small mb-1 fw-semibold">Nominal Infaq (Rp)</label>
                                    <input type="number" step="1000" name="nominal_infaq" id="edit_nominal_infaq" class="form-control form-control-sm" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Bayar -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Pembayaran</label>
                        <input type="date" name="tanggal_bayar" id="edit_tanggal_bayar" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Catatan -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Catatan / Referensi</label>
                        <textarea name="catatan" id="edit_catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan transfer, kwitansi, atau keterangan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.col-md-2-4 {
    flex: 0 0 auto;
    width: 20%;
}
@media (max-width: 991px) {
    .col-md-2-4 {
        width: 50%;
    }
}
@media (max-width: 575px) {
    .col-md-2-4 {
        width: 100%;
    }
}
</style>

<script>
$(document).ready(function() {
    // Checkbox select all
    $('#selectAll').on('change', function() {
        $('.member-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Populate Modal
    $('.btn-edit-iuran').on('click', function() {
        var memberId = $(this).data('member-id');
        var nama = $(this).data('nama');
        var statusItb = $(this).data('status-itb');
        var nominalItb = $(this).data('nominal-itb');
        var statusIwb = $(this).data('status-iwb');
        var nominalIwb = $(this).data('nominal-iwb');
        var statusInfaq = $(this).data('status-infaq');
        var nominalInfaq = $(this).data('nominal-infaq');
        var tanggalBayar = $(this).data('tanggal-bayar');
        var catatan = $(this).data('catatan');

        $('#edit_member_id').val(memberId);
        $('#edit_nama_anggota').text(nama);
        $('#edit_status_itb').val(statusItb || 'belum');
        $('#edit_nominal_itb').val(nominalItb || 0);
        $('#edit_status_iwb').val(statusIwb || 'belum');
        $('#edit_nominal_iwb').val(nominalIwb || 0);
        $('#edit_status_infaq').val(statusInfaq || 'belum');
        $('#edit_nominal_infaq').val(nominalInfaq || 0);
        $('#edit_tanggal_bayar').val(tanggalBayar || '<?= date('Y-m-d') ?>');
        $('#edit_catatan').val(catatan || '');

        var modal = new bootstrap.Modal(document.getElementById('modalEditIuran'));
        modal.show();
    });
});
</script>

<?= $this->endSection() ?>
