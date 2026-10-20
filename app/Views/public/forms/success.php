<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — NGOPI PANMAS</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-coffee: #2C1810;
            --gold: #D4AF37;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #efe8f9 0%, #f6f1ea 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-card {
            background: #ffffff;
            border-radius: 16px;
            border-top: 10px solid var(--gold);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            max-width: 580px;
            width: 100%;
            padding: 45px 35px;
            text-align: center;
        }
        .vintage-serif {
            font-family: 'Playfair Display', serif;
        }
        .btn-coffee {
            background: var(--dark-coffee);
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
        }
        .btn-coffee:hover {
            background: #4a2817;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 80px; height: 80px;">
            <i class="fa-solid fa-circle-check fa-3x"></i>
        </div>
    </div>

    <h3 class="vintage-serif fw-bold mb-2" style="color: var(--dark-coffee);">Jawaban Berhasil Terkirim!</h3>
    <p class="text-muted mb-4" style="font-size: 0.95rem;">
        <?= esc($form['title']) ?>
    </p>

    <div class="p-3 mb-4 rounded-3 text-start" style="background: #fdfbf7; border: 1px solid #faeccd;">
        <p class="mb-0 text-dark" style="font-size: 0.92rem; line-height: 1.6;">
            <i class="fa-solid fa-quote-left text-gold me-2"></i>
            <?= nl2br(esc($form['success_message'] ?: 'Terima kasih, data dan respon Anda telah berhasil disimpan di sistem.')) ?>
        </p>
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 pt-2">
        <a href="<?= base_url('form/' . $form['slug']) ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-rotate-left me-2"></i>Kirim Jawaban Lain
        </a>
        <a href="<?= base_url('/') ?>" class="btn btn-coffee">
            <i class="fa-solid fa-house me-2"></i>Kembali ke Beranda
        </a>
    </div>

    <div class="text-muted small mt-5 pt-3 border-top">
        NGOPI PANMAS &bull; Sistem Pendataan Komunitas
    </div>
</div>

</body>
</html>
