<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- ===== FLOATING BULK ACTION BAR ===== -->
<?php if ($can_create ?? false): ?>
<div id="bulkActionBar" style="display:none; position:sticky; top:70px; z-index:100;
     background: linear-gradient(135deg, #2c1810, #4a2c1e);
     border-left: 4px solid #c8a97e; border-radius: 0.75rem;
     padding: 0.75rem 1.25rem; margin-bottom: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark fw-bold fs-6" id="selectedCount">0</span>
            <span style="color:#f5f0e8;" class="fw-semibold">anggota dipilih</span>
        </div>
        <form id="bulkPromoteForm" action="<?= base_url('admin/members/bulk-promote') ?>" method="POST" class="d-inline">
            <?= csrf_field() ?>
            <div id="hiddenCheckboxContainer"></div>
            <button type="submit" class="btn btn-warning fw-bold px-4"
                    id="bulkPromoteBtn">
                <i class="fa-solid fa-user-plus me-2"></i>Buat Akun Portal (Massal)
            </button>
        </form>
        <button class="btn btn-sm btn-outline-light ms-auto" id="clearSelectionBtn">
            <i class="fa-solid fa-xmark me-1"></i>Batal Pilih
        </button>
    </div>
</div>
<?php endif; ?>

<!-- ===== NAV TABS / PILLS ===== -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?= base_url('admin/members') ?>" class="btn btn-sm fw-semibold px-3 active shadow-sm" style="border-radius:20px; background:var(--dark-coffee); color:#fff; border:none;">
            <i class="fa-solid fa-users me-1 text-gold"></i>Daftar Anggota
        </a>
        <a href="<?= base_url('admin/members/ekonomi') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-sack-dollar me-1 text-gold"></i>Data Ekonomi Anggota
        </a>
        <a href="<?= base_url('admin/members/pendidikan') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-graduation-cap me-1 text-gold"></i>Pendidikan Anggota
        </a>
        <a href="<?= base_url('admin/members/dashboard') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-chart-pie me-1"></i>Statistik & Dashboard
        </a>
        <a href="<?= base_url('admin/upa') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-people-group me-1 text-gold"></i>Group UPA
        </a>
    </div>
</div>

<!-- ===== HEADER + FILTER ===== -->
<div class="card shadow-sm border-0 mb-4" style="background:#efebe9;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 style="font-family:'Playfair Display',serif; color:#3e2723;" class="mb-0">
                <i class="fa-solid fa-users me-3" style="color:#d4af37;"></i>Kelola Anggota Komunitas
            </h3>
            <?php if ($can_create ?? false): ?>
            <a href="<?= base_url('admin/members/create') ?>" class="btn btn-gold fw-semibold">
                <i class="fa-solid fa-plus me-2"></i>Tambah Anggota
            </a>
            <?php endif; ?>
        </div>
        <form action="" method="GET" class="row g-2 align-items-center">
            <div class="col-xl-3 col-lg-3 col-md-6">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama lengkap..." value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-xl-2 col-lg-2 col-md-3">
                <select name="status_anggota" class="form-select">
                    <option value="pelopor" <?= ($selected_status ?? '') === 'pelopor' ? 'selected' : '' ?>>Pelopor (Madya/Dewasa/Utama)</option>
                    <option value="all" <?= ($selected_status ?? '') === 'all' ? 'selected' : '' ?>>Semua Jenjang</option>
                    <option value="Utama"  <?= ($selected_status ?? '') === 'Utama'  ? 'selected' : '' ?>>Utama</option>
                    <option value="Dewasa" <?= ($selected_status ?? '') === 'Dewasa' ? 'selected' : '' ?>>Dewasa</option>
                    <option value="Madya"  <?= ($selected_status ?? '') === 'Madya'  ? 'selected' : '' ?>>Madya</option>
                    <option value="Muayyid" <?= ($selected_status ?? '') === 'Muayyid' ? 'selected' : '' ?>>Muayyid</option>
                    <option value="Muhib"   <?= ($selected_status ?? '') === 'Muhib'   ? 'selected' : '' ?>>Muhib</option>
                    <option value="Belum Tarbiyah" <?= ($selected_status ?? '') === 'Belum Tarbiyah' ? 'selected' : '' ?>>Belum Tarbiyah</option>
                    <option value="non_pelopor" <?= ($selected_status ?? '') === 'non_pelopor' ? 'selected' : '' ?>>Non-Pelopor</option>
                </select>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-3">
                <select name="gender" class="form-select">
                    <option value="">-- Semua Gender --</option>
                    <option value="L" <?= ($selected_gender ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki (Ikhwan)</option>
                    <option value="P" <?= ($selected_gender ?? '') === 'P' ? 'selected' : '' ?>>Perempuan (Akhwat)</option>
                </select>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-3">
                <select name="kode_upa" class="form-select">
                    <option value="">-- Semua UPA --</option>
                    <?php foreach ($regions as $r): ?>
                        <option value="<?= $r['kode_upa'] ?>"
                                <?= ($selected_upa ?? '') === $r['kode_upa'] ? 'selected' : '' ?>>
                            <?= $r['kode_upa'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-3">
                <select name="account_filter" class="form-select">
                    <option value="">-- Status Akun --</option>
                    <option value="no_account" <?= ($account_filter ?? '') === 'no_account' ? 'selected' : '' ?>>
                        Belum Punya Akun
                    </option>
                    <option value="invited"    <?= ($account_filter ?? '') === 'invited'    ? 'selected' : '' ?>>
                        Sudah Diundang
                    </option>
                    <option value="active"     <?= ($account_filter ?? '') === 'active'     ? 'selected' : '' ?>>
                        Aktif
                    </option>
                </select>
            </div>
            <div class="col-xl-1 col-lg-1 col-md-6 d-flex gap-1">
                <button type="submit" class="btn fw-semibold btn-gold flex-fill px-2" title="Terapkan Filter">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
                <a href="<?= base_url('admin/members') ?>" class="btn btn-outline-secondary px-2" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ===== STATS ROW ===== -->
<?php
    $totalCount    = count($members);
    $noAcctCount   = count(array_filter($members, fn($m) => $m['account_status'] === 'no_account'));
    $invitedCount  = count(array_filter($members, fn($m) => $m['account_status'] === 'invited'));
    $activeCount   = count(array_filter($members, fn($m) => $m['account_status'] === 'active'));
?>
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4" style="color:#3e2723;"><?= $totalCount ?></div>
            <small class="text-muted">Total Ditampilkan</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-top:3px solid #6b7280!important;">
            <div class="fw-bold fs-4 text-secondary"><?= $noAcctCount ?></div>
            <small class="text-muted">Belum Punya Akun</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-top:3px solid #f59e0b!important;">
            <div class="fw-bold fs-4 text-warning"><?= $invitedCount ?></div>
            <small class="text-muted">Diundang</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-top:3px solid #22c55e!important;">
            <div class="fw-bold fs-4 text-success"><?= $activeCount ?></div>
            <small class="text-muted">Aktif</small>
        </div>
    </div>
</div>

<!-- ===== TABLE CARD ===== -->
<div class="card border-0 shadow-sm">
    <!-- Bulk select controls -->
    <div class="px-4 py-2 border-bottom bg-light d-flex align-items-center gap-3 flex-wrap">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="selectAllNoAcct">
            <label class="form-check-label small fw-semibold" for="selectAllNoAcct">
                Pilih Semua Anggota Tanpa Akun
            </label>
        </div>
        <span class="text-muted small">|</span>
        <button class="btn btn-sm btn-outline-secondary" type="button" id="selectAllBtn">Pilih Semua</button>
        <button class="btn btn-sm btn-outline-secondary" type="button" id="deselectAllBtn">Batal Pilih</button>
        <?php if ($noAcctCount > 0): ?>
        <span class="ms-auto badge rounded-pill"
              style="background:#3e272320; color:#3e2723; border:1px solid #d4af37;">
            <i class="fa-solid fa-circle-info me-1" style="color:#d4af37;"></i>
            <?= $noAcctCount ?> anggota belum punya akun (Wajib memiliki Email untuk dipromote)
        </span>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0 no-datatable" id="membersTable">
            <thead style="background:#3e2723; color:#f5f0e8;">
                <tr>
                    <th class="ps-3" style="width:44px;"></th>
                    <th>Nama Lengkap</th>
                    <th>Jenjang</th>
                    <th>UPA</th>
                    <th>Alamat</th>
                    <th>Pekerjaan</th>
                    <th class="text-center" style="width: 140px; min-width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($members)): ?>
                    <?php foreach ($members as $member): ?>
                        <?php 
                            $canPromote = empty($member['user_id']); 
                            $hasEmail = !empty($member['email']) && filter_var($member['email'], FILTER_VALIDATE_EMAIL);
                        ?>
                        <tr class="<?= $canPromote ? 'can-promote-row' : '' ?>">
                            <!-- Checkbox col -->
                            <td class="ps-3">
                                <?php if (($can_create ?? false) && $canPromote): ?>
                                    <input class="form-check-input member-cb" type="checkbox"
                                           value="<?= $member['id'] ?>"
                                           data-name="<?= esc($member['nama_lengkap']) ?>"
                                           data-has-email="<?= $hasEmail ? '1' : '0' ?>"
                                           title="Pilih <?= esc($member['nama_lengkap']) ?>">
                                <?php endif; ?>
                            </td>
                            <!-- Nama -->
                            <td>
                                <strong><?= esc($member['nama_lengkap']) ?></strong>
                                <div class="text-muted small">
                                    <?= $member['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                    &middot; 
                                    <?php if ($member['email']): ?>
                                        <span class="text-dark fw-medium text-email-val"><?= esc($member['email']) ?></span>
                                    <?php else: ?>
                                        <span class="text-danger fst-italic text-email-val">(Email belum diisi)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <!-- Jenjang -->
                            <td>
                                <?php if (!empty($member['status_anggota'])): ?>
                                    <?php
                                    $badgeStyle = match($member['status_anggota']) {
                                        'Utama'          => 'background-color: #5d4037; color: #fff;',
                                        'Dewasa'         => 'background-color: #d4af37; color: #2c1810;',
                                        'Madya'          => 'background-color: #8d6e63; color: #fff;',
                                        'Muayyid'        => 'background-color: #0288d1; color: #fff;',
                                        'Muhib'          => 'background-color: #2e7d32; color: #fff;',
                                        'Belum Tarbiyah' => 'background-color: #757575; color: #fff;',
                                        default          => 'background-color: #c8a97e; color: #2c1810;',
                                    };
                                    ?>
                                    <span class="badge fw-semibold" style="<?= $badgeStyle ?>">
                                        <?= esc($member['status_anggota']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">-</span>
                                <?php endif; ?>
                            </td>
                            <!-- UPA -->
                            <td>
                                <?php if (!empty($member['kode_upa'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline-dark font-monospace py-1 px-2 btn-view-history" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTimelineHistory"
                                            data-member-id="<?= $member['id'] ?>" 
                                            data-member-name="<?= esc($member['nama_lengkap']) ?>" 
                                            title="Klik untuk melihat riwayat group UPA & pembimbing">
                                        <i class="fa-solid fa-clock-rotate-left text-gold me-1"></i><?= esc($member['kode_upa']) ?>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-light text-muted py-1 px-2 btn-view-history"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTimelineHistory"
                                            data-member-id="<?= $member['id'] ?>" 
                                            data-member-name="<?= esc($member['nama_lengkap']) ?>" 
                                            title="Klik untuk melihat riwayat pembinaan masa lalu">
                                        <span class="fst-italic">-</span>
                                    </button>
                                <?php endif; ?>
                            </td>
                            <!-- Alamat -->
                            <td style="font-size:0.82rem;">
                                <?= esc($member['alamat_lengkap']) ?><br>
                                <small class="text-muted">
                                    RT <?= esc($member['rt']) ?: '-' ?> / RW <?= esc($member['rw']) ?: '-' ?>,
                                    <?= esc($member['kelurahan'] ?? '') ?>
                                </small>
                            </td>
                            <!-- Pekerjaan -->
                            <td><?= esc($member['pekerjaan']) ?: '-' ?></td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <?php if (($can_create ?? false) && $canPromote): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-success btn-promote-single"
                                                data-id="<?= $member['id'] ?>"
                                                data-name="<?= esc($member['nama_lengkap']) ?>"
                                                data-email="<?= esc($member['email'] ?? '') ?>"
                                                title="Buat Akun Portal">
                                            <i class="fa-solid fa-user-plus"></i>
                                        </button>
                                    <?php elseif ($member['account_status'] === 'invited' && !empty($member['user_id'])): ?>
                                        <!-- Tombol Reset Password untuk yang berstatus Diundang -->
                                        <?php if ($can_update ?? false): ?>
                                        <a href="<?= base_url('admin/users/reset-password/' . $member['user_id']) ?>"
                                           class="btn btn-sm btn-warning text-dark"
                                           title="Reset Password default (P4nm4s)"
                                           onclick="return confirm('Kirim/Reset password user ini ke default P4nm4s kembali?')">
                                            <i class="fa-solid fa-key"></i>
                                        </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($can_update ?? false): ?>
                                    <a href="<?= base_url('admin/members/edit/' . $member['id']) ?>"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($can_delete ?? false): ?>
                                    <a href="<?= base_url('admin/members/delete/' . $member['id']) ?>"
                                       onclick="return confirm('Hapus anggota <?= esc($member['nama_lengkap']) ?>?')"
                                       class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-users-slash fa-2x mb-2 d-block text-muted"></i>
                            Belum ada anggota terdaftar atau tidak ada hasil filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="p-3 d-flex justify-content-center">
        <?= ($pager ?? null) ? $pager->links() : '' ?>
    </div>
</div>

<!-- ===== MODAL PROMOTE ANGGOTA ===== -->
<div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="promoteSingleForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="promoteModalLabel"><i class="fa-solid fa-user-plus me-2"></i>Promosikan ke User Portal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan membuatkan akun portal untuk anggota berikut:</p>
                    <div class="p-3 bg-light rounded mb-3">
                        <strong>Nama:</strong> <span id="promoteMemberName"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Anggota <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="promoteMemberEmail" class="form-control" placeholder="Masukkan alamat email aktif" required>
                        <div class="form-text">Setiap user portal wajib menggunakan email aktif yang unik.</div>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="fa-solid fa-circle-info me-1"></i> Akun akan dibuat dengan:
                        <ul class="mb-0 mt-1">
                            <li>Role default: <strong>User</strong></li>
                            <li>Password default: <code>P4nm4s</code></li>
                            <li>Wajib ganti password pada login pertama kali.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-check me-1"></i>Buat Akun Portal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ===== MODAL TIMELINE RIWAYAT UPA ANGGOTA ===== -->
<div class="modal fade" id="modalTimelineHistory" tabindex="-1" aria-labelledby="modalTimelineLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-coffee text-cream" style="background-color: #3e2723; color: #fff8e1;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalTimelineLabel">
                        <i class="fa-solid fa-timeline text-gold me-2" style="color: #d4af37;"></i>Riwayat Perjalanan Group UPA & Naqib
                    </h5>
                    <small style="color: #d4af37;" id="timelineMemberSub">Memuat data...</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="timelineContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <div class="text-muted mt-2">Memuat riwayat pembinaan...</div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    :root { --dark-coffee: #3e2723; --gold: #d4af37; --cream: #f5f0e8; }
    .bg-light-cream    { background-color: #efebe9 !important; }
    .text-dark-coffee  { color: var(--dark-coffee) !important; }
    .text-gold         { color: var(--gold) !important; }
    .btn-gold          { background-color: var(--gold); color: var(--dark-coffee); font-weight: 600; border: none; }
    .btn-gold:hover    { background-color: #c5a028; color: var(--dark-coffee); }
    .btn-outline-gold  { color: var(--gold); border-color: var(--gold); }
    .btn-outline-gold:hover { background-color: var(--gold); color: var(--dark-coffee); }
    .bg-gold           { background-color: var(--gold) !important; }
    .border-bottom-gold { border-bottom: 2px solid var(--gold) !important; }
    .fs-7              { font-size: 0.82rem; }
    .vintage-serif     { font-family: 'Playfair Display', Georgia, serif; }
    .text-cream        { color: var(--cream); }

    /* Bulk bar animation */
    #bulkActionBar {
        transition: all 0.3s ease;
    }
    #bulkActionBar.d-none { opacity: 0; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
(function () {
    // Init DataTables — order by Nama Lengkap (col 1)
    if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#membersTable')) {
        $('#membersTable').DataTable({
            pageLength: 10,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 6] }  // Disable sort on checkbox & action cols
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_-_END_ dari _TOTAL_ entri",
                infoEmpty: "0 entri",
                infoFiltered: "(filter dari _MAX_ total)",
                zeroRecords: "Tidak ada data",
                emptyTable: "Tidak ada data",
                paginate: { first:"Awal", previous:"Sebelumnya", next:"Selanjutnya", last:"Akhir" }
            }
        });
    }

    // ---- Checkbox logic ----
    const bar         = document.getElementById('bulkActionBar');
    const countBadge  = document.getElementById('selectedCount');
    const container   = document.getElementById('hiddenCheckboxContainer');
    const selectAllCb = document.getElementById('selectAllNoAcct');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectBtn  = document.getElementById('deselectAllBtn');
    const clearBtn     = document.getElementById('clearSelectionBtn');

    function getCheckboxes() {
        return document.querySelectorAll('.member-cb');
    }

    function updateBar() {
        const checked = document.querySelectorAll('.member-cb:checked');
        countBadge.textContent = checked.length;

        // Rebuild hidden inputs
        container.innerHTML = '';
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'member_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        // Highlight rows
        getCheckboxes().forEach(cb => {
            const row = cb.closest('tr');
            if (cb.checked) row.style.background = 'rgba(212,175,55,0.10)';
            else            row.style.background = '';
        });

        // Show / hide bar
        bar.style.display = checked.length > 0 ? 'block' : 'none';
    }

    // Event delegation — DataTables rerenders DOM, so use document
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('member-cb')) updateBar();
    });

    selectAllCb.addEventListener('change', function() {
        getCheckboxes().forEach(cb => { cb.checked = this.checked; });
        updateBar();
    });

    selectAllBtn.addEventListener('click', function() {
        getCheckboxes().forEach(cb => { cb.checked = true; });
        selectAllCb.checked = true;
        updateBar();
    });

    deselectBtn.addEventListener('click', function() {
        getCheckboxes().forEach(cb => { cb.checked = false; });
        selectAllCb.checked = false;
        updateBar();
    });

    clearBtn.addEventListener('click', function() {
        getCheckboxes().forEach(cb => { cb.checked = false; });
        selectAllCb.checked = false;
        updateBar();
    });

    // Re-sync bar after DataTables page change
    document.addEventListener('draw.dt', updateBar);

    // Single promote modal handler
    const promoteModal = new bootstrap.Modal(document.getElementById('promoteModal'));
    const promoteForm = document.getElementById('promoteSingleForm');
    const promoteNameSpan = document.getElementById('promoteMemberName');
    const promoteEmailInput = document.getElementById('promoteMemberEmail');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-promote-single');
        if (btn) {
            const memberId = btn.getAttribute('data-id');
            const memberName = btn.getAttribute('data-name');
            const memberEmail = btn.getAttribute('data-email');
            
            promoteForm.action = '<?= base_url('admin/members/promote') ?>/' + memberId;
            promoteNameSpan.textContent = memberName;
            promoteEmailInput.value = memberEmail;
            
            promoteModal.show();
        }
    });

    // Confirm before bulk submit & check email requirements
    document.getElementById('bulkPromoteForm').addEventListener('submit', function(e) {
        const checkedList = document.querySelectorAll('.member-cb:checked');
        const n = checkedList.length;
        if (n === 0) { e.preventDefault(); alert('Pilih minimal 1 anggota.'); return; }
        
        let missingEmailsCount = 0;
        checkedList.forEach(cb => {
            if (cb.getAttribute('data-has-email') === '0') {
                missingEmailsCount++;
            }
        });

        if (missingEmailsCount > 0) {
            e.preventDefault();
            alert('Ada ' + missingEmailsCount + ' anggota terpilih yang belum memiliki email valid. Anggota tanpa email valid tidak dapat dipromosikan massal. Silakan isi email mereka terlebih dahulu satu per satu.');
            return;
        }

        if (!confirm('Buat akun portal untuk ' + n + ' anggota?\n\nPassword default semua: P4nm4s\nUser wajib ganti password saat login pertama.')) {
            e.preventDefault();
        }
    });

    // Timeline History Modal Handler
    const modalTimelineEl = document.getElementById('modalTimelineHistory');
    const canManageUpa = <?= (($can_update ?? false) || (auth()->user() && (auth()->user()->inGroup('admin') || auth()->user()->inGroup('kaderisasi')))) ? 'true' : 'false' ?>;

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function loadMemberHistory(memberId, memberName) {
        const timelineContainer = document.getElementById('timelineContainer');
        const timelineMemberSub = document.getElementById('timelineMemberSub');

        if (timelineMemberSub && memberName) {
            timelineMemberSub.textContent = `Anggota: ${memberName}`;
        }
        if (timelineContainer) {
            timelineContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <div class="text-muted mt-2">Memuat riwayat pembinaan...</div>
                </div>
            `;
        }

        fetch(`<?= base_url('admin/upa/member_history') ?>/${memberId}`)
            .then(r => r.json())
            .then(data => {
                if (!timelineContainer) return;
                const mName = memberName || (data.member ? data.member.nama_lengkap : '');
                if (timelineMemberSub && mName) {
                    timelineMemberSub.textContent = `Anggota: ${mName}`;
                }

                if (!data.histories || data.histories.length === 0) {
                    timelineContainer.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clock-rotate-left fa-3x d-block mb-3 opacity-25"></i>
                            Belum ada catatan riwayat UPA untuk anggota ini.
                        </div>
                    `;
                    return;
                }

                let html = '<div class="timeline-wrapper position-relative ps-4" style="border-left: 2px solid #d4af37;">';
                data.histories.forEach(h => {
                    const isCurrent = (h.is_current == 1);
                    const badgeStatus = isCurrent 
                        ? '<span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Aktif Saat Ini</span>'
                        : (h.status === 'pindah' 
                            ? '<span class="badge bg-warning text-dark"><i class="fa-solid fa-right-left me-1"></i>Pindah Group</span>' 
                            : '<span class="badge bg-secondary"><i class="fa-solid fa-graduation-cap me-1"></i>Alumni</span>');

                    const deleteBtn = canManageUpa ? `
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-delete-history" 
                            data-history-id="${h.id}" 
                            data-member-id="${memberId}" 
                            data-member-name="${escapeHtml(mName)}" 
                            data-kode-upa="${h.kode_upa}" 
                            data-is-current="${isCurrent ? '1' : '0'}"
                            title="Hapus riwayat ini"
                            style="font-size: 0.78rem;">
                            <i class="fa-solid fa-trash-can me-1"></i>Hapus
                        </button>
                    ` : '';

                    html += `
                        <div class="timeline-item position-relative mb-4 pb-2">
                            <div class="timeline-dot" style="position: absolute; width: 14px; height: 14px; border-radius: 50%; left: -32px; top: 15px; border: 2px solid #ffffff; background-color: ${isCurrent ? '#198754' : '#3e2723'};"></div>
                            <div class="card border ${isCurrent ? 'border-success shadow-sm' : 'border-light-subtle'} bg-white" style="border-radius: 10px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge font-monospace fs-6" style="background-color:#3e2723 !important; color:#fff8e1;">${h.kode_upa}</span>
                                            <div>${badgeStatus}</div>
                                        </div>
                                        ${deleteBtn}
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">${h.nama_group || ('Group ' + h.kode_upa)}</h6>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="fa-solid fa-calendar me-1 text-warning"></i>Periode: <strong>${h.periode || (h.tahun_mulai + ' - ' + (isCurrent ? 'Sekarang' : (h.tahun_selesai || '')))}</strong>
                                        </small>
                                        <small class="text-dark d-block mt-1">
                                            <i class="fa-solid fa-chalkboard-user me-1 text-warning"></i>Pembimbing (Naqib): <strong>${h.pembimbing_nama || 'Belum ditentukan'}</strong>
                                            ${h.jenjang_pembimbing ? `<span class="badge bg-light text-dark border ms-1">${h.jenjang_pembimbing}</span>` : ''}
                                        </small>
                                        ${h.nama_kelurahan ? `<small class="text-muted d-block"><i class="fa-solid fa-location-dot me-1 text-secondary"></i>Wilayah: Kel. ${h.nama_kelurahan}</small>` : ''}
                                    </div>
                                    ${h.catatan ? `<div class="bg-light p-2 rounded small text-secondary border-start border-3 border-secondary"><i class="fa-solid fa-quote-left me-1 text-muted"></i>${h.catatan}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                timelineContainer.innerHTML = html;
            })
            .catch(err => {
                const timelineContainer = document.getElementById('timelineContainer');
                if (timelineContainer) {
                    timelineContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat data riwayat: ${err.message}</div>`;
                }
            });
    }

    if (modalTimelineEl) {
        modalTimelineEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button) {
                const memberId = button.getAttribute('data-member-id');
                const memberName = button.getAttribute('data-member-name');
                if (memberId) {
                    loadMemberHistory(memberId, memberName);
                }
            }
        });
    }

    // Event delegation fallback for clicking .btn-view-history anywhere in document / DataTables
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-view-history');
        if (btn) {
            const memberId = btn.getAttribute('data-member-id');
            const memberName = btn.getAttribute('data-member-name');
            if (memberId && modalTimelineEl) {
                loadMemberHistory(memberId, memberName);
                const modal = bootstrap.Modal.getOrCreateInstance(modalTimelineEl);
                modal.show();
            }
        }
    });

    // Event delegation for delete history button inside modal timeline
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-history');
        if (!btn) return;

        const historyId = btn.getAttribute('data-history-id');
        const memberId  = btn.getAttribute('data-member-id');
        const memberName = btn.getAttribute('data-member-name') || '';
        const kodeUpa   = btn.getAttribute('data-kode-upa') || '';
        const isCurrent = btn.getAttribute('data-is-current') === '1';

        let promptMsg = `Hapus riwayat UPA ${kodeUpa}` + (memberName ? ` untuk ${memberName}?` : '?');
        if (isCurrent) {
            promptMsg += `\n\nPerhatian: Ini adalah kelompok aktif saat ini. Menghapusnya akan mengembalikan status aktif ke kelompok UPA sebelumnya secara otomatis.`;
        }

        if (!confirm(promptMsg)) {
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';

        fetch(`<?= base_url('admin/upa/member_history/delete') ?>/${historyId}?ajax=1`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                // Refresh modal timeline
                loadMemberHistory(memberId, memberName);

                // Update badge di tabel anggota tanpa reload halaman
                const memberButtons = document.querySelectorAll(`.btn-view-history[data-member-id="${memberId}"]`);
                memberButtons.forEach(b => {
                    if (res.new_kode_upa) {
                        b.className = 'btn btn-sm btn-outline-dark font-monospace py-1 px-2 btn-view-history';
                        b.innerHTML = `<i class="fa-solid fa-clock-rotate-left text-gold me-1"></i>${res.new_kode_upa}`;
                    } else {
                        b.className = 'btn btn-sm btn-light text-muted py-1 px-2 btn-view-history';
                        b.innerHTML = `<span class="fst-italic">-</span>`;
                    }
                });
            } else {
                alert('Gagal menghapus: ' + (res.message || 'Terjadi kesalahan'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-trash-can me-1"></i>Hapus';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan koneksi: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-trash-can me-1"></i>Hapus';
        });
    });
})();
</script>
<?= $this->endSection() ?>
