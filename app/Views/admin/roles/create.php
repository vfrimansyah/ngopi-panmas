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
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/roles') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-plus-circle me-2 text-gold"></i>Tambah Role Baru
        </h4>
        <p class="text-muted mb-0 small">Buat role custom dengan permissions CRUD per halaman.</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="<?= base_url('admin/roles/store') ?>">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Kiri: Info Role -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px;">
                <div class="card-header py-3" style="background: var(--dark-coffee);">
                    <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                        <i class="fa-solid fa-id-badge me-2"></i>Informasi Role
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="roleTitle"
                               class="form-control" placeholder="Contoh: Koordinator"
                               value="<?= old('title') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Slug / ID Role <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="roleSlug"
                               class="form-control font-monospace" placeholder="koordinator"
                               value="<?= old('slug') ?>" required>
                        <small class="text-muted">Hanya huruf kecil, angka, dan underscore (_)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Deskripsi singkat tentang role ini..."><?= old('description') ?></textarea>
                    </div>

                    <!-- Warna -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Warna</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($colorOptions as $key => $opt): ?>
                            <label class="color-pick" title="<?= $opt['label'] ?>">
                                <input type="radio" name="color" value="<?= $key ?>"
                                       <?= old('color', 'secondary') === $key ? 'checked' : '' ?> hidden>
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
                                       <?= old('icon', 'fa-circle-user') === $iconClass ? 'checked' : '' ?> hidden>
                                <span class="icon-dot">
                                    <i class="fa-solid <?= $iconClass ?>"></i>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Preset -->
                    <div class="mb-3 pt-2 border-top">
                        <label class="form-label fw-semibold small">Salin Permissions dari Role</label>
                        <div class="d-flex gap-2">
                            <select id="presetRole" class="form-select form-select-sm">
                                <option value="">— pilih template —</option>
                                <option value="admin">Super Admin</option>
                                <option value="bph">BPH</option>
                                <option value="kaderisasi">Kaderisasi</option>
                                <option value="seksi">Seksi</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="applyPreset()">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-grid">
                    <button type="submit" class="btn fw-semibold"
                            style="background:#3d1f0f;color:#fff;border:none;">
                        <i class="fa-solid fa-save me-2"></i>Simpan Role
                    </button>
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
                                <i class="fa-solid fa-check-double me-1"></i>Centang Semua
                            </button>
                            <button type="button" class="btn btn-sm" style="background:#ffffff22;color:var(--gold);" onclick="toggleAll(false)">
                                <i class="fa-solid fa-xmark me-1"></i>Hapus Semua
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="permMatrix">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:40%">Halaman</th>
                                    <th class="text-center">
                                        <span class="badge bg-info text-white">Read</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Lihat/Buka</small>
                                    </th>
                                    <th class="text-center">
                                        <span class="badge bg-success text-white">Create</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Tambah</small>
                                    </th>
                                    <th class="text-center">
                                        <span class="badge bg-warning text-dark">Update</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Edit</small>
                                    </th>
                                    <th class="text-center">
                                        <span class="badge bg-danger text-white">Delete</span>
                                        <small class="d-block text-muted" style="font-size:.6rem;">Hapus</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages as $pageKey => $page): ?>
                                <tr class="perm-row" data-page="<?= $pageKey ?>">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid <?= $page['icon'] ?> text-muted" style="width:18px;"></i>
                                            <div>
                                                <div class="fw-semibold small"><?= esc($page['label']) ?></div>
                                                <small class="text-muted"><?= $page['route'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <?php foreach (['read' => 'info', 'create' => 'success', 'update' => 'warning', 'delete' => 'danger'] as $op => $cls): ?>
                                    <td class="text-center">
                                        <div class="form-check d-inline-flex">
                                            <input class="form-check-input perm-cb" type="checkbox"
                                                   name="perm_<?= $pageKey ?>_<?= $op ?>"
                                                   id="perm_<?= $pageKey ?>_<?= $op ?>"
                                                   value="1"
                                                   data-page="<?= $pageKey ?>"
                                                   data-op="<?= $op ?>"
                                                   style="border-color: var(--bs-<?= $cls ?>); cursor:pointer;"
                                                   <?= old("perm_{$pageKey}_{$op}") ? 'checked' : '' ?>>
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
.perm-cb:checked { background-color: var(--bs-success); border-color: var(--bs-success); }
</style>

<script>
// Auto-generate slug dari title
document.getElementById('roleTitle').addEventListener('input', function() {
    const slugField = document.getElementById('roleSlug');
    if (!slugField.dataset.manual) {
        slugField.value = this.value.toLowerCase()
            .replace(/[^a-z0-9\s_]/g, '')
            .replace(/\s+/g, '_');
    }
});
document.getElementById('roleSlug').addEventListener('input', function() {
    this.dataset.manual = '1';
    this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
});

// Toggle semua checkbox
function toggleAll(state) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = state);
}

// Data preset permissions (dari controller defaults)
const presets = <?= json_encode($presets ?? []) ?>;

// Terapkan preset
function applyPreset() {
    const role = document.getElementById('presetRole').value;
    if (!role || !presets[role]) return;

    document.querySelectorAll('.perm-cb').forEach(cb => {
        const page = cb.dataset.page;
        const op   = cb.dataset.op;
        const key  = op === 'read'   ? 'can_read'
                   : op === 'create' ? 'can_create'
                   : op === 'update' ? 'can_update'
                   : 'can_delete';
        if (presets[role] && presets[role][page]) {
            cb.checked = presets[role][page][key] == 1;
        } else {
            cb.checked = false;
        }
    });
}
</script>

<?= $this->endSection() ?>
