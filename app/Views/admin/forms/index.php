<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <!-- Header Banner -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #2C1810 0%, #4a2817 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h3 class="fw-bold mb-1" style="color: var(--gold); font-family: 'Playfair Display', serif;">
                        <i class="fa-solid fa-clipboard-question me-2"></i>Manajemen Formulir & Survei
                    </h3>
                    <p class="mb-0 text-white-50 small">
                        Kelola kuesioner dinamis, skrining kesehatan, dan formulir pendataan publik yang terhubung ke anggota.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('admin/forms/create') ?>" class="btn btn-gold fw-semibold shadow-sm">
                        <i class="fa-solid fa-plus me-1"></i>Buat Form Baru
                    </a>
                </div>
            </div>
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

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Judul Formulir</th>
                            <th>Tautan Publik</th>
                            <th class="text-center">Total Respon</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Auto-Link</th>
                            <th class="text-end" style="min-width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($forms)): ?>
                            <?php foreach ($forms as $idx => $f): ?>
                                <?php $publicUrl = base_url('form/' . $f['slug']); ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-dark-coffee"><?= esc($f['title']) ?></div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 280px;">
                                            <?= esc($f['description'] ?: 'Tidak ada deskripsi.') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm" style="max-width: 230px;">
                                            <input type="text" class="form-control bg-light" value="<?= $publicUrl ?>" readonly id="url_<?= $f['id'] ?>">
                                            <button class="btn btn-outline-secondary" type="button" onclick="copyLink('url_<?= $f['id'] ?>')" title="Salin Tautan">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                            <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-outline-primary" title="Buka Form">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/forms/' . $f['id'] . '/responses') ?>" class="badge bg-primary text-decoration-none px-3 py-2">
                                            <i class="fa-solid fa-users me-1"></i><?= $f['response_count'] ?> Respon
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?= base_url('admin/forms/' . $f['id'] . '/toggle') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <?php 
                                                $isExpired = (!empty($f['closed_at']) && strtotime($f['closed_at']) <= time());
                                            ?>
                                            <?php if ($isExpired): ?>
                                                <span class="badge bg-danger px-2 py-1 mb-1 d-inline-block" title="Batas waktu lewat: <?= date('d M Y, H:i', strtotime($f['closed_at'])) ?>">
                                                    <i class="fa-solid fa-hourglass-end me-1"></i>Expired
                                                </span>
                                            <?php elseif (!empty($f['is_active'])): ?>
                                                <button type="submit" class="badge bg-success border-0 px-3 py-1 cursor-pointer mb-1" title="Klik untuk nonaktifkan">
                                                    <i class="fa-solid fa-check-circle me-1"></i>Aktif
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="badge bg-secondary border-0 px-3 py-1 cursor-pointer mb-1" title="Klik untuk aktifkan">
                                                    <i class="fa-solid fa-ban me-1"></i>Ditutup
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                        <?php if (!empty($f['closed_at'])): ?>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                <i class="fa-regular fa-clock me-1"></i>s/d <?= date('d/m/y H:i', strtotime($f['closed_at'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($f['auto_link_member'])): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" title="Mencocokkan No HP/NIK ke data anggota">
                                                <i class="fa-solid fa-link me-1"></i>Ya
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border">Manual</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/forms/' . $f['id'] . '/builder') ?>" class="btn btn-outline-dark" title="Form Builder (Pertanyaan)">
                                                <i class="fa-solid fa-sliders me-1"></i>Builder
                                            </a>
                                            <a href="<?= base_url('admin/forms/' . $f['id'] . '/responses') ?>" class="btn btn-outline-success" title="Lihat Respon">
                                                <i class="fa-solid fa-table-list"></i>
                                            </a>
                                            <a href="<?= base_url('admin/forms/' . $f['id'] . '/edit') ?>" class="btn btn-outline-secondary" title="Edit Form">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?= $f['id'] ?>, '<?= esc($f['title']) ?>')" title="Hapus Form">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<form id="deleteForm" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="POST">
</form>

<script>
function copyLink(inputId) {
    const copyText = document.getElementById(inputId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Tautan berhasil disalin!',
        showConfirmButton: false,
        timer: 2000
    });
}

function confirmDelete(id, title) {
    Swal.fire({
        title: 'Hapus Formulir?',
        text: `Formulir "${title}" beserta semua pertanyaan dan respon yang masuk akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `<?= base_url('admin/forms') ?>/${id}/delete`;
            form.submit();
        }
    });
}
</script>

<?= $this->endSection() ?>
