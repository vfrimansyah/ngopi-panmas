<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-list-check me-2 text-gold"></i><?= isset($menu) ? 'Edit Menu' : 'Tambah Menu Baru' ?>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-secondary text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/menus') ?>" class="text-secondary text-decoration-none">Kelola Menu</a></li>
                <li class="breadcrumb-item active text-dark-coffee fw-medium" aria-current="page"><?= isset($menu) ? 'Edit' : 'Tambah' ?></li>
            </ol>
        </nav>
    </div>
    <a href="<?= base_url('admin/menus') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- ERROR VALIDASI -->
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger border-0 shadow-sm" role="alert" style="border-radius: 8px;">
        <h6 class="fw-bold"><i class="fa-solid fa-circle-xmark me-2"></i>Perbaiki Kesalahan Berikut:</h6>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #ffffff;">
    <div class="card-body p-4">
        <form action="<?= isset($menu) ? base_url('admin/menus/update/' . $menu['id']) : base_url('admin/menus/store') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="row g-3">
                <!-- 1. Title -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Judul Menu <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Kelola Anggota, Kategori" value="<?= old('title', $menu['title'] ?? '') ?>" required>
                    <div class="form-text">Nama menu yang akan tampil di sidebar.</div>
                </div>

                <!-- 2. Parent Menu -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Parent Menu (Menu Utama)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- Tidak Ada (Jadikan Menu Utama) --</option>
                        <?php foreach ($parents as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= old('parent_id', $menu['parent_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Pilih jika menu ini ingin dijadikan sub-menu di dalam menu utama.</div>
                </div>

                <!-- 3. Icon -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark-coffee">Ikon FontAwesome</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-icons"></i></span>
                        <input type="text" name="icon" class="form-control" placeholder="Contoh: fa-solid fa-users" value="<?= old('icon', $menu['icon'] ?? '') ?>">
                    </div>
                    <div class="form-text">Gunakan class FontAwesome 6 (kosongkan jika sub-menu).</div>
                </div>

                <!-- 4. URL Path -->
                <div class="col-md-5">
                    <label class="form-label fw-bold text-dark-coffee">URL Path</label>
                    <input type="text" name="url" class="form-control" placeholder="Contoh: admin/members (kosongkan jika parent wrapper)" value="<?= old('url', $menu['url'] ?? '') ?>">
                    <div class="form-text">Path URL yang dipanggil saat diklik. Kosongkan jika hanya pembungkus sub-menu.</div>
                </div>

                <!-- 5. Sort Order -->
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark-coffee">No Urut Tampil <span class="text-danger">*</span></label>
                    <input type="number" name="sort_order" class="form-control" value="<?= old('sort_order', $menu['sort_order'] ?? 0) ?>" required>
                    <div class="form-text">Urutan menu dari atas ke bawah.</div>
                </div>

                <!-- 6. Access Roles -->
                <div class="col-12 mt-4">
                    <label class="form-label fw-bold text-dark-coffee d-block">Akses Role (Shield Group) <span class="text-danger">*</span></label>
                    <div class="d-flex gap-4 flex-wrap">
                        <?php foreach ($groups as $g): 
                            $isChecked = isset($existingRoles) ? in_array($g, $existingRoles) : true; // Default checked for new menu
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $g ?>" id="role_<?= $g ?>" <?= $isChecked ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold text-capitalize" for="role_<?= $g ?>">
                                    <?= esc($g) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-text mt-2 text-danger">Minimal pilih 1 grup/role agar menu ini dapat diakses oleh user.</div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-gold px-4">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Menu
                </button>
                <a href="<?= base_url('admin/menus') ?>" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
            </div>
        </form>
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
    .text-gold {
        color: #d4af37 !important;
    }
</style>
<?= $this->endSection() ?>
