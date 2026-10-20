<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('admin/meja/profile') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-file-signature text-gold me-2"></i>Proposal Saya
                </h4>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Kelola dan cetak proposal resmi untuk Pokir DPRD, CSR Perusahaan, Lembaga Sosial, dan Bantuan Hibah.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/meja/lembaga') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fa-solid fa-landmark me-1"></i>Data Yayasan & Legalitas
            </a>
            <a href="<?= base_url('admin/meja/proposals/create') ?>" class="btn btn-gold btn-sm fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i>Buat Proposal Baru
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="<?= base_url('admin/meja/proposals') ?>" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari judul proposal / instansi tujuan..." value="<?= esc($filters['search'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="target" class="form-select form-select-sm">
                        <option value="">-- Semua Target Pengajuan --</option>
                        <option value="pokir_dprd" <?= ($filters['target_pengajuan'] ?? '') === 'pokir_dprd' ? 'selected' : '' ?>>Pokir DPRD</option>
                        <option value="csr_perusahaan" <?= ($filters['target_pengajuan'] ?? '') === 'csr_perusahaan' ? 'selected' : '' ?>>CSR Perusahaan</option>
                        <option value="lembaga_sosial" <?= ($filters['target_pengajuan'] ?? '') === 'lembaga_sosial' ? 'selected' : '' ?>>Lembaga Sosial / Baznas</option>
                        <option value="hibah_pemda" <?= ($filters['target_pengajuan'] ?? '') === 'hibah_pemda' ? 'selected' : '' ?>>Hibah / Bansos Pemda</option>
                        <option value="sponsor_umum" <?= ($filters['target_pengajuan'] ?? '') === 'sponsor_umum' ? 'selected' : '' ?>>Sponsor / Donatur Umum</option>
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
                    <a href="<?= base_url('admin/meja/proposals') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Proposals -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <?php if (empty($proposals)): ?>
                <div class="text-center py-5">
                    <div class="text-muted mb-3" style="font-size: 3rem;">
                        <i class="fa-solid fa-file-invoice text-gold"></i>
                    </div>
                    <h6 class="fw-bold text-dark-coffee">Belum Ada Proposal yang Dibuat</h6>
                    <p class="text-muted small mx-auto mb-3" style="max-width: 450px;">
                        Mulai buat proposal bantuan/pokir pertama Anda sekarang dengan mengisi formulir wizard yang otomatis dan praktis.
                    </p>
                    <a href="<?= base_url('admin/meja/proposals/create') ?>" class="btn btn-gold btn-sm fw-semibold shadow-sm">
                        <i class="fa-solid fa-plus me-1"></i>Buat Proposal Sekarang
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Judul Proposal & No. Surat</th>
                                <th>Lembaga Pengusul</th>
                                <th>Tujuan Pengajuan</th>
                                <th>Total Anggaran (RAB)</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proposals as $idx => $p): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-dark-coffee"><?= esc($p['judul_proposal']) ?></div>
                                        <div class="small text-muted">
                                            No: <code><?= esc($p['nomor_surat'] ?: '-') ?></code> &bull; Tgl: <?= $p['tanggal_surat'] ? date('d/m/Y', strtotime($p['tanggal_surat'])) : '-' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($p['nama_lembaga'] ?: 'Pribadi / Panitia') ?></div>
                                        <small class="text-muted"><?= strtoupper(str_replace('_', ' ', esc($p['jenis_lembaga'] ?? 'Yayasan'))) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                            $targetBadge = match($p['target_pengajuan']) {
                                                'pokir_dprd'     => ['bg' => 'primary', 'label' => 'Pokir DPRD'],
                                                'csr_perusahaan' => ['bg' => 'success', 'label' => 'CSR Perusahaan'],
                                                'lembaga_sosial' => ['bg' => 'info',    'label' => 'Lembaga Sosial / Baznas'],
                                                'hibah_pemda'    => ['bg' => 'warning', 'label' => 'Hibah Pemda'],
                                                default          => ['bg' => 'secondary', 'label' => 'Umum'],
                                            };
                                        ?>
                                        <span class="badge bg-<?= $targetBadge['bg'] ?>-subtle text-<?= $targetBadge['bg'] ?> border border-<?= $targetBadge['bg'] ?>-subtle mb-1">
                                            <?= $targetBadge['label'] ?>
                                        </span>
                                        <div class="small text-muted" style="font-size: 0.8rem;">
                                            <?= esc($p['tujuan_nama'] ?: ($p['tujuan_instansi'] ?: '-')) ?>
                                        </div>
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
                                        <span class="badge bg-<?= $statusBadge['bg'] ?>">
                                            <?= $statusBadge['label'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/meja/proposals/' . $p['id'] . '/preview') ?>" class="btn btn-outline-primary" title="Pratinjau Dokumen">
                                                <i class="fa-solid fa-eye me-1"></i>Preview
                                            </a>
                                            <a href="<?= base_url('admin/meja/proposals/' . $p['id'] . '/print') ?>" target="_blank" class="btn btn-outline-dark" title="Cetak / Simpan ke PDF">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <a href="<?= base_url('admin/meja/proposals/' . $p['id'] . '/word') ?>" class="btn btn-outline-success" title="Unduh File Word (.doc)">
                                                <i class="fa-solid fa-file-word"></i>
                                            </a>
                                            <a href="<?= base_url('admin/meja/proposals/' . $p['id'] . '/edit') ?>" class="btn btn-outline-secondary" title="Edit Proposal">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteProposal(<?= $p['id'] ?>, '<?= esc(addslashes($p['judul_proposal'])) ?>')" title="Hapus Proposal">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteProposalForm" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="POST">
</form>

<script>
function confirmDeleteProposal(id, title) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Proposal?',
            text: `Yakin ingin menghapus proposal "${title}" beserta rincian anggarannya?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteProposalForm');
                form.action = `<?= base_url('admin/meja/proposals') ?>/${id}/delete`;
                form.submit();
            }
        });
    } else {
        if (confirm(`Yakin ingin menghapus proposal "${title}"?`)) {
            const form = document.getElementById('deleteProposalForm');
            form.action = `<?= base_url('admin/meja/proposals') ?>/${id}/delete`;
            form.submit();
        }
    }
}
</script>

<?= $this->endSection() ?>
