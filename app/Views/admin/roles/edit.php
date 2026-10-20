<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<?php
$hexMap = [
    'danger'    => '#ef4444',
    'warning'   => '#f59e0b',
    'info'      => '#3b82f6',
    'success'   => '#10b981',
    'primary'   => '#6366f1',
    'secondary' => '#6b7280',
    'dark'      => '#1f2937',
];
$hex = $hexMap[$role['color'] ?? 'secondary'] ?? '#6b7280';
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/roles') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center"
             style="width:44px;height:44px;background:<?= $hex ?>22;">
            <i class="fa-solid <?= $role['icon'] ?? 'fa-circle-user' ?> fs-5" style="color:<?= $hex ?>"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)">
                Edit Role: <?= esc($role['title'] ?? $slug) ?>
            </h4>
            <p class="text-muted mb-0 small">
                Slug: <code><?= $slug ?></code>
                <?php if ($is_system): ?>
                <span class="badge ms-2" style="background:<?= $hex ?>22;color:<?= $hex ?>;font-size:.6rem;">
                    <i class="fa-solid fa-shield-halved me-1"></i>System Role
                </span>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="<?= base_url('admin/roles/update/' . $slug) ?>">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Kiri: Info Role -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px;border-top:4px solid <?= $hex ?> !important;">
                <div class="card-header py-3" style="background: var(--dark-coffee);">
                    <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                        <i class="fa-solid fa-id-badge me-2"></i>Informasi Role
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if (!$is_system): ?>
                    <!-- Custom role: bisa edit nama, warna, ikon -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Role</label>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc($role['title'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"><?= esc($role['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Warna -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Warna</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($colorOptions as $key => $opt): ?>
                            <label class="color-pick" title="<?= $opt['label'] ?>">
                                <input type="radio" name="color" value="<?= $key ?>"
                                       <?= ($role['color'] ?? 'secondary') === $key ? 'checked' : '' ?> hidden>
                                <span class="color-dot" style="background:<?= $opt['hex'] ?>;"></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Ikon -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Ikon</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($iconOptions as $iconClass): ?>
                            <label class="icon-pick" title="<?= $iconClass ?>">
                                <input type="radio" name="icon" value="<?= $iconClass ?>"
                                       <?= ($role['icon'] ?? '') === $iconClass ? 'checked' : '' ?> hidden>
                                <span class="icon-dot">
                                    <i class="fa-solid <?= $iconClass ?>"></i>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- System role: info saja, tidak bisa diubah -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-muted">Nama Role</label>
                        <p class="fw-semibold mb-0"><?= esc($role['title'] ?? $slug) ?></p>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-muted">Deskripsi</label>
                        <p class="text-muted small mb-0"><?= esc($role['description'] ?? '') ?></p>
                    </div>
                    <div class="alert alert-warning border-0 small mt-3 mb-0 py-2">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Info role sistem tidak dapat diubah, hanya permissions yang bisa diedit.
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light d-grid gap-2">
                    <button type="submit" class="btn fw-semibold"
                            style="background:#3d1f0f;color:#fff;border:none;">
                        <i class="fa-solid fa-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="<?= base_url('admin/roles/copy/' . $slug) ?>"
                       class="btn btn-outline-secondary btn-sm"
                       onclick="return confirm('Duplikasi role ini?')">
                        <i class="fa-solid fa-copy me-1"></i>Duplikasi Role Ini
                    </a>
                </div>
            </div>
        </div>

        <!-- Kanan: Matrix CRUD -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background: var(--dark-coffee);">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                            <i class="fa-solid fa-table-list me-2"></i>Permissions per Halaman
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm" style="background:#ffffff22;color:var(--gold);" onclick="toggleAll(true)">
                                <i class="fa-solid fa-check-double me-1"></i>Semua
                            </button>
                            <button type="button" class="btn btn-sm" style="background:#ffffff22;color:var(--gold);" onclick="toggleAll(false)">
                                <i class="fa-solid fa-xmark me-1"></i>Kosong
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:40%">Halaman</th>
                                    <th class="text-center">
                                        <span class="badge bg-info">Read</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Lihat</small>
                                    </th>
                                    <th class="text-center">
                                        <span class="badge bg-success">Create</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Tambah</small>
                                    </th>
                                    <th class="text-center">
                                        <span class="badge bg-warning text-dark">Update</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Edit</small>
                                    </th>
                                    <th class="text-center">
                                        <span class="badge bg-danger">Delete</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Hapus</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages as $pageKey => $page):
                                    $p  = $rolePerms[$pageKey] ?? [];
                                    $cr = !empty($p['can_create']);
                                    $rd = !empty($p['can_read']);
                                    $up = !empty($p['can_update']);
                                    $dl = !empty($p['can_delete']);
                                ?>
                                <tr class="perm-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid <?= $page['icon'] ?> text-muted" style="width:18px;"></i>
                                            <div>
                                                <div class="fw-semibold small"><?= esc($page['label']) ?></div>
                                                <small class="text-muted"><?= $page['route'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <?php
                                    $ops = [
                                        'read'   => ['checked' => $rd, 'cls' => 'info'],
                                        'create' => ['checked' => $cr, 'cls' => 'success'],
                                        'update' => ['checked' => $up, 'cls' => 'warning'],
                                        'delete' => ['checked' => $dl, 'cls' => 'danger'],
                                    ];
                                    foreach ($ops as $op => $cfg):
                                    ?>
                                    <td class="text-center">
                                        <div class="form-check d-inline-flex">
                                            <input class="form-check-input perm-cb" type="checkbox"
                                                   name="perm_<?= $pageKey ?>_<?= $op ?>"
                                                   id="perm_<?= $pageKey ?>_<?= $op ?>"
                                                   value="1"
                                                   data-page="<?= $pageKey ?>"
                                                   data-op="<?= $op ?>"
                                                   style="border-color: var(--bs-<?= $cfg['cls'] ?>); cursor:pointer;"
                                                   <?= $cfg['checked'] ? 'checked' : '' ?>>
                                        </div>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 px-4">
                    <small class="text-muted">
                        <i class="fa-solid fa-circle-info me-1 text-info"></i>
                        <strong>Read</strong> = bisa buka halaman &nbsp;|&nbsp;
                        <strong>Create</strong> = bisa tambah data &nbsp;|&nbsp;
                        <strong>Update</strong> = bisa edit data &nbsp;|&nbsp;
                        <strong>Delete</strong> = bisa hapus data
                    </small>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.color-pick input:checked + .color-dot { outline: 3px solid #1f2937; outline-offset: 2px; }
.color-dot { display:inline-block; width:28px; height:28px; border-radius:50%; cursor:pointer; transition:.15s; }
.color-dot:hover { transform:scale(1.15); }
.icon-pick input:checked + .icon-dot { background: var(--dark-coffee); color: var(--cream) !important; }
.icon-dot { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:8px; border:1px solid #e5e7eb; cursor:pointer; transition:.15s; color:#6b7280; }
.icon-dot:hover { background:#f3f4f6; }
.perm-row:hover { background:#f9fafb; }
</style>

<script>
function toggleAll(state) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = state);
}
</script>

<?= $this->endSection() ?>
