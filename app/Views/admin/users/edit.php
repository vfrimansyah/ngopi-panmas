<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)">Edit User</h4>
                <small class="text-muted">Ubah data, role, atau password user <strong><?= esc($user['username']) ?></strong>.</small>
            </div>
        </div>

        <?php if (session('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach ((array) session('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom" style="background: var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                    <i class="fa-solid fa-pen me-2"></i>Edit Informasi User
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control"
                               value="<?= old('username', $user['username']) ?>"
                               required minlength="3" maxlength="30">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="<?= old('email', $user['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Role & Hak Akses <span class="text-danger">*</span></label>
                        <?php
                            $userGroups = is_array($user['groups']) ? $user['groups'] : explode(',', $user['groups']);
                        ?>
                        <div class="row g-2 mb-2">
                            <?php foreach ($roles as $key => $label): ?>
                                <div class="col-md-6">
                                    <div class="form-check p-2 border rounded bg-light d-flex align-items-center">
                                        <input class="form-check-input ms-1 me-2" type="checkbox" name="roles[]" value="<?= $key ?>" id="role_<?= $key ?>"
                                            <?= in_array($key, (array) old('roles', $userGroups)) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold text-dark small mb-0 w-100" for="role_<?= $key ?>">
                                            <?= esc($label) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text">Centang satu atau beberapa role sekaligus untuk memberikan kombinasi hak akses.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Akun</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active" id="activeSwitch" value="1"
                                   <?= $user['active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activeSwitch">Akun Aktif</label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Kosongkan jika tidak ingin mengubah password"
                               minlength="6" autocomplete="new-password">
                        <div class="form-text">Jika diisi, user akan diminta ganti password saat login berikutnya.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-4" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
