<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<?php
// Hex warna dari color name
$hexMap = [
    'danger'    => '#ef4444',
    'warning'   => '#f59e0b',
    'info'      => '#3b82f6',
    'success'   => '#10b981',
    'primary'   => '#6366f1',
    'secondary' => '#6b7280',
    'dark'      => '#1f2937',
];
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-lock me-2 text-gold"></i>Page Role & Permissions
        </h4>
        <p class="text-muted mb-0 small">Daftar role, hak akses CRUD, dan mapping halaman yang dapat diakses setiap role.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/roles/create') ?>" class="btn btn-sm px-4 fw-semibold"
       style="background:#3d1f0f;color:#fff;border:none;">
        <i class="fa-solid fa-plus me-2"></i>Tambah Role
    </a>
    <?php endif; ?>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Role Cards -->
<div class="row g-3 mb-5">
    <?php foreach ($roles as $slug => $role):
        $hex = $hexMap[$role['color']] ?? '#6b7280';
    ?>
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 role-card" style="border-top: 4px solid <?= $hex ?> !important; transition: transform .2s;">
            <div class="card-body p-4">
                <!-- Header card -->
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:48px;height:48px;background:<?= $hex ?>22;">
                            <i class="fa-solid <?= $role['icon'] ?> fs-5" style="color:<?= $hex ?>"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0"><?= esc($role['title']) ?></h6>
                            <?php if ($role['is_system']): ?>
                            <span class="badge" style="font-size:.6rem;background:<?= $hex ?>22;color:<?= $hex ?>;">
                                <i class="fa-solid fa-shield-halved me-1"></i>System Role
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="badge rounded-pill" style="background:<?= $hex ?>22;color:<?= $hex ?>;font-size:.75rem;">
                        <?= $role['user_count'] ?> user
                    </span>
                </div>

                <p class="text-muted small mb-3"><?= esc($role['description']) ?></p>

                <!-- Summary permissions -->
                <?php
                $cntRead = 0; $cntCreate = 0; $cntUpdate = 0; $cntDelete = 0;
                foreach ($role['permissions'] as $p) {
                    if ($p['can_read'])   $cntRead++;
                    if ($p['can_create']) $cntCreate++;
                    if ($p['can_update']) $cntUpdate++;
                    if ($p['can_delete']) $cntDelete++;
                }
                ?>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light border text-dark" style="font-size:.65rem;" title="Halaman yang bisa dibuka">
                        <i class="fa-solid fa-eye me-1 text-info"></i>Read <?= $cntRead ?>
                    </span>
                    <span class="badge bg-light border text-dark" style="font-size:.65rem;" title="Halaman yang bisa create">
                        <i class="fa-solid fa-plus me-1 text-success"></i>Create <?= $cntCreate ?>
                    </span>
                    <span class="badge bg-light border text-dark" style="font-size:.65rem;" title="Halaman yang bisa edit">
                        <i class="fa-solid fa-pen me-1 text-warning"></i>Update <?= $cntUpdate ?>
                    </span>
                    <span class="badge bg-light border text-dark" style="font-size:.65rem;" title="Halaman yang bisa delete">
                        <i class="fa-solid fa-trash me-1 text-danger"></i>Delete <?= $cntDelete ?>
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mt-auto pt-2 border-top">
                    <?php if ($can_update ?? false): ?>
                    <a href="<?= base_url('admin/roles/edit/' . $slug) ?>"
                       class="btn btn-sm flex-grow-1 fw-semibold"
                       style="background:<?= $hex ?>18;color:<?= $hex ?>;border:1px solid <?= $hex ?>44;">
                        <i class="fa-solid fa-pen me-1"></i>Edit
                    </a>
                    <a href="<?= base_url('admin/roles/copy/' . $slug) ?>"
                       class="btn btn-sm btn-outline-secondary"
                       title="Duplikasi role ini"
                       onclick="return confirm('Duplikasi role <?= esc($role['title']) ?>?')">
                        <i class="fa-solid fa-copy"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!$role['is_system'] && ($can_delete ?? false)): ?>
                    <a href="<?= base_url('admin/roles/delete/' . $slug) ?>"
                       class="btn btn-sm btn-outline-danger"
                       title="Hapus role"
                       onclick="return confirm('Hapus role <?= esc($role['title']) ?>? User terkait akan dipindahkan ke role User.')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    <?php elseif ($role['is_system']): ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled title="Role sistem tidak bisa dihapus">
                        <i class="fa-solid fa-lock"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Peta Akses CRUD per Halaman -->
<div class="card border-0 shadow-sm">
    <div class="card-header py-3 border-bottom" style="background: var(--dark-coffee);">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                <i class="fa-solid fa-table-list me-2"></i>Peta Akses Halaman Admin
            </h6>
            <small style="color: var(--gold); opacity:.7;">C=Create &nbsp; R=Read &nbsp; U=Update &nbsp; D=Delete</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="pageRoleTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="min-width:200px;">Halaman</th>
                        <?php foreach ($roles as $slug => $role):
                            $hex = $hexMap[$role['color']] ?? '#6b7280';
                        ?>
                        <th class="text-center" style="min-width:140px;">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <span class="badge" style="background:<?= $hex ?>;font-size:.7rem;"><?= esc($role['title']) ?></span>
                                <small class="text-muted" style="font-size:.6rem;letter-spacing:.5px;">C&nbsp;R&nbsp;U&nbsp;D</small>
                            </div>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $pageKey => $page): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid <?= $page['icon'] ?> text-muted" style="width:18px;"></i>
                                <div>
                                    <div class="fw-semibold"><?= esc($page['label']) ?></div>
                                    <small class="text-muted"><?= $page['route'] ?></small>
                                </div>
                            </div>
                        </td>
                        <?php foreach ($roles as $slug => $role):
                            $p   = $allPerms[$slug][$pageKey] ?? [];
                            $cr  = !empty($p['can_create']);
                            $rd  = !empty($p['can_read']);
                            $up  = !empty($p['can_update']);
                            $dl  = !empty($p['can_delete']);
                            $hex = $hexMap[$role['color']] ?? '#6b7280';
                        ?>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <?php
                                $icons = [
                                    ['flag' => $cr,  'lbl' => 'C', 'title' => 'Create'],
                                    ['flag' => $rd,  'lbl' => 'R', 'title' => 'Read'],
                                    ['flag' => $up,  'lbl' => 'U', 'title' => 'Update'],
                                    ['flag' => $dl,  'lbl' => 'D', 'title' => 'Delete'],
                                ];
                                foreach ($icons as $ic):
                                    if ($ic['flag']):
                                ?>
                                <span class="crud-badge crud-on" title="<?= $ic['title'] ?>" style="color:<?= $hex ?>;"><?= $ic['lbl'] ?></span>
                                <?php else: ?>
                                <span class="crud-badge crud-off" title="No <?= $ic['title'] ?>"><?= $ic['lbl'] ?></span>
                                <?php endif; endforeach; ?>
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
            <span class="crud-badge crud-on me-1" style="color:#22c55e;">C</span> Aktif &nbsp;
            <span class="crud-badge crud-off me-1 ms-3">C</span> Tidak aktif
        </small>
    </div>
</div>

<style>
.role-card:hover { transform: translateY(-3px); }
.crud-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px; height: 22px;
    border-radius: 4px;
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: 0;
}
.crud-on  { background: currentColor; }
.crud-on  span, .crud-badge.crud-on  { color: inherit; }
.crud-badge.crud-on  { background: transparent; border: 1.5px solid currentColor; }
.crud-badge.crud-off { background: #f3f4f6; color: #d1d5db; border: 1.5px solid #e5e7eb; }
</style>

<?= $this->endSection() ?>
