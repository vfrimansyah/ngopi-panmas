<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GalleryItemModel;
use App\Models\GalleryAlbumModel;

class GalleryController extends BaseController
{
    protected GalleryItemModel $itemModel;
    protected GalleryAlbumModel $albumModel;

    public function __construct()
    {
        $this->itemModel  = new GalleryItemModel();
        $this->albumModel = new GalleryAlbumModel();
    }

    public function index()
    {
        $filters = [
            'tipe'        => $this->request->getGet('tipe'),
            'album_id'    => $this->request->getGet('album_id'),
            'is_featured' => $this->request->getGet('is_featured'),
            'search'      => $this->request->getGet('search'),
        ];

        $viewMode = $this->request->getGet('mode') ?: 'grid'; // 'grid' or 'table'

        $items   = $this->itemModel->getList($filters);
        $albums  = $this->albumModel->orderBy('sort_order', 'ASC')->findAll();
        $stats   = $this->itemModel->getStats();
        $perms   = $this->getPagePermissions('gallery');

        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];

        $data = [
            'title'           => 'Galeri & Dokumentasi Media (Foto & Video)',
            'items'           => $items,
            'albums'          => $albums,
            'stats'           => $stats,
            'filters'         => $filters,
            'viewMode'        => $viewMode,
            'can_create'      => $perms['canCreate'],
            'can_update'      => $perms['canUpdate'],
            'can_delete'      => $perms['canDelete'],
            'is_full_admin'   => in_array('admin', $userGroups) || in_array('bph', $userGroups),
            'current_user_id' => $user ? $user->id : 0,
        ];

        return view('admin/gallery/index', $data);
    }

    /**
     * Form Upload Foto (Mendukung Multiple / Batch Upload)
     */
    public function createImage()
    {
        $albums      = $this->albumModel->orderBy('sort_order', 'ASC')->findAll();
        $selectedAlb = $this->request->getGet('album_id');

        $data = [
            'title'       => 'Upload Foto Dokumentasi',
            'albums'      => $albums,
            'selectedAlb' => $selectedAlb,
        ];

        return view('admin/gallery/form_image', $data);
    }

    /**
     * Simpan Foto (Mendukung Multiple / Batch Files)
     */
    public function storeImage()
    {
        // 1. Cek jika ukuran seluruh request melebihi post_max_size PHP
        //    Saat ini terjadi, $_POST dan $_FILES kosong, dan PHP mungkin sudah mengirim warning header.
        //    Jangan pakai redirect() karena akan trigger session -> crash. Gunakan raw HTML response.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $postLimit = ini_get('post_max_size');
            $backUrl   = base_url('admin/gallery/create-image');
            // Output raw HTML redirect (tanpa menyentuh session/redirect CI)
            @ob_end_clean();
            http_response_code(200);
            echo '<!DOCTYPE html><html><head><meta charset="utf-8">';
            echo '<meta http-equiv="refresh" content="3;url=' . $backUrl . '">';
            echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
            echo '</head><body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">';
            echo '<div class="card shadow p-4 text-center" style="max-width:500px">';
            echo '<h4 class="text-danger mb-3"><i class="fa-solid fa-triangle-exclamation"></i> Upload Melebihi Batas</h4>';
            echo '<p>Ukuran total file yang diunggah melebihi kapasitas server (<strong>' . $postLimit . '</strong>).</p>';
            echo '<p class="text-muted">Silakan unggah foto dalam jumlah lebih sedikit (10–15 foto per upload).</p>';
            echo '<a href="' . $backUrl . '" class="btn btn-primary mt-2">Kembali ke Form Upload</a>';
            echo '</div></body></html>';
            exit;
        }

        $files = $this->request->getFileMultiple('images');
        if (empty($files)) {
            return redirect()->back()->withInput()->with('errors', ['Tidak ada file foto yang dipilih.']);
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $validFiles  = [];
        $fileErrors  = [];

        foreach ($files as $idx => $file) {
            if (!$file->isValid()) {
                if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $fileErrors[] = "Foto #" . ($idx + 1) . " gagal diunggah: " . $file->getErrorString();
                continue;
            }

            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, $allowedExts)) {
                $fileErrors[] = "File '" . $file->getClientName() . "' bukan format gambar yang didukung (JPG, PNG, WEBP, GIF).";
                continue;
            }

            $validFiles[] = $file;
        }

        if (empty($validFiles)) {
            $errors = !empty($fileErrors) ? $fileErrors : ['Tidak ada foto valid yang dapat diunggah.'];
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $titleMode   = $this->request->getPost('title_mode') ?: 'filename'; // 'filename' or 'custom'
        $albumId     = $this->request->getPost('album_id') ?: null;
        $commonJudul = trim((string)$this->request->getPost('judul'));
        $deskripsi   = $this->request->getPost('deskripsi');
        $tglMedia    = $this->request->getPost('tanggal_media') ?: date('Y-m-d');
        $lokasi      = $this->request->getPost('lokasi');
        $kredit      = $this->request->getPost('kredit');
        $isFeatured  = $this->request->getPost('is_featured') ? 1 : 0;
        $currentUserId = auth()->id();

        $uploadDir = FCPATH . 'uploads/gallery';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $insertedCount = 0;

        foreach ($validFiles as $idx => $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $originalName = $file->getClientName();
                $ext          = strtolower($file->getClientExtension());
                $fileSize     = $file->getSize();

                $newName = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalName);
                $file->move($uploadDir, $newName);

                // Auto-generate judul based on title_mode
                $rawTitle   = pathinfo($originalName, PATHINFO_FILENAME);
                $cleanTitle = trim(preg_replace('/\s+/', ' ', str_replace(['_', '-'], ' ', $rawTitle)));

                if ($titleMode === 'filename') {
                    // Mode 1: Ambil dari nama file asli (dengan awalan opsional jika diisi)
                    if (!empty($commonJudul)) {
                        $itemJudul = $commonJudul . ' - ' . $cleanTitle;
                    } else {
                        $itemJudul = $cleanTitle;
                    }
                } else {
                    // Mode 2: Satu judul seragam + nomor urut
                    if (!empty($commonJudul)) {
                        $itemJudul = count($validFiles) > 1 ? ($commonJudul . ' - Foto ' . ($idx + 1)) : $commonJudul;
                    } else {
                        $itemJudul = $cleanTitle;
                    }
                }

                $slug = url_title($itemJudul, '-', true) . '-' . time() . '-' . $idx;

                $this->itemModel->insert([
                    'album_id'       => $albumId,
                    'tipe'           => 'image',
                    'judul'          => $itemJudul,
                    'slug'           => $slug,
                    'deskripsi'      => $deskripsi,
                    'file_path'      => 'uploads/gallery/' . $newName,
                    'file_name'      => $originalName,
                    'file_size'      => $fileSize,
                    'thumbnail_path' => 'uploads/gallery/' . $newName,
                    'tanggal_media'  => $tglMedia,
                    'lokasi'         => $lokasi,
                    'kredit'         => $kredit,
                    'is_featured'    => $isFeatured,
                    'views_count'    => 0,
                    'created_by'     => $currentUserId,
                ]);

                $insertedCount++;
            }
        }

        $msg = "Berhasil mengunggah {$insertedCount} foto ke galeri.";
        if (!empty($fileErrors)) {
            $msg .= " (Catatan: " . count($fileErrors) . " berkas dilewati karena format/ukuran).";
        }

        return redirect()->to(base_url('admin/gallery'))->with('success', $msg);
    }

    /**
     * Form Tambah Video (YouTube URL atau Upload MP4)
     */
    public function createVideo()
    {
        $albums      = $this->albumModel->orderBy('sort_order', 'ASC')->findAll();
        $selectedAlb = $this->request->getGet('album_id');

        $data = [
            'title'       => 'Tambah Video Dokumentasi',
            'albums'      => $albums,
            'selectedAlb' => $selectedAlb,
        ];

        return view('admin/gallery/form_video', $data);
    }

    /**
     * Simpan Video
     */
    public function storeVideo()
    {
        $videoSource = $this->request->getPost('video_source'); // 'youtube' or 'upload'

        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
        ];

        if ($videoSource === 'youtube') {
            $rules['video_url'] = 'required|valid_url';
        } else {
            $rules['video_file'] = 'uploaded[video_file]|mime_in[video_file,video/mp4,video/webm,video/ogg]|max_size[video_file,102400]'; // max 100MB
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $judul       = trim((string)$this->request->getPost('judul'));
        $slug        = url_title($judul, '-', true) . '-' . time();
        $albumId     = $this->request->getPost('album_id') ?: null;
        $deskripsi   = $this->request->getPost('deskripsi');
        $tglMedia    = $this->request->getPost('tanggal_media') ?: date('Y-m-d');
        $lokasi      = $this->request->getPost('lokasi');
        $kredit      = $this->request->getPost('kredit');
        $isFeatured  = $this->request->getPost('is_featured') ? 1 : 0;
        $currentUserId = auth()->id();

        $videoData = [
            'album_id'      => $albumId,
            'tipe'          => 'video',
            'judul'         => $judul,
            'slug'          => $slug,
            'deskripsi'     => $deskripsi,
            'tanggal_media' => $tglMedia,
            'lokasi'        => $lokasi,
            'kredit'        => $kredit,
            'is_featured'   => $isFeatured,
            'views_count'   => 0,
            'created_by'    => $currentUserId,
        ];

        if ($videoSource === 'youtube') {
            $url = $this->request->getPost('video_url');
            $youtubeId = $this->extractYouTubeId($url);

            $videoData['video_type']     = 'youtube';
            $videoData['video_url']      = $url;
            $videoData['video_id']       = $youtubeId;
            $videoData['thumbnail_path'] = $youtubeId ? "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg" : null;
        } else {
            // Video File Upload
            $file = $this->request->getFile('video_file');
            $customThumb = $this->request->getFile('custom_thumbnail');

            $uploadDir = FCPATH . 'uploads/gallery';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $origName = $file->getClientName();
                $newName  = 'vid_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $origName);
                $file->move($uploadDir, $newName);

                $videoData['video_type'] = 'upload';
                $videoData['file_path']  = 'uploads/gallery/' . $newName;
                $videoData['file_name']  = $origName;
                $videoData['file_size']  = $file->getSize();
            }

            if ($customThumb && $customThumb->isValid() && !$customThumb->hasMoved()) {
                $thumbName = 'thumb_' . time() . '_' . $customThumb->getRandomName();
                $customThumb->move($uploadDir, $thumbName);
                $videoData['thumbnail_path'] = 'uploads/gallery/' . $thumbName;
            }
        }

        $this->itemModel->insert($videoData);

        return redirect()->to(base_url('admin/gallery'))->with('success', 'Video berhasil ditambahkan ke galeri.');
    }

    /**
     * Form Edit Metadata Media
     */
    public function edit(int $id)
    {
        $item = $this->itemModel->find($id);
        if (!$item) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Media tidak ditemukan.']);
        }

        if (!$this->canModifyResource($item['created_by'] ?? null, 'update', 'gallery')) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Anda hanya dapat mengubah media yang Anda unggah sendiri.']);
        }

        $albums = $this->albumModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'title'  => 'Edit Media: ' . $item['judul'],
            'item'   => $item,
            'albums' => $albums,
        ];

        return view('admin/gallery/edit', $data);
    }

    /**
     * Update Media Metadata
     */
    public function update(int $id)
    {
        $item = $this->itemModel->find($id);
        if (!$item) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Media tidak ditemukan.']);
        }

        if (!$this->canModifyResource($item['created_by'] ?? null, 'update', 'gallery')) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Anda hanya dapat mengubah media yang Anda unggah sendiri.']);
        }

        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'album_id'      => $this->request->getPost('album_id') ?: null,
            'judul'         => trim((string)$this->request->getPost('judul')),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'tanggal_media' => $this->request->getPost('tanggal_media') ?: $item['tanggal_media'],
            'lokasi'        => $this->request->getPost('lokasi'),
            'kredit'        => $this->request->getPost('kredit'),
            'is_featured'   => $this->request->getPost('is_featured') ? 1 : 0,
        ];

        // If replace image
        if ($item['tipe'] === 'image') {
            $newImage = $this->request->getFile('image_replace');
            if ($newImage && $newImage->isValid() && !$newImage->hasMoved()) {
                $uploadDir = FCPATH . 'uploads/gallery';
                $oldPath = FCPATH . $item['file_path'];
                if (file_exists($oldPath) && is_file($oldPath)) {
                    unlink($oldPath);
                }

                $origName = $newImage->getClientName();
                $newName  = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $origName);
                $newImage->move($uploadDir, $newName);

                $updateData['file_path']      = 'uploads/gallery/' . $newName;
                $updateData['file_name']      = $origName;
                $updateData['file_size']      = $newImage->getSize();
                $updateData['thumbnail_path'] = 'uploads/gallery/' . $newName;
            }
        } elseif ($item['tipe'] === 'video' && $item['video_type'] === 'youtube') {
            $newUrl = $this->request->getPost('video_url');
            if (!empty($newUrl)) {
                $youtubeId = $this->extractYouTubeId($newUrl);
                $updateData['video_url']      = $newUrl;
                $updateData['video_id']       = $youtubeId;
                $updateData['thumbnail_path'] = $youtubeId ? "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg" : $item['thumbnail_path'];
            }
        }

        $this->itemModel->update($id, $updateData);

        return redirect()->to(base_url('admin/gallery'))->with('success', 'Data media berhasil diperbarui.');
    }

    /**
     * Hapus Media
     */
    public function delete(int $id)
    {
        $item = $this->itemModel->find($id);
        if (!$item) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Media tidak ditemukan.']);
        }

        if (!$this->canModifyResource($item['created_by'] ?? null, 'delete', 'gallery')) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Anda hanya dapat menghapus media yang Anda unggah sendiri.']);
        }

        // Delete physical file if local
        if (!empty($item['file_path'])) {
            $filePath = FCPATH . $item['file_path'];
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
        }

        if (!empty($item['thumbnail_path']) && !str_starts_with($item['thumbnail_path'], 'http')) {
            $thumbPath = FCPATH . $item['thumbnail_path'];
            if (file_exists($thumbPath) && is_file($thumbPath) && $thumbPath !== ($filePath ?? '')) {
                unlink($thumbPath);
            }
        }

        $this->itemModel->delete($id);

        return redirect()->to(base_url('admin/gallery'))->with('success', 'Media berhasil dihapus dari galeri.');
    }

    /**
     * Bulk Delete — Hapus banyak media sekaligus
     */
    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');
        
        if (empty($ids) || !is_array($ids)) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Tidak ada media yang dipilih untuk dihapus.']);
        }

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($ids as $id) {
            $item = $this->itemModel->find((int) $id);
            if (!$item) {
                $skippedCount++;
                continue;
            }

            if (!$this->canModifyResource($item['created_by'] ?? null, 'delete', 'gallery')) {
                $skippedCount++;
                continue;
            }

            // Delete physical file
            if (!empty($item['file_path'])) {
                $filePath = FCPATH . $item['file_path'];
                if (file_exists($filePath) && is_file($filePath)) {
                    unlink($filePath);
                }
            }

            if (!empty($item['thumbnail_path']) && !str_starts_with($item['thumbnail_path'], 'http')) {
                $thumbPath = FCPATH . $item['thumbnail_path'];
                if (file_exists($thumbPath) && is_file($thumbPath) && $thumbPath !== ($filePath ?? '')) {
                    unlink($thumbPath);
                }
            }

            $this->itemModel->delete((int) $id);
            $deletedCount++;
        }

        $msg = "Berhasil menghapus {$deletedCount} media dari galeri.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} media dilewati karena keterbatasan akses.)";
        }

        return redirect()->to(base_url('admin/gallery'))->with('success', $msg);
    }

    /**
     * Toggle status Featured
     */
    public function toggleFeatured(int $id)
    {
        $item = $this->itemModel->find($id);
        if (!$item) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Media tidak ditemukan.']);
        }

        if (!$this->canModifyResource($item['created_by'] ?? null, 'update', 'gallery')) {
            return redirect()->to(base_url('admin/gallery'))->with('errors', ['Akses ditolak.']);
        }

        $newVal = $item['is_featured'] ? 0 : 1;
        $this->itemModel->update($id, ['is_featured' => $newVal]);

        $msg = $newVal ? 'Media ditandai sebagai Unggulan (Featured).' : 'Tanda Unggulan dilepas.';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Helper: Extract YouTube Video ID from various URL formats
     */
    private function extractYouTubeId(string $url): ?string
    {
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i';
        if (preg_match($pattern, $url, $match)) {
            return $match[1];
        }
        return null;
    }
}
