<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)">Tambah User Baru</h4>
                <small class="text-muted">User akan mendapat password default <code>P4nm4s</code> dan wajib menggantinya saat pertama login.</small>
            </div>
        </div>

        <?php if (session('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach (session('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom" style="background: var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                    <i class="fa-solid fa-user-plus me-2"></i>Informasi User Baru
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/users/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control"
                               value="<?= old('username') ?>"
                               placeholder="Contoh: budi.santoso"
                               required minlength="3" maxlength="30">
                        <div class="form-text">Minimal 3 karakter, tanpa spasi.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="<?= old('email') ?>"
                               placeholder="contoh@email.com" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Role & Hak Akses <span class="text-danger">*</span></label>
                        <div class="row g-2 mb-2">
                            <?php foreach ($roles as $key => $label): ?>
                                <div class="col-md-6">
                                    <div class="form-check p-2 border rounded bg-light d-flex align-items-center">
                                        <input class="form-check-input ms-1 me-2" type="checkbox" name="roles[]" value="<?= $key ?>" id="role_<?= $key ?>"
                                            <?= (is_array(old('roles')) && in_array($key, old('roles'))) || (empty(old('roles')) && $key === 'user') ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold text-dark small mb-0 w-100" for="role_<?= $key ?>">
                                            <?= esc($label) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text">User dapat memiliki lebih dari satu role sekaligus (Multi-Role).</div>
                    </div>

                    <!-- Info password default -->
                    <div class="alert alert-info d-flex align-items-start gap-2 py-2 mb-4">
                        <i class="fa-solid fa-key mt-1 text-info"></i>
                        <div>
                            <strong>Password Default:</strong> <code>P4nm4s</code><br>
                            <small class="text-muted">User wajib mengganti password ini saat pertama kali login.</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-4" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i>Simpan User
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
