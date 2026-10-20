<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-sliders me-2 text-gold"></i>Pengaturan Website (CMS)
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-secondary text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-secondary text-decoration-none">Pengaturan</a></li>
                <li class="breadcrumb-item active text-dark-coffee fw-medium" aria-current="page">Pengaturan Web</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('message') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #ffffff;">
    <div class="card-body p-4">
        <!-- TABS HEADER -->
        <ul class="nav nav-tabs border-bottom mb-4" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark-coffee border-0 border-bottom border-3 border-transparent" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero-panel" type="button" role="tab" aria-controls="hero-panel" aria-selected="true" style="transition: all 0.2s;">
                    <i class="fa-solid fa-image me-2 text-gold"></i>Section Hero (Header)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark-coffee border-0 border-bottom border-3 border-transparent" id="about-tab" data-bs-toggle="tab" data-bs-target="#about-panel" type="button" role="tab" aria-controls="about-panel" aria-selected="false" style="transition: all 0.2s;">
                    <i class="fa-solid fa-circle-info me-2 text-gold"></i>Section Tentang & Pilar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark-coffee border-0 border-bottom border-3 border-transparent" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-panel" type="button" role="tab" aria-controls="contact-panel" aria-selected="false" style="transition: all 0.2s;">
                    <i class="fa-solid fa-address-book me-2 text-gold"></i>Kontak & Footer
                </button>
            </li>
        </ul>

        <!-- FORM START -->
        <form action="<?= base_url('admin/settings/update') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="tab-content" id="settingsTabContent">
                <!-- PANEL 1: HERO -->
                <div class="tab-pane fade show active" id="hero-panel" role="tabpanel" aria-labelledby="hero-tab">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark-coffee">Hero Subtitle</label>
                            <input type="text" name="hero_subtitle" class="form-control" value="<?= esc($settings['hero_subtitle']) ?>" placeholder="Filosofi dari Hulu ke Hilir">
                            <div class="form-text">Teks kecil di atas judul utama banner.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark-coffee">Hero Title (Judul Utama) <span class="text-danger">*</span></label>
                            <input type="text" name="hero_title" class="form-control" value="<?= esc($settings['hero_title']) ?>" placeholder="Menyeduh Kebersamaan, Merayakan perjuangan Petani Kopi." required>
                            <div class="form-text">Gunakan tag HTML jika diperlukan (misal: <code>&lt;em&gt;kata&lt;/em&gt;</code> untuk tulisan miring).</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark-coffee">Hero Description (Slogan / Kutipan) <span class="text-danger">*</span></label>
                            <textarea name="hero_description" class="form-control" rows="4" placeholder="Tuliskan slogan utama..." required><?= esc($settings['hero_description']) ?></textarea>
                            <div class="form-text">Deskripsi panjang di tengah banner hero.</div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: ABOUT -->
                <div class="tab-pane fade" id="about-panel" role="tabpanel" aria-labelledby="about-tab">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark-coffee">Judul Section Tentang <span class="text-danger">*</span></label>
                            <input type="text" name="about_title" class="form-control" value="<?= esc($settings['about_title']) ?>" placeholder="Berkomitmen pada kualitas dari hulu hingga ke hilir." required>
                            <div class="form-text">Judul utama untuk bagian pengenalan komitmen komunitas.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark-coffee">Deskripsi Section Tentang <span class="text-danger">*</span></label>
                            <textarea name="about_description" class="form-control" rows="5" placeholder="Tuliskan deskripsi panjang tentang filosofi..." required><?= esc($settings['about_description']) ?></textarea>
                            <div class="form-text">Penjelasan komitmen dan filosofi Ngopi Panmas.</div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 3: CONTACT & FOOTER -->
                <div class="tab-pane fade" id="contact-panel" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark-coffee">Alamat Fisik Kantor <span class="text-danger">*</span></label>
                            <input type="text" name="contact_address" class="form-control" value="<?= esc($settings['contact_address']) ?>" placeholder="Contoh: Jl. Raya Pandeglang No. 10" required>
                            <div class="form-text">Alamat yang tampil di form kontak dan bagian bawah halaman.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark-coffee">Email Kontak <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" class="form-control" value="<?= esc($settings['contact_email']) ?>" placeholder="kontak@ngopipanmas.or.id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark-coffee">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="contact_phone" class="form-control" value="<?= esc($settings['contact_phone']) ?>" placeholder="+62 812-3456-7890" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-gold px-4 py-2">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan
                </button>
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
    .nav-tabs .nav-link.active {
        border-color: #d4af37 !important;
        background-color: transparent !important;
        color: #3e2723 !important;
    }
</style>
<?= $this->endSection() ?>
