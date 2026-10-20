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
                    <i class="fa-solid fa-landmark text-gold me-2"></i>Profil Yayasan / Lembaga Saya
                </h4>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Bank data profil organisasi, yayasan, DKM, PAUD, atau majelis taklim beserta berkas legalitas resmi untuk pengajuan proposal.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/meja/proposals/create') ?>" class="btn btn-outline-dark btn-sm shadow-sm">
                <i class="fa-solid fa-file-signature me-1"></i>Buat Proposal Baru
            </a>
            <a href="<?= base_url('admin/meja/lembaga/create') ?>" class="btn btn-gold btn-sm fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i>Tambah Lembaga / Yayasan
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

    <!-- Info Banner -->
    <div class="alert bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
        <div class="fs-2 text-primary"><i class="fa-solid fa-shield-halved"></i></div>
        <div class="small">
            <strong>Keuntungan Bank Data Legalitas:</strong> Unggah berkas resmi yayasan/lembaga Anda (KTP Ketua, NPWP, Akta Notaris, SK Kemenkumham, NIB, & Buku Tabungan) <strong>sekali saja</strong>. Berkas ini akan otomatis terlampir setiap kali Anda meng-generate proposal Pokir DPRD, CSR, maupun bantuan sosial lainnya!
        </div>
    </div>

    <?php if (empty($lembagas)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <div class="text-muted mb-3" style="font-size: 3.5rem;">
                    <i class="fa-solid fa-building-circle-check text-gold"></i>
                </div>
                <h5 class="fw-bold text-dark-coffee">Belum Ada Profil Lembaga / Yayasan</h5>
                <p class="text-muted small mx-auto" style="max-width: 480px;">
                    Anda belum mendaftarkan yayasan, DKM, atau majelis taklim. Tambahkan profil lembaga Anda sekarang agar siap digunakan untuk membuat proposal.
                </p>
                <a href="<?= base_url('admin/meja/lembaga/create') ?>" class="btn btn-gold btn-sm px-4 fw-semibold shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i>Daftarkan Lembaga / Yayasan Sekarang
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($lembagas as $l): ?>
                <?php
                    $isOwner = $lembagaModel->isOwner($l, auth()->id(), $member['id'] ?? null, $isAdmin);
                    $authList = !empty($l['authorized_members']) ? (json_decode($l['authorized_members'], true) ?: []) : [];
                    $authCount = count($authList);
                ?>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 position-relative">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($l['file_logo'])): ?>
                                        <img src="<?= base_url('uploads/lembaga/' . $l['file_logo']) ?>" alt="Logo" class="rounded-3 object-fit-cover shadow-sm" style="width: 55px; height: 55px;">
                                    <?php else: ?>
                                        <div class="rounded-3 bg-dark-coffee text-gold d-flex align-items-center justify-content-center fw-bold fs-4 shadow-sm" style="width: 55px; height: 55px;">
                                            <i class="fa-solid fa-landmark"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h5 class="fw-bold text-dark-coffee mb-1"><?= esc($l['nama_lembaga']) ?></h5>
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                <?= strtoupper(str_replace('_', ' ', esc($l['jenis_lembaga']))) ?>
                                            </span>
                                            <?php if ($isOwner): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle" title="Anda adalah pendaftar utama yayasan ini">
                                                    <i class="fa-solid fa-crown me-1"></i>Owner
                                                </span>
                                            <?php elseif (!empty($l['is_public'])): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border" title="Terbuka untuk semua anggota komunitas">
                                                    <i class="fa-solid fa-globe me-1"></i>Akses Publik
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="Diberikan wewenang membuat proposal">
                                                    <i class="fa-solid fa-user-check me-1"></i>Pengurus Berwenang
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($l['kategori_bidang'])): ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                    <?= esc($l['kategori_bidang']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border rounded-circle" type="button" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url('admin/meja/proposals/create?lembaga_id=' . $l['id']) ?>">
                                                <i class="fa-solid fa-file-circle-plus text-success me-2"></i>Buat Proposal Baru
                                            </a>
                                        </li>
                                        <?php if ($isOwner): ?>
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('admin/meja/lembaga/' . $l['id'] . '/edit') ?>">
                                                    <i class="fa-solid fa-pen text-primary me-2"></i>Edit Data & Berkas
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="confirmDeleteLembaga(<?= $l['id'] ?>, '<?= esc(addslashes($l['nama_lembaga'])) ?>')">
                                                    <i class="fa-solid fa-trash-can me-2"></i>Hapus Lembaga
                                                </button>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Detail Singkat -->
                            <div class="small text-muted mb-3">
                                <div class="mb-1">
                                    <i class="fa-solid fa-location-dot text-danger me-2" style="width: 16px;"></i>
                                    <?= esc($l['alamat_lengkap'] ?: ($l['kelurahan'] . ', ' . $l['kecamatan'])) ?>
                                </div>
                                <div class="mb-1">
                                    <i class="fa-solid fa-user-tie text-gold me-2" style="width: 16px;"></i>
                                    Ketua: <strong><?= esc($l['nama_ketua'] ?: '-') ?></strong> (<?= esc($l['hp_ketua'] ?: '-') ?>)
                                </div>
                                <?php if (!empty($l['nama_bank']) && !empty($l['no_rekening'])): ?>
                                    <div class="mb-1">
                                        <i class="fa-solid fa-credit-card text-success me-2" style="width: 16px;"></i>
                                        Rekening: <?= esc($l['nama_bank']) ?> - <strong><?= esc($l['no_rekening']) ?></strong> (a.n <?= esc($l['atas_nama_rekening']) ?>)
                                    </div>
                                <?php endif; ?>
                                <?php if ($authCount > 0): ?>
                                    <div class="mb-1">
                                        <i class="fa-solid fa-users text-primary me-2" style="width: 16px;"></i>
                                        Pengurus Diberi Akses: <span class="badge bg-light text-dark border"><?= $authCount ?> Orang Anggota</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Status Kelengkapan Berkas Legalitas -->
                            <div class="p-3 bg-light rounded-3 mb-3">
                                <div class="fw-semibold text-dark-coffee small mb-2 d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-file-circle-check text-success me-1"></i>Kelengkapan Berkas Legalitas:</span>
                                    <?php
                                        $docs = [
                                            'ktp'      => !empty($l['file_ktp_ketua']),
                                            'npwp_k'   => !empty($l['file_npwp_ketua']),
                                            'akta'     => !empty($l['file_akta']),
                                            'sk'       => !empty($l['file_sk_kemenkumham']),
                                            'nib'      => !empty($l['file_nib']),
                                            'npwp_l'   => !empty($l['file_npwp_lembaga']),
                                            'rekening' => !empty($l['file_buku_rekening']),
                                        ];
                                        $uploadedCount = count(array_filter($docs));
                                        $totalDocs = count($docs);
                                        $pct = round(($uploadedCount / $totalDocs) * 100);
                                    ?>
                                    <span class="badge bg-<?= $pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger') ?>">
                                        <?= $uploadedCount ?> / <?= $totalDocs ?> Berkas (<?= $pct ?>%)
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge <?= $docs['ktp'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?>">
                                        <i class="fa-solid fa-<?= $docs['ktp'] ? 'check' : 'xmark' ?> me-1"></i>KTP Ketua
                                    </span>
                                    <span class="badge <?= $docs['npwp_k'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?>">
                                        <i class="fa-solid fa-<?= $docs['npwp_k'] ? 'check' : 'xmark' ?> me-1"></i>NPWP Ketua
                                    </span>
                                    <span class="badge <?= $docs['npwp_l'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?>">
                                        <i class="fa-solid fa-<?= $docs['npwp_l'] ? 'check' : 'xmark' ?> me-1"></i>NPWP Lembaga
                                    </span>
                                    <span class="badge <?= $docs['akta'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?>">
                                        <i class="fa-solid fa-<?= $docs['akta'] ? 'check' : 'xmark' ?> me-1"></i>Akta Notaris
                                    </span>
                                    <span class="badge <?= $docs['sk'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?>">
                                        <i class="fa-solid fa-<?= $docs['sk'] ? 'check' : 'xmark' ?> me-1"></i>SK Kemenkumham
                                    </span>
                                    <span class="badge <?= $docs['nib'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?>">
                                        <i class="fa-solid fa-<?= $docs['nib'] ? 'check' : 'xmark' ?> me-1"></i>NIB
                                    </span>
                                    <span class="badge <?= $docs['rekening'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?>">
                                        <i class="fa-solid fa-<?= $docs['rekening'] ? 'check' : 'xmark' ?> me-1"></i>Buku Rekening
                                    </span>
                                </div>
                            </div>

                            <!-- Footer Buttons -->
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('admin/meja/proposals/create?lembaga_id=' . $l['id']) ?>" class="btn btn-gold btn-sm flex-fill fw-semibold">
                                    <i class="fa-solid fa-file-pen me-1"></i>Buat Proposal Baru
                                </a>
                                <?php if ($isOwner): ?>
                                    <a href="<?= base_url('admin/meja/lembaga/' . $l['id'] . '/edit') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit / Berkas
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-light btn-sm text-muted border" title="Hanya pendaftar yayasan yang dapat mengubah berkas legalitas resmi">
                                        <i class="fa-solid fa-lock me-1"></i>Berkas Terkunci
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Hidden Delete Form -->
<form id="deleteLembagaForm" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="POST">
</form>

<script>
function confirmDeleteLembaga(id, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Profil Lembaga?',
            text: `Yakin ingin menghapus data lembaga "${name}" beserta seluruh berkas legalitasnya?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteLembagaForm');
                form.action = `<?= base_url('admin/meja/lembaga') ?>/${id}/delete`;
                form.submit();
            }
        });
    } else {
        if (confirm(`Yakin ingin menghapus lembaga "${name}"?`)) {
            const form = document.getElementById('deleteLembagaForm');
            form.action = `<?= base_url('admin/meja/lembaga') ?>/${id}/delete`;
            form.submit();
        }
    }
}
</script>

<?= $this->endSection() ?>
