<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark-coffee">
                <i class="fa-solid fa-file-invoice-dollar text-gold me-2"></i>Rekapitulasi & Monitoring Usulan Pokir / Proposal
            </h4>
            <p class="text-muted small mb-0 mt-1">
                Pemetaan dan pengawalan seluruh proposal bantuan yang diajukan oleh kader, struktur, dan yayasan di Pancoran Mas.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/meja/proposals/create') ?>" class="btn btn-gold btn-sm fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i>Input Proposal Baru
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block mb-1">Total Nilai Usulan</small>
                <div class="fs-5 fw-bold text-success">
                    Rp <?= number_format($totalDana, 0, ',', '.') ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block mb-1">Usulan Pokir DPRD</small>
                <div class="fs-4 fw-bold text-primary"><?= $countPokir ?> <small class="fs-6 fw-normal text-muted">Proposal</small></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block mb-1">Usulan CSR BUMN / Swasta</small>
                <div class="fs-4 fw-bold text-success"><?= $countCsr ?> <small class="fs-6 fw-normal text-muted">Proposal</small></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block mb-1">Usulan Lembaga Sosial / Baznas</small>
                <div class="fs-4 fw-bold text-info"><?= $countSosial ?> <small class="fs-6 fw-normal text-muted">Proposal</small></div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="<?= base_url('admin/proposals') ?>" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari judul, yayasan, atau instansi..." value="<?= esc($filters['search'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="target" class="form-select form-select-sm">
                        <option value="">-- Semua Target --</option>
                        <option value="pokir_dprd" <?= ($filters['target_pengajuan'] ?? '') === 'pokir_dprd' ? 'selected' : '' ?>>Pokir DPRD</option>
                        <option value="csr_perusahaan" <?= ($filters['target_pengajuan'] ?? '') === 'csr_perusahaan' ? 'selected' : '' ?>>CSR Perusahaan</option>
                        <option value="lembaga_sosial" <?= ($filters['target_pengajuan'] ?? '') === 'lembaga_sosial' ? 'selected' : '' ?>>Lembaga Sosial / Baznas</option>
                        <option value="hibah_pemda" <?= ($filters['target_pengajuan'] ?? '') === 'hibah_pemda' ? 'selected' : '' ?>>Hibah Pemda</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="diajukan" <?= ($filters['status'] ?? '') === 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
                        <option value="disetujui" <?= ($filters['status'] ?? '') === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="ditolak" <?= ($filters['status'] ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        <option value="selesai" <?= ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark flex-fill">Filter</button>
                    <a href="<?= base_url('admin/proposals') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table of All Proposals -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Judul Usulan & Yayasan</th>
                            <th>Pengusul / Anggota</th>
                            <th>Target & Instansi</th>
                            <th>Total Anggaran</th>
                            <th>Status & Catatan</th>
                            <th class="text-end" style="min-width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($proposals)): ?>
                            <?php foreach ($proposals as $idx => $p): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-dark-coffee"><?= esc($p['judul_proposal']) ?></div>
                                        <div class="small text-muted">
                                            <i class="fa-solid fa-landmark text-gold me-1"></i><?= esc($p['nama_lembaga'] ?: '-') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($p['pembuat_nama'] ?: 'Pengguna Portal') ?></div>
                                        <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y', strtotime($p['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                            $targetBadge = match($p['target_pengajuan']) {
                                                'pokir_dprd'     => ['bg' => 'primary', 'label' => 'Pokir DPRD'],
                                                'csr_perusahaan' => ['bg' => 'success', 'label' => 'CSR'],
                                                'lembaga_sosial' => ['bg' => 'info',    'label' => 'Lembaga Sosial'],
                                                'hibah_pemda'    => ['bg' => 'warning', 'label' => 'Hibah Pemda'],
                                                default          => ['bg' => 'secondary', 'label' => 'Umum'],
                                            };
                                        ?>
                                        <span class="badge bg-<?= $targetBadge['bg'] ?>-subtle text-<?= $targetBadge['bg'] ?> border border-<?= $targetBadge['bg'] ?>-subtle mb-1">
                                            <?= $targetBadge['label'] ?>
                                        </span>
                                        <div class="small text-muted"><?= esc($p['tujuan_nama'] ?: ($p['tujuan_instansi'] ?: '-')) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            Rp <?= number_format((float)$p['total_anggaran'], 0, ',', '.') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $statusBadge = match($p['status']) {
                                                'diajukan'   => ['bg' => 'info',    'label' => 'Diajukan'],
                                                'disetujui'  => ['bg' => 'success', 'label' => 'Disetujui'],
                                                'ditolak'    => ['bg' => 'danger',  'label' => 'Ditolak'],
                                                'selesai'    => ['bg' => 'dark',    'label' => 'Selesai'],
                                                default      => ['bg' => 'secondary', 'label' => 'Draft'],
                                            };
                                        ?>
                                        <button type="button" class="badge bg-<?= $statusBadge['bg'] ?> border-0 cursor-pointer mb-1" 
                                                onclick="openStatusModal(<?= $p['id'] ?>, '<?= $p['status'] ?>', '<?= esc(addslashes($p['catatan_admin'] ?? '')) ?>', '<?= esc(addslashes($p['judul_proposal'])) ?>')" title="Klik untuk ubah status">
                                            <?= $statusBadge['label'] ?> <i class="fa-solid fa-pen-to-square ms-1" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <?php if (!empty($p['catatan_admin'])): ?>
                                            <div class="small text-muted fst-italic" style="font-size: 0.75rem;">
                                                "<?= esc($p['catatan_admin']) ?>"
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/meja/proposals/' . $p['id'] . '/preview') ?>" class="btn btn-outline-primary" title="Lihat Pratinjau">
                                                <i class="fa-solid fa-eye me-1"></i>Lihat
                                            </a>
                                            <a href="<?= base_url('admin/meja/proposals/' . $p['id'] . '/print') ?>" target="_blank" class="btn btn-outline-dark" title="Cetak / PDF">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <a href="<?= base_url('admin/meja/proposals/' . $p['id'] . '/word') ?>" class="btn btn-outline-success" title="Word">
                                                <i class="fa-solid fa-file-word"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">- Belum ada proposal yang masuk -</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Status Admin -->
<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-dark-coffee text-white border-0 py-3 px-4">
                <h6 class="modal-title fw-bold" id="modalStatusTitle">Update Status Pengawalan Proposal</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formStatus" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3" id="modalProposalName"></p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status Pengajuan</label>
                        <select name="status" id="modalStatusSelect" class="form-select" required>
                            <option value="draft">Draft (Belum Diajukan)</option>
                            <option value="diajukan">Diajukan ke Dewan / Instansi</option>
                            <option value="disetujui">Disetujui / Terverifikasi</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="selesai">Selesai (Dana / Bantuan Cair)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan Pengawalan / Keterangan Admin</label>
                        <textarea name="catatan_admin" id="modalCatatanAdmin" class="form-control" rows="3" placeholder="Contoh: Sudah diajukan ke Fraksi PKS DPRD Komisi D, menunggu pembahasan banggar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold fw-semibold px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openStatusModal(id, currentStatus, catatan, title) {
    const form = document.getElementById('formStatus');
    form.action = `<?= base_url('admin/proposals') ?>/${id}/status`;
    document.getElementById('modalProposalName').innerHTML = `Proposal: <strong>${title}</strong>`;
    document.getElementById('modalStatusSelect').value = currentStatus;
    document.getElementById('modalCatatanAdmin').value = catatan;

    const modal = new bootstrap.Modal(document.getElementById('modalStatus'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
