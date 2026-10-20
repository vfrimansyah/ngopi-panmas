<?php
// HAPUS FILE INI SETELAH DIGUNAKAN!
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache berhasil direset!';
} else {
    echo 'OPcache tidak aktif.';
}
