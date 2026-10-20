<?php

ob_start();

// ---------------------------------------------------------------
// EARLY DETECTION: POST size exceeds PHP's post_max_size
// ---------------------------------------------------------------
// When PHP receives a POST body larger than post_max_size, it drops
// $_POST and $_FILES and emits a Warning that sends HTTP headers.
// This makes CodeIgniter's Session->configure() crash on ini_set().
// We detect this condition HERE, before CI boots, and show a friendly
// error page instead of the ugly ErrorException.
if (
    isset($_SERVER['REQUEST_METHOD'], $_SERVER['CONTENT_LENGTH'])
    && $_SERVER['REQUEST_METHOD'] === 'POST'
    && empty($_POST)
    && empty($_FILES)
    && (int) $_SERVER['CONTENT_LENGTH'] > 0
) {
    // Flush any PHP warnings that may have been buffered
    @ob_end_clean();
    $limit = ini_get('post_max_size');
    $sizeMb = round((int) $_SERVER['CONTENT_LENGTH'] / 1024 / 1024, 1);
    http_response_code(200);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Upload Melebihi Batas</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">';
    echo '</head><body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">';
    echo '<div class="card shadow-lg border-0 p-5 text-center" style="max-width:520px">';
    echo '<div class="mb-3"><i class="fa-solid fa-cloud-arrow-up text-danger" style="font-size:3rem"></i></div>';
    echo '<h4 class="text-danger fw-bold">Upload Melebihi Batas Server</h4>';
    echo '<p class="mt-3">Ukuran total file yang diunggah (<strong>' . $sizeMb . ' MB</strong>) melebihi kapasitas server (<strong>' . $limit . '</strong>).</p>';
    echo '<p class="text-muted">Silakan unggah foto dalam jumlah lebih sedikit, misalnya <strong>10–15 foto</strong> per sesi upload.</p>';
    echo '<hr>';
    echo '<a href="javascript:history.back()" class="btn btn-primary px-4"><i class="fa-solid fa-arrow-left me-2"></i>Kembali</a>';
    echo '</div></body></html>';
    exit;
}

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
