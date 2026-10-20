<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/kegiatan/list') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)"><i class="fa-solid fa-clipboard-check me-2 text-gold"></i>Absensi Kegiatan</h4>
        <small class="text-muted">Kelola data kehadiran anggota pada kegiatan.</small>
    </div>
</div>

<!-- Kegiatan Info Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold text-dark-coffee mb-2"><?= esc($kegiatan['nama_kegiatan']) ?></h5>
                <div class="small text-muted">
                    <i class="fa-solid fa-tag me-1 text-gold"></i>Jenis: <strong><?= esc($jenisNama) ?></strong>
                </div>
                <div class="small text-muted mt-1">
                    <i class="fa-solid fa-calendar me-1 text-gold"></i>
                    <?= !empty($kegiatan['tanggal']) ? date('d M Y', strtotime($kegiatan['tanggal'])) : '-' ?>
                    &nbsp;|&nbsp;
                    <i class="fa-solid fa-clock me-1"></i>
                    <?= esc($kegiatan['jam_mulai'] ?? '-') ?> - <?= esc($kegiatan['jam_selesai'] ?? '-') ?>
                </div>
                <div class="small text-muted mt-1">
                    <i class="fa-solid fa-location-dot me-1 text-gold"></i>
                    <?= esc($kegiatan['lokasi_kegiatan'] ?? '-') ?>
                    <?= !empty($kegiatan['dpra']) ? ' — ' . esc($kegiatan['dpra']) : '' ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row text-center g-2 mt-2 mt-md-0">
                    <div class="col-3">
                        <div class="border rounded-3 py-2 px-1" style="background: #d4edda;">
                            <div class="fw-bold fs-5 text-success" id="countHadir"><?= $stats['hadir'] ?></div>
                            <div class="small text-muted">Hadir</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded-3 py-2 px-1" style="background: #fff3cd;">
                            <div class="fw-bold fs-5 text-warning" id="countIzin"><?= $stats['izin'] ?></div>
                            <div class="small text-muted">Izin</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded-3 py-2 px-1" style="background: #cfe2ff;">
                            <div class="fw-bold fs-5 text-primary" id="countSakit"><?= $stats['sakit'] ?></div>
                            <div class="small text-muted">Sakit</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded-3 py-2 px-1" style="background: #f8d7da;">
                            <div class="fw-bold fs-5 text-danger" id="countAlfa"><?= $stats['alfa'] ?></div>
                            <div class="small text-muted">Alfa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($can_update ?? false): ?>
<form method="POST" action="<?= base_url('admin/kegiatan/absensi/' . $kegiatan['id'] . '/save') ?>" id="formAbsensi">
    <?= csrf_field() ?>
<?php endif; ?>

<!-- Toolbar -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <?php if ($can_update ?? false): ?>
        <button type="button" class="btn btn-sm btn-success" id="btnSetAllHadir" title="Set semua hadir">
            <i class="fa-solid fa-check-double me-1"></i>Semua Hadir
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetAll" title="Reset semua ke alfa">
            <i class="fa-solid fa-rotate-left me-1"></i>Reset
        </button>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/kegiatan/absensi/' . $kegiatan['id'] . '/export') ?>" class="btn btn-sm btn-success px-3">
            <i class="fa-solid fa-file-excel me-1"></i>Export
        </a>
    </div>
</div>

<!-- Absensi Table -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableAbsensi">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Nama Anggota</th>
                        <th style="width: 100px;">UPA</th>
                        <th style="width: 100px;">JK</th>
                        <th class="text-center" style="width: 180px;">Status Kehadiran</th>
                        <th style="width: 200px;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($members as $m): ?>
                    <?php
                        $existing = $absensiMap[$m['id']] ?? null;
                        $curStatus = $existing['status'] ?? 'alfa';
                        $curCatatan = $existing['catatan'] ?? '';
                    ?>
                    <tr>
                        <td class="ps-4"><?= $no++ ?></td>
                        <td>
                            <div class="fw-semibold"><?= esc($m['nama_lengkap']) ?></div>
                        </td>
                        <td><span class="badge bg-secondary-subtle text-secondary"><?= esc($m['kode_upa'] ?? '-') ?></span></td>
                        <td>
                            <?php if (($m['jenis_kelamin'] ?? '') === 'L'): ?>
                                <span class="badge bg-primary-subtle text-primary">L</span>
                            <?php elseif (($m['jenis_kelamin'] ?? '') === 'P'): ?>
                                <span class="badge bg-danger-subtle text-danger">P</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($can_update ?? false): ?>
                            <input type="hidden" name="member_id[]" value="<?= $m['id'] ?>">
                            <select name="status[]" class="form-select form-select-sm status-select"
                                    data-member="<?= $m['id'] ?>" style="font-size: 0.8rem;">
                                <option value="alfa" <?= $curStatus === 'alfa' ? 'selected' : '' ?>>❌ Alfa</option>
                                <option value="hadir" <?= $curStatus === 'hadir' ? 'selected' : '' ?>>✅ Hadir</option>
                                <option value="izin" <?= $curStatus === 'izin' ? 'selected' : '' ?>>📋 Izin</option>
                                <option value="sakit" <?= $curStatus === 'sakit' ? 'selected' : '' ?>>🏥 Sakit</option>
                            </select>
                            <?php else: ?>
                            <?php
                                $statusBadge = match($curStatus) {
                                    'hadir' => '<span class="badge bg-success">✅ Hadir</span>',
                                    'izin'  => '<span class="badge bg-warning text-dark">📋 Izin</span>',
                                    'sakit' => '<span class="badge bg-primary">🏥 Sakit</span>',
                                    default => '<span class="badge bg-danger">❌ Alfa</span>',
                                };
                            ?>
                            <?= $statusBadge ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($can_update ?? false): ?>
                            <input type="text" name="catatan[]" class="form-control form-control-sm"
                                   placeholder="Opsional..." value="<?= esc($curCatatan) ?>" style="font-size: 0.8rem;">
                            <?php else: ?>
                            <span class="small text-muted"><?= esc($curCatatan) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($can_update ?? false): ?>
<!-- Save Button -->
<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;" id="btnSimpan">
        <i class="fa-solid fa-save me-2"></i>Simpan Absensi
    </button>
    <a href="<?= base_url('admin/kegiatan/list') ?>" class="btn btn-outline-secondary px-4">Batal</a>
</div>
</form>
<?php endif; ?>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-dark-coffee { color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
.status-select { min-width: 120px; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable with custom config
    if ($('#tableAbsensi').length && !$.fn.DataTable.isDataTable('#tableAbsensi')) {
        $('#tableAbsensi').DataTable({
            pageLength: 50,
            ordering: false,
            language: {
                search: "Cari Anggota:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ anggota",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 anggota",
                infoFiltered: "(disaring dari _MAX_ total anggota)",
                zeroRecords: "Tidak ditemukan anggota yang sesuai",
                emptyTable: "Tidak ada data anggota",
                paginate: { first: "Pertama", previous: "Sebelumnya", next: "Selanjutnya", last: "Terakhir" }
            }
        });
    }

    // Update counter stats live
    function updateCounters() {
        let h=0, i=0, s=0, a=0;
        $('.status-select').each(function() {
            switch($(this).val()) {
                case 'hadir': h++; break;
                case 'izin':  i++; break;
                case 'sakit': s++; break;
                default:      a++; break;
            }
        });
        $('#countHadir').text(h);
        $('#countIzin').text(i);
        $('#countSakit').text(s);
        $('#countAlfa').text(a);
    }

    $(document).on('change', '.status-select', function() {
        updateCounters();
    });

    // Set all to hadir
    $('#btnSetAllHadir').click(function() {
        $('.status-select').val('hadir');
        updateCounters();
    });

    // Reset all to alfa
    $('#btnResetAll').click(function() {
        Swal.fire({
            title: 'Reset Semua?',
            text: 'Semua status kehadiran akan direset ke Alfa.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('.status-select').val('alfa');
                $('input[name="catatan[]"]').val('');
                updateCounters();
            }
        });
    });

    // Confirm before save
    $('#formAbsensi').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        let hadir = 0;
        $('.status-select').each(function() {
            if ($(this).val() === 'hadir') hadir++;
        });
        Swal.fire({
            title: 'Simpan Absensi?',
            html: 'Total anggota hadir: <strong>' + hadir + '</strong> orang.<br>Lanjutkan menyimpan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3d1f0f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-save me-1"></i> Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
