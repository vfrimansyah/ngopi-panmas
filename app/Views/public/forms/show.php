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
            --light-cream: #FFFDF9;
            --warm-gray: #F8F5F0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0ebf8;
            background: linear-gradient(135deg, #efe8f9 0%, #f6f1ea 100%);
            min-height: 100vh;
            color: #333333;
            padding: 30px 15px 60px 15px;
        }
        .form-container {
            max-width: 680px;
            margin: 0 auto;
        }
        .header-card {
            background: #ffffff;
            border-radius: 12px;
            border-top: 10px solid var(--dark-coffee);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 28px 32px;
            margin-bottom: 16px;
        }
        .question-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2dbd0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            padding: 24px 28px;
            margin-bottom: 16px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .question-card:focus-within {
            border-color: var(--gold);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.15);
        }
        .vintage-serif {
            font-family: 'Playfair Display', serif;
        }
        .btn-gold {
            background-color: var(--gold);
            color: #ffffff;
            border: none;
            padding: 10px 28px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-gold:hover {
            background-color: #b8972c;
            color: #ffffff;
            transform: translateY(-1px);
        }
        .radio-option {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #e8e3dc;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .radio-option:hover {
            background-color: #fdfbf7;
            border-color: var(--gold);
        }
        .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }
        .account-badge {
            background: #fdfbf7;
            border: 1px solid #eaddc7;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 0.88rem;
        }
    </style>
</head>
<body>

<div class="form-container">
    <!-- Header Card -->
    <div class="header-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge" style="background: var(--gold); color: #fff; font-weight: 500; font-size: 0.8rem;">
                <i class="fa-solid fa-mug-hot me-1"></i>Indonesia Ngopi
            </span>
            <small class="text-muted"><i class="fa-solid fa-shield-halved me-1"></i>PANMAS Official</small>
        </div>
        <h2 class="vintage-serif fw-bold mb-2" style="color: var(--dark-coffee);"><?= esc($form['title']) ?></h2>
        <?php if (!empty($form['description'])): ?>
            <p class="text-secondary mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                <?= nl2br(esc($form['description'])) ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($form['closed_at'])): ?>
            <div class="p-2 px-3 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: #fff8e6; border: 1px solid #fae19c;">
                <div class="small text-dark">
                    <i class="fa-regular fa-clock text-warning me-1"></i>
                    Batas Akhir Pengisian: <strong><?= date('d M Y, H:i', strtotime($form['closed_at'])) ?> WIB</strong>
                </div>
                <span class="badge bg-warning text-dark small" style="font-size: 0.75rem;">Batas Waktu</span>
            </div>
        <?php endif; ?>

        <?php if ($isLoggedIn && $currentMember): ?>
            <div class="account-badge mb-3 d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa-solid fa-circle-user text-gold me-2"></i>
                    Mengisi sebagai: <strong><?= esc($currentMember['nama_lengkap']) ?></strong>
                    <span class="text-muted small ms-1">(UPA: <?= esc($currentMember['kode_upa'] ?? '-') ?>)</span>
                </div>
                <span class="badge bg-success-subtle text-success small">Terverifikasi</span>
            </div>
        <?php elseif ($isLoggedIn): ?>
            <div class="account-badge mb-3">
                <i class="fa-solid fa-user me-2 text-muted"></i>
                Login sebagai: <strong><?= esc(auth()->user()->username) ?></strong>
            </div>
        <?php endif; ?>

        <hr class="my-3" style="border-color: #eee;">
        <div class="text-danger small">
            * Menunjukkan pertanyaan yang wajib diisi
        </div>
    </div>

    <!-- Errors Flash -->
    <?php if (session('errors')): ?>
        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-3 p-3">
            <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Mohon lengkapi data berikut:</div>
            <ul class="mb-0 ps-3 small">
                <?php foreach (session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Questions Form -->
    <form action="<?= base_url('form/' . $form['slug']) ?>" method="POST">
        <?= csrf_field() ?>

        <?php foreach ($fields as $idx => $f): ?>
            <?php
                $fieldName = $f['field_name'];
                $mappedKey = $f['map_to_member_field'];
                $defaultVal = $f['default_value'] ?? '';

                // Prefill logic from member profile takes priority if logged in
                if (!empty($mappedKey) && !empty($prefill[$mappedKey])) {
                    $defaultVal = $prefill[$mappedKey];
                }
                $val = old($fieldName, $defaultVal);
            ?>

            <div class="question-card">
                <label class="form-label fw-semibold d-block mb-1" style="font-size: 1rem; color: #2d2d2d;">
                    <?= esc($f['field_label']) ?>
                    <?php if (!empty($f['is_required'])): ?>
                        <span class="text-danger ms-1">*</span>
                    <?php endif; ?>
                </label>

                <?php if (!empty($f['help_text'])): ?>
                    <small class="text-muted d-block mb-2" style="font-size: 0.83rem;">
                        <i class="fa-regular fa-circle-question me-1"></i><?= esc($f['help_text']) ?>
                    </small>
                <?php endif; ?>

                <!-- Field Type Rendering -->
                <?php if ($f['field_type'] === 'text'): ?>
                    <input type="text"
                           name="<?= esc($fieldName) ?>"
                           class="form-control"
                           value="<?= esc($val) ?>"
                           placeholder="Jawaban Anda"
                           <?= !empty($f['is_required']) ? 'required' : '' ?>>

                <?php elseif ($f['field_type'] === 'number'): ?>
                    <input type="number"
                           step="any"
                           name="<?= esc($fieldName) ?>"
                           class="form-control"
                           value="<?= esc($val) ?>"
                           placeholder="0"
                           <?= !empty($f['is_required']) ? 'required' : '' ?>>

                <?php elseif ($f['field_type'] === 'date'): ?>
                    <input type="date"
                           name="<?= esc($fieldName) ?>"
                           class="form-control"
                           value="<?= esc($val) ?>"
                           <?= !empty($f['is_required']) ? 'required' : '' ?>>

                <?php elseif ($f['field_type'] === 'textarea'): ?>
                    <textarea name="<?= esc($fieldName) ?>"
                              rows="3"
                              class="form-control"
                              placeholder="Jawaban Anda"
                              <?= !empty($f['is_required']) ? 'required' : '' ?>><?= esc($val) ?></textarea>

                <?php elseif ($f['field_type'] === 'radio'): ?>
                    <?php
                        $opts = json_decode($f['field_options'] ?? '[]', true);
                        if (!is_array($opts)) {
                            $opts = ['Ya', 'Tidak'];
                        }
                    ?>
                    <div class="mt-2">
                        <?php foreach ($opts as $oIdx => $opt): ?>
                            <label class="radio-option d-flex align-items-center w-100 mb-2">
                                <input class="form-check-input me-3 my-0"
                                       type="radio"
                                       name="<?= esc($fieldName) ?>"
                                       id="<?= esc($fieldName . '_' . $oIdx) ?>"
                                       value="<?= esc($opt) ?>"
                                       <?= (trim((string)$val) === trim((string)$opt)) ? 'checked' : '' ?>
                                       <?= !empty($f['is_required']) ? 'required' : '' ?>>
                                <span class="form-check-label"><?= esc($opt) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($f['field_type'] === 'checkbox'): ?>
                    <?php
                        $opts = json_decode($f['field_options'] ?? '[]', true) ?: [];
                        $checkedVals = is_array($val) ? $val : (json_decode($val, true) ?: array_map('trim', explode(',', (string)$val)));
                    ?>
                    <div class="mt-2">
                        <?php foreach ($opts as $oIdx => $opt): ?>
                            <label class="radio-option d-flex align-items-center w-100 mb-2">
                                <input class="form-check-input me-3 my-0"
                                       type="checkbox"
                                       name="<?= esc($fieldName) ?>[]"
                                       id="<?= esc($fieldName . '_' . $oIdx) ?>"
                                       value="<?= esc($opt) ?>"
                                       <?= in_array(trim((string)$opt), array_map('trim', $checkedVals)) ? 'checked' : '' ?>>
                                <span class="form-check-label"><?= esc($opt) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($f['field_type'] === 'select'): ?>
                    <?php
                        $opts = json_decode($f['field_options'] ?? '[]', true) ?: [];
                    ?>
                    <select name="<?= esc($fieldName) ?>"
                            class="form-select"
                            <?= !empty($f['is_required']) ? 'required' : '' ?>>
                        <option value="">-- Pilih Salah Satu --</option>
                        <?php foreach ($opts as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= (trim((string)$val) === trim((string)$opt)) ? 'selected' : '' ?>>
                                <?= esc($opt) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Submit Buttons -->
        <div class="d-flex align-items-center justify-content-between pt-2">
            <button type="submit" class="btn btn-gold btn-lg px-5 shadow-sm">
                <i class="fa-solid fa-paper-plane me-2"></i>Kirim Jawaban
            </button>
            <button type="reset" class="btn btn-link text-muted text-decoration-none small">
                Kosongkan formulir
            </button>
        </div>
    </form>

    <!-- Footer -->
    <div class="text-center mt-5 text-muted small">
        <p class="mb-1">Formulir Resmi Komunitas NGOPI PANMAS</p>
        <p class="mb-0">&copy; <?= date('Y') ?> PANMAS. Seluruh hak cipta dilindungi.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
