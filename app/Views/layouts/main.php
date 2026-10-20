<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Ngobrol Pikiran Indonesia — www.indonesiangopi.com' ?></title>
    <meta name="description" content="<?= $metaDesc ?? 'Ngobrol Pikiran Indonesia (NGOPI) — Dari Pikiran Menjadi Gerakan. Portal gagasan, berita, event, dan komunitas.' ?>">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/images/logo.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo.png') ?>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Public Design System -->
    <link href="<?= base_url('assets/css/public.css') ?>" rel="stylesheet">
    <?= $this->renderSection('css') ?>
</head>
<body>

    <!-- ============= NAVBAR ============= -->
    <nav class="pub-navbar">
        <div class="container">
            <!-- Brand -->
            <a class="pub-navbar-brand" href="<?= base_url() ?>" style="padding: 2px 0;">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Ngobrol Pikiran Indonesia" style="height: 44px; width: auto; background: #ffffff; padding: 4px 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            </a>

            <!-- Hamburger -->
            <button class="pub-navbar-toggler" id="navToggler" type="button" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars" id="navToggleIcon"></i>
            </button>

            <!-- Nav Links (desktop inline, mobile collapse) -->
            <div class="pub-nav-collapse" id="pubNavCollapse">
                <ul class="pub-nav-links">
                    <li>
                        <a href="<?= base_url() ?>" class="<?= url_is('/') ? 'active' : '' ?>">
                            <i class="fa-solid fa-house fa-xs"></i>Beranda
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('artikel') ?>" class="<?= url_is('artikel*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-book-open fa-xs"></i>Artikel
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('ide') ?>" class="<?= url_is('ide*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-lightbulb fa-xs"></i>Ide
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('diskusi') ?>" class="<?= url_is('diskusi*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-comments fa-xs"></i>Diskusi
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('berita') ?>" class="<?= url_is('berita*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-newspaper fa-xs"></i>Berita
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('event') ?>" class="<?= url_is('event*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-calendar-days fa-xs"></i>Agenda
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('galeri') ?>" class="<?= url_is('galeri*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-photo-film fa-xs"></i>Galeri
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('katalog') ?>" class="<?= url_is('katalog*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-store fa-xs"></i>Katalog
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('anggota') ?>" class="<?= url_is('anggota*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-users fa-xs"></i>Anggota
                        </a>
                    </li>
                </ul>

                <!-- Actions -->
                <div class="pub-nav-actions">
                    <?php if (auth()->loggedIn()): ?>
                        <div class="navbar-user-menu dropdown">
                            <button class="btn-user dropdown-toggle" type="button" id="userDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-user"></i>
                                <span><?= esc(auth()->user()->username) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <?php
                                $userGroups = auth()->user()->getGroups();
                                $hasAdminRole = false;
                                foreach ($userGroups as $grp) {
                                    if ($grp !== 'user') {
                                        $hasAdminRole = true;
                                        break;
                                    }
                                }
                                if ($hasAdminRole || auth()->user()->can('admin.access')):
                                ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('admin/dashboard') ?>">
                                        <i class="fa-solid fa-gauge"></i>Panel Admin
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: rgba(200,160,64,0.2); margin: 6px 8px;"></li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('profil') ?>">
                                        <i class="fa-solid fa-user-circle"></i>Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                        <i class="fa-solid fa-right-from-bracket"></i>Keluar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn-nav-login">
                            <i class="fa-solid fa-right-to-bracket"></i>Masuk
                        </a>
                        <a href="<?= base_url('register') ?>" class="btn-nav-register">
                            <i class="fa-solid fa-user-plus"></i>Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============= MAIN CONTENT ============= -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- ============= FOOTER ============= -->
    <footer class="pub-footer">
        <div class="container">
            <div class="row g-5">
                <!-- Brand col -->
                <div class="col-lg-4 col-md-6">
                    <div class="mb-3">
                        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Ngobrol Pikiran Indonesia" style="height: 52px; width: auto; background: #ffffff; padding: 5px 12px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                    </div>
                    <p class="footer-brand-desc">
                        <strong>Ngobrol Pikiran Indonesia (NGOPI)</strong> &mdash; Dari pikiran menjadi gerakan. Wadah gagasan, jaringan, dan aksi kolaboratif masyarakat Indonesia.<br>
                        <a href="https://www.indonesiangopi.com" target="_blank" style="color:var(--gold-400); font-weight:600; text-decoration:none;"><i class="fa-solid fa-globe me-1"></i>www.indonesiangopi.com</a>
                    </p>
                    <div class="footer-social">
                        <a href="#" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="#" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Navigasi -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h5 class="footer-col-title">Navigasi</h5>
                    <ul class="footer-links">
                        <li><a href="<?= base_url() ?>"><i class="fa-solid fa-chevron-right"></i>Beranda</a></li>
                        <li><a href="<?= base_url('artikel') ?>"><i class="fa-solid fa-chevron-right"></i>Artikel</a></li>
                        <li><a href="<?= base_url('ide') ?>"><i class="fa-solid fa-chevron-right"></i>Ide &amp; Gagasan</a></li>
                        <li><a href="<?= base_url('diskusi') ?>"><i class="fa-solid fa-chevron-right"></i>Forum Diskusi</a></li>
                        <li><a href="<?= base_url('berita') ?>"><i class="fa-solid fa-chevron-right"></i>Berita</a></li>
                        <li><a href="<?= base_url('event') ?>"><i class="fa-solid fa-chevron-right"></i>Agenda Event</a></li>
                        <li><a href="<?= base_url('katalog') ?>"><i class="fa-solid fa-chevron-right"></i>Katalog Produk</a></li>
                        <li><a href="<?= base_url('anggota') ?>"><i class="fa-solid fa-chevron-right"></i>Direktori Anggota</a></li>
                    </ul>
                </div>

                <!-- Member Area -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h5 class="footer-col-title">Area Anggota</h5>
                    <ul class="footer-links">
                        <li><a href="<?= base_url('login') ?>"><i class="fa-solid fa-chevron-right"></i>Login Anggota</a></li>
                        <li><a href="<?= base_url('register') ?>"><i class="fa-solid fa-chevron-right"></i>Daftar</a></li>
                        <?php if (auth()->loggedIn()): ?>
                        <li><a href="<?= base_url('profil') ?>"><i class="fa-solid fa-chevron-right"></i>Profil Saya</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-col-title">Kontak Kami</h5>
                    <ul class="list-unstyled footer-contact">
                        <li>
                            <i class="fa-solid fa-globe"></i>
                            <span>www.indonesiangopi.com</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <span>info@indonesiangopi.com</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            <span>+62 812-3456-7890</span>
                        </li>
                        <li>
                            <i class="fa-regular fa-clock"></i>
                            <span>Senin – Jumat: 08.00 – 17.00 WIB</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p class="mb-0">
                    &copy; <?= date('Y') ?> Ngobrol Pikiran Indonesia (NGOPI) &mdash; <a href="https://www.indonesiangopi.com" target="_blank" style="color:var(--gold-400);">www.indonesiangopi.com</a>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Mobile nav toggle
    const navToggler = document.getElementById('navToggler');
    const navIcon    = document.getElementById('navToggleIcon');
    const navCollapse = document.getElementById('pubNavCollapse');

    if (navToggler) {
        navToggler.addEventListener('click', () => {
            navCollapse.classList.toggle('show');
            navIcon.className = navCollapse.classList.contains('show')
                ? 'fa-solid fa-xmark'
                : 'fa-solid fa-bars';
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!navToggler.contains(e.target) && !navCollapse.contains(e.target)) {
                navCollapse.classList.remove('show');
                navIcon.className = 'fa-solid fa-bars';
            }
        });
    }

    // Counter animation
    function animateCounter(el) {
        const target = parseInt(el.dataset.target || el.textContent, 10);
        const duration = 1600;
        const step = target / (duration / 16);
        let current = 0;
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = Math.floor(current).toLocaleString('id-ID');
            if (current >= target) clearInterval(timer);
        }, 16);
    }

    // Trigger counter when visible
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.counted) {
                entry.target.dataset.counted = '1';
                animateCounter(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));
    </script>

    <?= $this->renderSection('js') ?>
</body>
</html>
