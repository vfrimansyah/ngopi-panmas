<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password — Ngopi Panmas Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-coffee: #2c1810;
            --gold: #c8a97e;
            --cream: #f5f0e8;
        }
        * { font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(135deg, #2c1810 0%, #4a2c1e 50%, #1a0f0a 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .card {
            border-radius: 1.25rem;
            border: 1px solid rgba(200,169,126,0.15);
            backdrop-filter: blur(8px);
        }
        .badge-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--dark-coffee), #6b3d2a);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 24px rgba(200,169,126,0.3);
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(200,169,126,0.25);
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--dark-coffee), #6b3d2a);
            color: #fff;
            border: none;
            padding: 0.65rem 2rem;
            border-radius: 0.6rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3d1f12, #8b4d38);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        }
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s;
            background: #e5e7eb;
        }
        .strength-bar.weak    { background: #ef4444; width: 33%; }
        .strength-bar.medium  { background: #f59e0b; width: 66%; }
        .strength-bar.strong  { background: #22c55e; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="badge-icon">
                        <i class="fa-solid fa-key fa-2x" style="color: var(--gold)"></i>
                    </div>
                    <h4 class="fw-bold text-white mb-1">Ganti Password</h4>
                    <p class="text-white-50 small">
                        Akun Anda menggunakan password default. <br>
                        Silakan buat password baru yang aman.
                    </p>
                </div>

                <!-- Alert -->
                <?php if (session('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0">
                        <?php foreach ((array) session('errors') as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <!-- Warning -->
                        <div class="alert alert-warning d-flex align-items-start gap-2 py-2 mb-4 rounded-3 border-0 bg-warning-subtle">
                            <i class="fa-solid fa-triangle-exclamation text-warning mt-1"></i>
                            <div class="small">
                                <strong>Password Default Terdeteksi</strong><br>
                                Anda login menggunakan password default <code>P4nm4s</code>. 
                                Buat password baru yang kuat sebelum melanjutkan.
                            </div>
                        </div>

                        <form action="<?= base_url('admin/change-password') ?>" method="POST" id="changePassForm">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="newPassword" class="form-control"
                                           placeholder="Minimal 6 karakter" required minlength="6"
                                           autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" id="toggleNew">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <!-- Strength indicator -->
                                <div class="mt-2">
                                    <div class="strength-bar" id="strengthBar"></div>
                                    <small class="text-muted" id="strengthLabel"></small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" name="pass_confirm" id="confirmPassword" class="form-control"
                                           placeholder="Ulangi password baru" required minlength="6"
                                           autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirm">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div id="matchMsg" class="small mt-1"></div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-lock me-2"></i>Simpan Password Baru
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Login sebagai <strong><?= esc(auth()->user()->username) ?></strong> &bull;
                                <a href="<?= base_url('logout') ?>" class="text-danger text-decoration-none">Logout</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('toggleNew').addEventListener('click', function() {
            const input = document.getElementById('newPassword');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
        document.getElementById('toggleConfirm').addEventListener('click', function() {
            const input = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Password strength
        document.getElementById('newPassword').addEventListener('input', function() {
            const val = this.value;
            const bar = document.getElementById('strengthBar');
            const lbl = document.getElementById('strengthLabel');
            bar.className = 'strength-bar';
            if (val.length === 0) { lbl.textContent = ''; return; }
            const hasUpper = /[A-Z]/.test(val);
            const hasLower = /[a-z]/.test(val);
            const hasNum   = /[0-9]/.test(val);
            const hasSpec  = /[^A-Za-z0-9]/.test(val);
            const score = [val.length >= 8, hasUpper, hasLower, hasNum, hasSpec].filter(Boolean).length;
            if (score <= 2) { bar.classList.add('weak'); lbl.textContent = 'Lemah'; lbl.style.color = '#ef4444'; }
            else if (score <= 3) { bar.classList.add('medium'); lbl.textContent = 'Sedang'; lbl.style.color = '#f59e0b'; }
            else { bar.classList.add('strong'); lbl.textContent = 'Kuat'; lbl.style.color = '#22c55e'; }
        });

        // Match check
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const pass = document.getElementById('newPassword').value;
            const msg = document.getElementById('matchMsg');
            if (this.value === pass) {
                msg.innerHTML = '<i class="fa-solid fa-check text-success me-1"></i><span class="text-success">Password cocok</span>';
            } else {
                msg.innerHTML = '<i class="fa-solid fa-times text-danger me-1"></i><span class="text-danger">Password tidak cocok</span>';
            }
        });
    </script>
</body>
</html>
