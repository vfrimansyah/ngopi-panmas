<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-box-open"></i>
                Katalog UMKM
            </div>
            <h1 class="page-hero-title" style="max-width:600px;">
                <?= esc($product['nama_produk']) ?>
            </h1>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <a href="<?= base_url('katalog') ?>">Katalog</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span><?= esc(mb_strimwidth($product['nama_produk'], 0, 40, '...')) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Product Detail -->
<section style="padding:50px 0 70px; background:#fff;">
    <div class="container">
        <div class="row g-5">

            <!-- Left: Photos -->
            <div class="col-lg-5">
                <?php
                $fotos = json_decode($product['foto_urls'] ?? '[]', true);
                $mainPhoto = !empty($fotos[0]) ? $fotos[0] : null;
                ?>
                <div style="border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--border-warm); box-shadow:var(--shadow-md); margin-bottom:12px;">
                    <?php if ($mainPhoto): ?>
                        <img src="<?= base_url('uploads/' . $mainPhoto) ?>"
                             alt="<?= esc($product['nama_produk']) ?>"
                             id="mainProductImg"
                             style="width:100%; height:360px; object-fit:cover; display:block;">
                    <?php else: ?>
                        <div style="width:100%;height:360px;background:linear-gradient(135deg,var(--coffee-100),var(--coffee-200));display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--coffee-400);">
                            <i class="fa-solid fa-box-open fa-3x"></i>
                            <span style="font-size:0.85rem; font-weight:600;">Foto belum tersedia</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Thumbnail strip -->
                <?php if (count($fotos) > 1): ?>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <?php foreach ($fotos as $i => $foto): ?>
                    <div class="img-zoom-wrap"
                         onclick="document.getElementById('mainProductImg').src='<?= base_url('uploads/' . $foto) ?>'"
                         style="width:70px; height:70px; border-radius:8px; overflow:hidden; border:2px solid <?= $i===0 ? 'var(--gold-400)' : 'var(--border-warm)' ?>; cursor:pointer;">
                        <img src="<?= base_url('uploads/' . $foto) ?>"
                             alt="Foto <?= $i+1 ?>"
                             style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: Info -->
            <div class="col-lg-7">
                <div style="font-size:0.75rem; font-weight:700; color:var(--coffee-400); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:8px;">
                    Produk UMKM Anggota
                </div>
                <h1 style="font-family:var(--font-serif); font-size:clamp(1.6rem,3vw,2.2rem); color:var(--coffee-900); margin-bottom:12px;">
                    <?= esc($product['nama_produk']) ?>
                </h1>

                <!-- Price -->
                <?php if (!empty($product['harga']) && $product['harga'] > 0): ?>
                <div style="font-family:var(--font-serif); font-size:2rem; font-weight:800; color:var(--coffee-700); margin-bottom:16px;">
                    Rp <?= number_format($product['harga'], 0, ',', '.') ?>
                    <?php if (!empty($product['satuan'])): ?>
                    <span style="font-size:1rem; font-weight:500; color:var(--warm-gray);">/ <?= esc($product['satuan']) ?></span>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div style="font-size:1.2rem; font-weight:700; color:var(--warm-gray); margin-bottom:16px;">
                    <i class="fa-solid fa-handshake me-2 text-gold"></i>Harga dapat dinegosiasikan
                </div>
                <?php endif; ?>

                <!-- Separator -->
                <div class="pub-divider mb-4"></div>

                <!-- Description -->
                <?php if (!empty($product['deskripsi'])): ?>
                <div class="mb-4">
                    <h6 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:10px;">
                        <i class="fa-solid fa-circle-info me-2 text-gold"></i>Deskripsi Produk
                    </h6>
                    <div class="article-body" style="font-size:0.95rem;">
                        <?= nl2br(esc($product['deskripsi'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Seller Info -->
                <?php if (!empty($member)): ?>
                <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:18px; margin-bottom:24px;">
                    <h6 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:12px;">
                        <i class="fa-solid fa-user-tie me-2 text-gold"></i>Penjual / Anggota
                    </h6>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="member-avatar" style="width:48px; height:48px; font-size:1.1rem;">
                            <?= mb_strtoupper(mb_substr($member['nama_lengkap'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-weight:700; color:var(--coffee-900); font-size:0.95rem;">
                                <?= esc($member['nama_lengkap']) ?>
                            </div>
                            <?php if (!empty($member['kode_upa'])): ?>
                            <div style="font-size:0.8rem; color:var(--warm-gray);">
                                <i class="fa-solid fa-location-dot me-1 text-gold"></i>UPA <?= esc($member['kode_upa']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Contact Buttons -->
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <?php if (!empty($product['kontak_whatsapp'])): ?>
                    <?php
                    $wa = preg_replace('/[^0-9]/', '', $product['kontak_whatsapp']);
                    if (substr($wa, 0, 1) === '0') $wa = '62' . substr($wa, 1);
                    $waMsg = urlencode('Halo, saya tertarik dengan produk ' . $product['nama_produk'] . ' yang ada di Katalog PANMAS.');
                    ?>
                    <a href="https://api.whatsapp.com/send?phone=<?= $wa ?>&text=<?= $waMsg ?>"
                       target="_blank" class="btn-coffee" id="btn-wa-seller"
                       style="background:linear-gradient(135deg,#25D366,#128C7E);">
                        <i class="fa-brands fa-whatsapp"></i>Hubungi via WhatsApp
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($product['link_eksternal'])): ?>
                    <a href="<?= esc($product['link_eksternal']) ?>" target="_blank"
                       class="btn-coffee-outline" id="btn-ext-link">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>Kunjungi Toko
                    </a>
                    <?php endif; ?>
                    <?php if (empty($product['kontak_whatsapp']) && empty($product['link_eksternal'])): ?>
                    <div style="background:var(--coffee-100); border:1px solid var(--border-warm); border-radius:var(--radius-sm); padding:12px 18px; font-size:0.875rem; color:var(--warm-gray);">
                        <i class="fa-solid fa-circle-info me-2"></i>Kontak penjual belum tersedia.
                        <?php if (!auth()->loggedIn()): ?>
                        <a href="<?= base_url('login') ?>" style="color:var(--coffee-600);">Login</a> untuk melihat info kontak.
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Back -->
                <div class="mt-4">
                    <a href="<?= base_url('katalog') ?>" class="view-all-link">
                        <i class="fa-solid fa-arrow-left"></i>Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5 pt-4" style="border-top:1px solid var(--border-warm);">
            <h4 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:24px;">
                <i class="fa-solid fa-layer-group me-2 text-gold"></i>Produk Serupa
            </h4>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $rp): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <div class="product-card-img">
                            <?php
                            $rpFotos = json_decode($rp['foto_urls'] ?? '[]', true);
                            $rpThumb = !empty($rpFotos[0]) ? $rpFotos[0] : null;
                            ?>
                            <?php if ($rpThumb): ?>
                                <img src="<?= base_url('uploads/' . $rpThumb) ?>" alt="<?= esc($rp['nama_produk']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="product-card-img-placeholder">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-name"><?= esc($rp['nama_produk']) ?></div>
                            <?php if (!empty($rp['harga']) && $rp['harga'] > 0): ?>
                                <div class="product-card-price">Rp <?= number_format($rp['harga'], 0, ',', '.') ?></div>
                            <?php else: ?>
                                <div class="product-card-price negotiable">Harga Nego</div>
                            <?php endif; ?>
                            <a href="<?= base_url('katalog/' . $rp['id']) ?>"
                               class="btn-coffee btn-sm-coffee btn-full" id="related-prod-<?= $rp['id'] ?>">
                                <i class="fa-solid fa-eye"></i>Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
