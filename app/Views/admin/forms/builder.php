<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('admin/forms') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-sliders text-gold me-2"></i>Form Builder: <?= esc($form['title']) ?>
                </h4>
                <small class="text-muted">
                    Atur daftar pertanyaan, tipe input, petunjuk pengisian, dan pemetaan ke profil anggota.
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('form/' . $form['slug']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-eye me-1"></i>Preview Form
            </a>
            <a href="<?= base_url('admin/forms/' . $form['id'] . '/responses') ?>" class="btn btn-outline-success btn-sm">
                <i class="fa-solid fa-table-list me-1"></i>Lihat Respon
            </a>
            <button type="button" class="btn btn-gold btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalField" onclick="openFieldModal()">
                <i class="fa-solid fa-plus me-1"></i>Tambah Pertanyaan
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Questions List -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark-coffee">
                <i class="fa-solid fa-list-ol text-gold me-2"></i>Daftar Pertanyaan (<?= count($fields) ?> Pertanyaan)
            </h6>
            <span class="badge bg-light text-muted border">Urutan Tampil Sesuai Angka Sort</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($fields)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($fields as $idx => $f): ?>
                        <div class="list-group-item p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 hover-bg-light">
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-bold">
                                    #<?= $f['sort_order'] ?: ($idx + 1) ?>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark mb-1">
                                        <?= esc($f['field_label']) ?>
                                        <?php if (!empty($f['is_required'])): ?>
                                            <span class="badge bg-danger-subtle text-danger small ms-1">Wajib</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted small ms-1">Opsional</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="fa-solid fa-tag me-1"></i>Tipe: <?= strtoupper($f['field_type']) ?>
                                        </span>
                                        <span>&bull; Key: <code><?= esc($f['field_name']) ?></code></span>
                                        <?php if (!empty($f['map_to_member_field'])): ?>
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                                <i class="fa-solid fa-link me-1"></i>Link Anggota: <code><?= esc($f['map_to_member_field']) ?></code>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($f['default_value'])): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="fa-regular fa-circle-check me-1"></i>Default: <strong><?= esc($f['default_value']) ?></strong>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($f['help_text'])): ?>
                                        <small class="text-muted d-block mt-1 fst-italic">
                                            <i class="fa-regular fa-comment-dots me-1"></i><?= esc($f['help_text']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="btn-group btn-group-sm align-self-end align-self-md-center">
                                <button type="button" class="btn btn-outline-secondary" onclick='editField(<?= json_encode($f) ?>)' title="Edit Pertanyaan">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="<?= base_url('admin/forms/' . $form['id'] . '/fields/' . $f['id'] . '/delete') ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus pertanyaan ini?')" title="Hapus Pertanyaan">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa-solid fa-clipboard-question fa-3x mb-3 text-secondary opacity-50"></i>
                    <h6>Belum Ada Pertanyaan di Formulir Ini</h6>
                    <p class="small text-muted mb-3">Tambahkan pertanyaan pertama Anda untuk mulai mengumpulkan data dari responden.</p>
                    <button type="button" class="btn btn-gold btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalField" onclick="openFieldModal()">
                        <i class="fa-solid fa-plus me-1"></i>Tambah Pertanyaan Sekarang
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Add / Edit Field -->
<div class="modal fade" id="modalField" tabindex="-1" aria-labelledby="modalFieldLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-dark-coffee text-white border-0 py-3 px-4">
                <h5 class="modal-title fw-bold" id="modalFieldLabel">
                    <i class="fa-solid fa-circle-question text-gold me-2"></i>Pertanyaan Formulir
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/forms/' . $form['id'] . '/fields/save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="field_id" id="modal_field_id" value="0">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teks Pertanyaan / Label <span class="text-danger">*</span></label>
                        <input type="text" name="field_label" id="modal_field_label" class="form-control" placeholder="Contoh: Apakah kamu pernah merasakan nyeri dada saat berolahraga?" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipe Input <span class="text-danger">*</span></label>
                            <select name="field_type" id="modal_field_type" class="form-select" onchange="toggleOptionsInput(this.value)">
                                <option value="text">Teks Singkat (Text)</option>
                                <option value="textarea">Teks Panjang (Textarea / Paragraf)</option>
                                <option value="number">Angka / Angka Desimal (Number)</option>
                                <option value="date">Tanggal (Date)</option>
                                <option value="radio">Pilihan Tunggal (Radio - misal: Ya/Tidak)</option>
                                <option value="checkbox">Kotak Centang / Multi-pilihan (Checkbox)</option>
                                <option value="select">Menu Dropdown (Select)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Identifier Key (Field Name)</label>
                            <input type="text" name="field_name" id="modal_field_name" class="form-control" placeholder="Otomatis jika dikosongkan (contoh: nyeri_dada)">
                            <small class="text-muted">Hanya huruf kecil dan underscore (_).</small>
                        </div>
                    </div>

                    <!-- Options Container (Only for radio, checkbox, select) -->
                    <div class="mb-3" id="optionsContainer" style="display: none;">
                        <label class="form-label fw-semibold">Pilihan Jawaban</label>
                        <textarea name="field_options" id="modal_field_options" rows="3" class="form-control" placeholder="Tuliskan pilihan jawaban dipisah koma atau baris baru (contoh: Ya, Tidak)"></textarea>
                        <small class="text-muted">Contoh: <code>Ya, Tidak</code> atau setiap pilihan pada baris baru.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teks Petunjuk / Bantuan (Help Text)</label>
                        <input type="text" name="help_text" id="modal_help_text" class="form-control" placeholder="Contoh: Pilih salah satu jawaban yang paling menggambarkan kondisi Anda">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jawaban / Pilihan Default (Default Value)</label>
                        <input type="text" name="default_value" id="modal_default_value" class="form-control" placeholder="Contoh: Pancoran Mas">
                        <small class="text-muted">Jika diisi, opsi ini otomatis tercentang/terisi saat responden membuka form (responden tidak perlu memilih lagi jika sesuai).</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Petakan ke Database Anggota</label>
                            <select name="map_to_member_field" id="modal_map_to_member" class="form-select">
                                <option value="">-- Tidak Dipetakan (Data Bebas) --</option>
                                <option value="nama_lengkap">Nama Lengkap (Auto-match & Prefill)</option>
                                <option value="handphone">No HP / WhatsApp (Kunci Utama Auto-match)</option>
                                <option value="tanggal_lahir">Tanggal Lahir</option>
                                <option value="alamat_lengkap">Alamat Domisili</option>
                                <option value="kode_upa">Kode Group UPA</option>
                                <option value="nik">NIK / No KTP</option>
                            </select>
                            <small class="text-muted">Digunakan untuk auto-prefill saat login & auto-link saat tamu mengisi.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Urutan (Sort)</label>
                            <input type="number" name="sort_order" id="modal_sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check pb-2">
                                <input class="form-check-input" type="checkbox" name="is_required" id="modal_is_required" value="1">
                                <label class="form-check-label fw-semibold" for="modal_is_required">
                                    Wajib Diisi
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold fw-semibold px-4">
                        <i class="fa-solid fa-save me-1"></i>Simpan Pertanyaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function getFieldModal() {
    const el = document.getElementById('modalField');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        return bootstrap.Modal.getOrCreateInstance(el);
    }
    // Fallback if bootstrap object isn't ready
    return {
        show: () => $(el).modal('show'),
        hide: () => $(el).modal('hide')
    };
}

function openFieldModal() {
    document.getElementById('modalFieldLabel').innerHTML = '<i class="fa-solid fa-circle-plus text-gold me-2"></i>Tambah Pertanyaan Baru';
    document.getElementById('modal_field_id').value = '0';
    document.getElementById('modal_field_label').value = '';
    document.getElementById('modal_field_type').value = 'text';
    document.getElementById('modal_field_name').value = '';
    document.getElementById('modal_field_options').value = '';
    document.getElementById('modal_default_value').value = '';
    document.getElementById('modal_help_text').value = '';
    document.getElementById('modal_map_to_member').value = '';
    document.getElementById('modal_sort_order').value = '<?= count($fields) + 1 ?>';
    document.getElementById('modal_is_required').checked = false;
    toggleOptionsInput('text');
    getFieldModal().show();
}

function editField(field) {
    document.getElementById('modalFieldLabel').innerHTML = '<i class="fa-solid fa-pen text-gold me-2"></i>Edit Pertanyaan';
    document.getElementById('modal_field_id').value = field.id;
    document.getElementById('modal_field_label').value = field.field_label;
    document.getElementById('modal_field_type').value = field.field_type;
    document.getElementById('modal_field_name').value = field.field_name;

    // Decode options if json
    let opts = field.field_options;
    try {
        const parsed = JSON.parse(opts);
        if (Array.isArray(parsed)) {
            opts = parsed.join(', ');
        }
    } catch(e) {}
    document.getElementById('modal_field_options').value = opts || '';
    document.getElementById('modal_default_value').value = field.default_value || '';

    document.getElementById('modal_help_text').value = field.help_text || '';
    document.getElementById('modal_map_to_member').value = field.map_to_member_field || '';
    document.getElementById('modal_sort_order').value = field.sort_order || '0';
    document.getElementById('modal_is_required').checked = field.is_required == 1;

    toggleOptionsInput(field.field_type);
    getFieldModal().show();
}

function toggleOptionsInput(type) {
    const container = document.getElementById('optionsContainer');
    if (['radio', 'checkbox', 'select'].includes(type)) {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

$(document).ready(function() {
    const labelInput = document.getElementById('modal_field_label');
    const nameInput  = document.getElementById('modal_field_name');
    const mapSelect  = document.getElementById('modal_map_to_member');

    labelInput.addEventListener('input', function() {
        if (document.getElementById('modal_field_id').value === '0') {
            const raw = this.value.toString().toLowerCase().trim();
            nameInput.value = raw.replace(/\s+/g, '_').replace(/[^\w]/g, '').substring(0, 50);

            // Auto-detect mapping to member profile
            if (raw.includes('telepon') || raw.includes('telp') || raw.includes('hp') || raw.includes('handphone') || raw.includes('wa') || raw.includes('whatsapp')) {
                mapSelect.value = 'handphone';
            } else if (raw.includes('nama')) {
                mapSelect.value = 'nama_lengkap';
            } else if (raw.includes('nik') || raw.includes('ktp')) {
                mapSelect.value = 'nik';
            } else if (raw.includes('upa') || raw.includes('grup')) {
                mapSelect.value = 'kode_upa';
            } else if (raw.includes('alamat') || raw.includes('domisili')) {
                mapSelect.value = 'alamat_lengkap';
            } else if (raw.includes('lahir')) {
                mapSelect.value = 'tanggal_lahir';
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
