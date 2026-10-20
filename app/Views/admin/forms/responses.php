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
                    <i class="fa-solid fa-table-list text-gold me-2"></i>Respon: <?= esc($form['title']) ?>
                </h4>
                <small class="text-muted">
                    Daftar responden dan status keterhubungan ke data anggota di portal.
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/forms/' . $form['id'] . '/export') ?>" class="btn btn-success btn-sm fw-semibold shadow-sm">
                <i class="fa-solid fa-file-excel me-1"></i>Ekspor ke Excel (.xlsx)
            </a>
            <a href="<?= base_url('admin/forms/' . $form['id'] . '/builder') ?>" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-sliders me-1"></i>Edit Pertanyaan
            </a>
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

    <!-- Summary KPI Cards -->
    <?php
        $totalResponses = count($responses);
        $autoLinkedCount = 0;
        $manualLinkedCount = 0;
        $unlinkedCount = 0;
        foreach ($responses as $r) {
            if ($r['link_status'] === 'auto_linked') $autoLinkedCount++;
            elseif ($r['link_status'] === 'manual_linked') $manualLinkedCount++;
            else $unlinkedCount++;
        }
    ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block mb-1">Total Respon Masuk</small>
                <h3 class="fw-bold text-dark-coffee mb-0"><?= $totalResponses ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
                <small class="text-muted d-block mb-1">Auto-Linked (Cocok No HP/NIK)</small>
                <h3 class="fw-bold text-success mb-0"><?= $autoLinkedCount ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary">
                <small class="text-muted d-block mb-1">Manual Linked (Oleh Admin)</small>
                <h3 class="fw-bold text-primary mb-0"><?= $manualLinkedCount ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
                <small class="text-muted d-block mb-1">Tamu / Belum Tertaut</small>
                <h3 class="fw-bold text-warning mb-0"><?= $unlinkedCount ?></h3>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="small fw-semibold text-muted">Filter Status:</span>
        <a href="<?= base_url('admin/forms/' . $form['id'] . '/responses') ?>" class="btn btn-sm <?= empty($statusFilter) ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Semua (<?= $totalResponses ?>)
        </a>
        <a href="<?= base_url('admin/forms/' . $form['id'] . '/responses?status=auto_linked') ?>" class="btn btn-sm <?= $statusFilter === 'auto_linked' ? 'btn-success' : 'btn-outline-success' ?>">
            Auto Linked (<?= $autoLinkedCount ?>)
        </a>
        <a href="<?= base_url('admin/forms/' . $form['id'] . '/responses?status=manual_linked') ?>" class="btn btn-sm <?= $statusFilter === 'manual_linked' ? 'btn-primary' : 'btn-outline-primary' ?>">
            Manual Linked (<?= $manualLinkedCount ?>)
        </a>
        <a href="<?= base_url('admin/forms/' . $form['id'] . '/responses?status=unlinked') ?>" class="btn btn-sm <?= $statusFilter === 'unlinked' ? 'btn-warning' : 'btn-outline-warning' ?>">
            Belum Tertaut (<?= $unlinkedCount ?>)
        </a>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Waktu Kirim</th>
                            <th>Responden Form</th>
                            <th>No. Handphone / WA</th>
                            <th>Status Tautan Anggota</th>
                            <th class="text-end" style="min-width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($responses)): ?>
                            <?php foreach ($responses as $idx => $r): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($r['created_at'])) ?> WIB</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark-coffee"><?= esc($r['respondent_name'] ?: 'Tanpa Nama') ?></div>
                                        <small class="text-muted"><?= esc($r['respondent_email'] ?: '-') ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['respondent_phone'])): ?>
                                            <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $r['respondent_phone'])) ?>" target="_blank" class="text-decoration-none text-dark">
                                                <i class="fa-brands fa-whatsapp text-success me-1"></i><?= esc($r['respondent_phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['member_name'])): ?>
                                            <div>
                                                <?php if ($r['link_status'] === 'auto_linked'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">
                                                        <i class="fa-solid fa-link me-1"></i>Auto-Linked
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">
                                                        <i class="fa-solid fa-user-check me-1"></i>Manual Linked
                                                    </span>
                                                <?php endif; ?>
                                                <div class="fw-semibold text-dark-coffee" style="font-size: 0.9rem;">
                                                    <i class="fa-solid fa-user me-1 text-gold"></i><?= esc($r['member_name']) ?>
                                                </div>
                                                <small class="text-muted">UPA: <?= esc($r['member_upa'] ?? '-') ?> | <?= esc($r['status_anggota'] ?? 'Anggota') ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="fa-solid fa-circle-question me-1"></i>Belum Tertaut (Tamu)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="viewDetail(<?= $r['id'] ?>)" title="Lihat Jawaban Lengkap">
                                                <i class="fa-solid fa-eye me-1"></i>Jawaban
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="openLinkModal(<?= $r['id'] ?>, '<?= esc(addslashes($r['respondent_name'] ?: 'Responden')) ?>', <?= $r['member_id'] ?: 'null' ?>)" title="Tautkan ke Anggota">
                                                <i class="fa-solid fa-link"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteResponse(<?= $r['id'] ?>, '<?= esc(addslashes($r['respondent_name'] ?: 'Responden')) ?>')" title="Hapus Respon">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL JAWABAN -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-dark-coffee text-white border-0 py-3 px-4">
                <h5 class="modal-title fw-bold" id="modalDetailLabel">
                    <i class="fa-solid fa-clipboard-user text-gold me-2"></i>Rincian Jawaban Responden
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-gold" role="status"></div>
                    <div class="small text-muted mt-2">Memuat jawaban...</div>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-danger btn-sm" id="btnModalDeleteResponse" style="display: none;">
                    <i class="fa-solid fa-trash-can me-1"></i>Hapus Respon Ini
                </button>
                <button type="button" class="btn btn-secondary btn-sm ms-auto" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL MANUAL LINK ANGGOTA -->
<div class="modal fade" id="modalLink" tabindex="-1" aria-labelledby="modalLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-dark-coffee text-white border-0 py-3 px-4">
                <h5 class="modal-title fw-bold" id="modalLinkLabel">
                    <i class="fa-solid fa-link text-gold me-2"></i>Tautkan Respon ke Anggota
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/forms/responses/link') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="response_id" id="link_response_id" value="">

                <div class="modal-body p-4">
                    <p class="small text-muted mb-3" id="link_info_text">
                        Pilih profil anggota di portal yang sesuai dengan responden ini:
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Anggota</label>
                        <select name="member_id" id="link_member_select" class="form-select select2-basic" style="width: 100%;">
                            <option value="">-- Lepas Tautan (Jadikan Tamu / Unlinked) --</option>
                            <?php foreach ($allMembers as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= esc($m['nama_lengkap']) ?> (UPA: <?= esc($m['kode_upa'] ?? '-') ?> | <?= esc($m['status_anggota'] ?? 'Anggota') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small text-muted">
                            Ketik nama anggota untuk mempermudah pencarian.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold fw-semibold px-4">
                        <i class="fa-solid fa-check me-1"></i>Simpan Tautan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hidden untuk Hapus Respon -->
<form id="deleteResponseForm" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="POST">
</form>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function getDetailModal() {
    const el = document.getElementById('modalDetail');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        return bootstrap.Modal.getOrCreateInstance(el);
    }
    return { show: () => $(el).modal('show'), hide: () => $(el).modal('hide') };
}

function getLinkModal() {
    const el = document.getElementById('modalLink');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        return bootstrap.Modal.getOrCreateInstance(el);
    }
    return { show: () => $(el).modal('show'), hide: () => $(el).modal('hide') };
}

function viewDetail(responseId) {
    const body = document.getElementById('modalDetailBody');
    const deleteBtn = document.getElementById('btnModalDeleteResponse');
    if (deleteBtn) deleteBtn.style.display = 'none';

    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-warning" role="status"></div>
            <div class="small text-muted mt-2">Memuat jawaban...</div>
        </div>
    `;
    getDetailModal().show();

    fetch(`<?= base_url('admin/forms/responses/detail') ?>/${responseId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const r = data.response;
                const m = data.member;
                const answers = data.answers;

                if (deleteBtn) {
                    deleteBtn.style.display = 'inline-block';
                    deleteBtn.onclick = function() {
                        getDetailModal().hide();
                        confirmDeleteResponse(r.id, r.respondent_name || 'Responden');
                    };
                }

                let memberInfo = '<span class="badge bg-warning text-dark">Tamu (Belum Tertaut)</span>';
                if (m) {
                    memberInfo = `
                        <div class="p-3 bg-light-cream rounded-3 border mb-3">
                            <div class="fw-bold text-dark-coffee"><i class="fa-solid fa-user-check text-gold me-2"></i>Tertaut ke Anggota: ${m.nama_lengkap}</div>
                            <small class="text-muted">UPA: ${m.kode_upa || '-'} &bull; Status: ${m.status_anggota || 'Anggota'} &bull; No HP: ${m.handphone || '-'}</small>
                        </div>
                    `;
                }

                let answerRows = '';
                answers.forEach((a, i) => {
                    let displayVal = a.answer_value || '<span class="text-muted fst-italic">- Kosong -</span>';
                    try {
                        const parsed = JSON.parse(a.answer_value);
                        if (Array.isArray(parsed)) {
                            displayVal = parsed.map(v => `<span class="badge bg-secondary me-1">${v}</span>`).join('');
                        }
                    } catch(e) {}

                    // Highlight for PAR-Q Ya / Tidak
                    let valClass = '';
                    if (displayVal === 'Ya') {
                        valClass = 'text-danger fw-bold';
                    } else if (displayVal === 'Tidak') {
                        valClass = 'text-success fw-semibold';
                    }

                    answerRows += `
                        <div class="mb-3 p-3 border rounded-3 bg-light-subtle">
                            <div class="small text-muted mb-1 fw-medium">#${i + 1}. ${a.field_label}</div>
                            <div class="fw-semibold fs-6 ${valClass}">${displayVal}</div>
                        </div>
                    `;
                });

                body.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark-coffee mb-0">${r.respondent_name || 'Tanpa Nama'}</h5>
                            <small class="text-muted"><i class="fa-regular fa-clock me-1"></i>Dikirim: ${r.created_at}</small>
                        </div>
                        <div>
                            <span class="badge bg-info-subtle text-info">${r.link_status}</span>
                        </div>
                    </div>
                    ${memberInfo}
                    <h6 class="fw-bold text-dark-coffee mb-3"><i class="fa-solid fa-list-check text-gold me-2"></i>Jawaban Pertanyaan:</h6>
                    ${answerRows}
                `;
            } else {
                body.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(err => {
            body.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>`;
        });
}

function openLinkModal(responseId, respondentName, currentMemberId) {
    document.getElementById('link_response_id').value = responseId;
    document.getElementById('link_info_text').innerHTML = `Tautkan respon dari <strong>${respondentName}</strong> ke profil anggota:`;
    if (currentMemberId) {
        $('#link_member_select').val(currentMemberId).trigger('change');
    } else {
        $('#link_member_select').val('').trigger('change');
    }
    getLinkModal().show();
}

function confirmDeleteResponse(id, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Respon?',
            text: `Yakin ingin menghapus data respon dari "${name}"? Tindakan ini akan menghapus data jawaban secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i>Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteResponseForm');
                form.action = `<?= base_url('admin/forms/' . $form['id'] . '/responses') ?>/${id}/delete`;
                form.submit();
            }
        });
    } else {
        if (confirm(`Yakin ingin menghapus data respon dari "${name}"?`)) {
            const form = document.getElementById('deleteResponseForm');
            form.action = `<?= base_url('admin/forms/' . $form['id'] . '/responses') ?>/${id}/delete`;
            form.submit();
        }
    }
}
</script>

<?= $this->endSection() ?>
