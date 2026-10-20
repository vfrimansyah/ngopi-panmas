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
        .closed-card {
            background: #ffffff;
            border-radius: 16px;
            border-top: 10px solid #dc3545;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            max-width: 540px;
            width: 100%;
            padding: 45px 35px;
            text-align: center;
        }
        .vintage-serif {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body>

<div class="closed-card">
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 80px; height: 80px;">
            <i class="fa-solid fa-lock fa-3x"></i>
        </div>
    </div>

    <h3 class="vintage-serif fw-bold mb-2" style="color: var(--dark-coffee);"><?= esc($form['title']) ?></h3>
    
    <?php if (!empty($isExpired)): ?>
        <div class="badge bg-danger mb-4 px-3 py-2"><i class="fa-solid fa-hourglass-end me-1"></i>Batas Waktu Telah Berakhir</div>
        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
            Mohon maaf, batas akhir pengisian formulir ini telah terlewati pada <strong><?= date('d M Y, H:i', strtotime($form['closed_at'])) ?> WIB</strong> sehingga formulir tidak lagi menerima respon baru.
        </p>
    <?php else: ?>
        <div class="badge bg-secondary mb-4 px-3 py-2"><i class="fa-solid fa-lock me-1"></i>Formulir Telah Ditutup</div>
        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
            Mohon maaf, penerimaan respon untuk formulir ini saat ini sedang tidak aktif atau telah ditutup oleh pengurus.
        </p>
    <?php endif; ?>

    <a href="<?= base_url('/') ?>" class="btn btn-outline-dark px-4">
        <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Beranda
    </a>
</div>

</body>
</html>
