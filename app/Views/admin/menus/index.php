<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-list-check me-2 text-gold"></i>Kelola Menu & Akses Role
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-secondary text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-secondary text-decoration-none">Pengaturan</a></li>
                <li class="breadcrumb-item active text-dark-coffee fw-medium" aria-current="page">Kelola Menu</li>
            </ol>
        </nav>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/menus/create') ?>" class="btn btn-gold fw-semibold">
        <i class="fa-solid fa-plus me-2"></i>Tambah Menu
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('message') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-circle-xmark me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0" id="menusTable">
            <thead class="bg-dark-coffee text-cream" style="background-color: #3e2723; color: #fff8e1;">
                <tr>
                    <th class="ps-4" style="width: 60px;">Urutan</th>
                    <th>Judul Menu</th>
                    <th>Parent Menu</th>
                    <th>Ikon</th>
                    <th>URL Path</th>
                    <th>Akses Role (Shield Group)</th>
                    <th class="text-center" style="width: 140px; min-width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($menus)): ?>
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td class="ps-4 text-center fw-bold text-muted"><?= $m['sort_order'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($m['parent_id'])): ?>
                                        <i class="fa-solid fa-turn-up fa-rotate-90 text-secondary ms-2 me-1"></i>
                                    <?php endif; ?>
                                    <span class="<?= empty($m['parent_id']) ? 'fw-bold text-dark-coffee' : 'text-secondary small' ?>">
                                        <?= esc($m['title']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-secondary"><?= esc($m['parent_title']) ?></td>
                            <td>
                                <?php if (!empty($m['icon'])): ?>
                                    <span class="badge bg-light text-dark border p-2" style="font-size: 0.95rem;">
                                        <i class="<?= esc($m['icon']) ?> text-gold"></i>
                                    </span>
                                    <small class="text-muted ms-1"><?= esc($m['icon']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($m['url'] === ''): ?>
                                    <span class="badge bg-secondary">Outbound Link (Home)</span>
                                <?php elseif ($m['url'] === null): ?>
                                    <span class="badge bg-light text-dark border">None (Parent Wrapper)</span>
                                <?php else: ?>
                                    <code class="text-dark"><?= esc($m['url']) ?></code>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <?php if (empty($m['roles'])): ?>
                                        <span class="badge bg-danger">No Access</span>
                                    <?php else: ?>
                                        <?php foreach ($m['roles'] as $r): ?>
                                            <span class="badge bg-gold text-dark font-monospace"><?= esc($r) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <?php if ($can_update ?? false): ?>
                                    <a href="<?= base_url('admin/menus/edit/' . $m['id']) ?>" 
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($can_delete ?? false): ?>
                                    <a href="<?= base_url('admin/menus/delete/' . $m['id']) ?>" 
                                       onclick="return confirm('Hapus menu ini? Semua sub-menu dan relasi akses role terkait akan ikut terhapus.')" 
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-list-check fa-2x d-block mb-2 opacity-50"></i>
                            Belum ada data menu.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .text-dark-coffee {
        color: #3e2723 !important;
    }
    .btn-gold {
        background-color: #d4af37 !important;
        color: #3e2723 !important;
        font-weight: 600;
        border: none;
    }
    .bg-gold {
        background-color: #d4af37 !important;
    }
    .text-gold {
        color: #d4af37 !important;
    }
    .text-cream {
        color: #fff8e1 !important;
    }
</style>
<?= $this->endSection() ?>
