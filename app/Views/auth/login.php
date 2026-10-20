<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Ngobrol Pikiran Indonesia</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/images/logo.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo.png') ?>">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #2C1A0E;
            --primary-coffee: #6F4E37;
            --cream: #F5E6D3;
            --gold: #C8A97E;
            --warm-white: #FFF8F0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(44, 26, 14, 0.9), rgba(44, 26, 14, 0.9)), url('https://images.unsplash.com/photo-1507133750040-4a8f57021571?auto=format&fit=crop&q=80&w=1000') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background-color: var(--warm-white);
            border: 2px solid var(--gold);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }
        .login-header {
            background-color: var(--primary-dark);
            color: var(--gold);
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid var(--gold);
        }
        .login-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control {
            border: 1px solid var(--cream);
            border-radius: 8px;
            padding: 12px;
            background-color: #ffffff;
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(200, 169, 126, 0.25);
        }
        .btn-coffee {
            background-color: var(--primary-coffee);
            color: var(--warm-white);
            font-weight: 600;
            padding: 12px;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-coffee:hover {
            background-color: var(--primary-dark);
            color: var(--gold);
        }
        .text-coffee {
            color: var(--primary-coffee);
        }
        .text-coffee:hover {
            color: var(--primary-dark);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <a href="<?= base_url() ?>" class="d-inline-block mb-2">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="NGOPI" style="height: 48px; width: auto; background: #ffffff; padding: 4px 10px; border-radius: 8px;">
        </a>
        <p class="mb-0 text-cream small text-uppercase" style="letter-spacing: 2px;">Selamat Datang Kembali</p>
    </div>
    <div class="login-body">
        <?php if (session('error') !== null) : ?>
            <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
        <?php elseif (session('errors') !== null) : ?>
            <div class="alert alert-danger" role="alert">
                <?php if (is_array(session('errors'))) : ?>
                    <?php foreach (session('errors') as $error) : ?>
                        <?= $error ?><br>
                    <?php endforeach ?>
                <?php else : ?>
                    <?= session('errors') ?>
                <?php endif ?>
            </div>
        <?php endif ?>

        <?php if (session('message') !== null) : ?>
            <div class="alert alert-success" role="alert"><?= session('message') ?></div>
        <?php endif ?>

        <form action="<?= url_to('login') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Email -->
            <div class="mb-3">
                <label for="floatingEmailInput" class="form-label">Alamat Email</label>
                <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" placeholder="nama@email.com" value="<?= old('email') ?>" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="floatingPasswordInput" class="form-label">Kata Sandi</label>
                <input type="password" class="form-control" id="floatingPasswordInput" name="password" inputmode="text" placeholder="Masukkan kata sandi" required>
            </div>

            <!-- Remember me -->
            <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberCheck" <?= old('remember') ? 'checked' : '' ?>>
                    <label class="form-check-label text-muted small" for="rememberCheck">
                        Ingat Saya di Perangkat Ini
                    </label>
                </div>
            <?php endif; ?>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-coffee w-100">Masuk Sekarang</button>
            </div>

            <p class="text-center text-muted small mb-0">Belum punya akun? <a href="<?= base_url('register') ?>" class="text-coffee fw-bold text-decoration-none">Daftar Akun Baru</a></p>
        </form>
    </div>
</div>

</body>
</html>
