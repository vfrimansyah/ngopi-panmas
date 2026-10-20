<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SeedFrontendModules extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'seed:frontend';
    protected $description = 'Seeds sample data for Artikel, Ide, and Diskusi modules.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // 1. Article Categories
        $categories = [
            ['nama_kategori' => 'Opini & Pemikiran', 'tipe' => 'artikel'],
            ['nama_kategori' => 'Pendidikan & Kebudayaan', 'tipe' => 'artikel'],
            ['nama_kategori' => 'Ekonomi & UMKM', 'tipe' => 'ide'],
            ['nama_kategori' => 'Teknologi & Inovasi', 'tipe' => 'ide'],
            ['nama_kategori' => 'Sosial Kemasyarakatan', 'tipe' => 'artikel'],
            ['nama_kategori' => 'Kebijakan Publik', 'tipe' => 'ide'],
        ];

        foreach ($categories as $cat) {
            $exists = $db->table('article_categories')->where('nama_kategori', $cat['nama_kategori'])->get()->getRow();
            if (!$exists) {
                $db->table('article_categories')->insert($cat);
            }
        }

        $catOpini     = $db->table('article_categories')->where('nama_kategori', 'Opini & Pemikiran')->get()->getRow('id') ?? 1;
        $catEkonomi   = $db->table('article_categories')->where('nama_kategori', 'Ekonomi & UMKM')->get()->getRow('id') ?? 1;
        $catTeknologi = $db->table('article_categories')->where('nama_kategori', 'Teknologi & Inovasi')->get()->getRow('id') ?? 1;
        $catSosial    = $db->table('article_categories')->where('nama_kategori', 'Sosial Kemasyarakatan')->get()->getRow('id') ?? 1;

        // Get first active member ID
        $member = $db->table('members')->where('is_active', 1)->get()->getRow('id') ?? 1;

        // 2. Sample Artikel
        $artikels = [
            [
                'judul'            => 'Peran Dialog dan Pertukaran Pikiran dalam Membangun Peradaban Bangsa',
                'slug'             => 'peran-dialog-dan-pertukaran-pikiran-membangun-bangsa',
                'tipe'             => 'artikel',
                'kategori_id'      => $catOpini,
                'konten'           => '<p>Pertukaran gagasan dan diskusi terbuka merupakan pondasi utama dari setiap kemajuan peradaban. Ketika berbagai latar belakang pemikiran bertemu dalam ruang dialog yang konstruktif, solusi-solusi inovatif untuk tantangan sosial dapat lahir.</p><p>Gerakan <strong>Ngobrol Pikiran Indonesia (NGOPI)</strong> hadir untuk mengkanalisasi gagasan-gagasan produktif dari seluruh pelosok negeri menjadi aksi nyata yang berdampak langsung bagi masyarakat.</p>',
                'author_member_id' => $member,
                'status'           => 'published',
                'views_count'      => 142,
                'published_at'     => date('Y-m-d H:i:s', strtotime('-2 days')),
                'created_at'       => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'judul'            => 'Strategi Penguatan Literasi Digital Masyarakat di Era Transformasi Informasi',
                'slug'             => 'strategi-penguatan-literasi-digital-masyarakat',
                'tipe'             => 'artikel',
                'kategori_id'      => $catTeknologi,
                'konten'           => '<p>Literasi digital bukan lagi sekadar kemampuan menggunakan perangkat elektronik, melainkan kemampuan memilah, menganalisis, dan memanfaatkan informasi secara cerdas dan kritis.</p><p>Melalui kolaborasi komunitas, literasi digital dapat diperluas hingga ke pemuda dan pelaku usaha lokal untuk meningkatkan efisiensi dan daya saing nasional.</p>',
                'author_member_id' => $member,
                'status'           => 'published',
                'views_count'      => 98,
                'published_at'     => date('Y-m-d H:i:s', strtotime('-5 days')),
                'created_at'       => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],
        ];

        foreach ($artikels as $art) {
            $exists = $db->table('articles')->where('slug', $art['slug'])->get()->getRow();
            if (!$exists) {
                $db->table('articles')->insert($art);
            }
        }

        // 3. Sample Ide / Gagasan
        $ides = [
            [
                'judul'            => 'Gagasan Pembentukan Sentra Inkubasi UMKM Berbasis Komunitas Lokal',
                'slug'             => 'gagasan-pembentukan-sentra-inkubasi-umkm-komunitas',
                'tipe'             => 'ide',
                'kategori_id'      => $catEkonomi,
                'konten'           => '<p>Usulan program pemberdayaan ekonomi berbasis komunitas dengan mendirikan Sentra Inkubasi UMKM. Sentra ini bertugas memberikan pendampingan legalitas, sertifikasi halal, desain kemasan modern, dan pemasaran digital bagi produk-produk hasil karya anggota.</p><p>Dengan dukungan pendampingan terstruktur, UMKM lokal dapat berkembang cepat dan menembus pasar skala nasional.</p>',
                'author_member_id' => $member,
                'status'           => 'published',
                'views_count'      => 210,
                'published_at'     => date('Y-m-d H:i:s', strtotime('-1 day')),
                'created_at'       => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'judul'            => 'Inisiatif Bank Sampah Komunitas dan Edukasi Pengelolaan Limbah Organik',
                'slug'             => 'inisiatif-bank-sampah-komunitas-edukasi-limbah',
                'tipe'             => 'ide',
                'kategori_id'      => $catSosial,
                'konten'           => '<p>Gagasan untuk menciptakan lingkungan bersih dan berdaya nilai ekonomi melalui gerakan Bank Sampah mandiri. Sampah organik diolah menjadi kompos berkualitas tinggi, sementara sampah anorganik ditabung dan didaur ulang.</p>',
                'author_member_id' => $member,
                'status'           => 'published',
                'views_count'      => 175,
                'published_at'     => date('Y-m-d H:i:s', strtotime('-3 days')),
                'created_at'       => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
        ];

        foreach ($ides as $ide) {
            $exists = $db->table('articles')->where('slug', $ide['slug'])->get()->getRow();
            if (!$exists) {
                $db->table('articles')->insert($ide);
            }
        }

        // 4. Sample Discussions
        $discussions = [
            [
                'judul'            => 'Bagaimana Langkah Efektif Meningkatkan Partisipasi Pemuda dalam Program Sosial?',
                'slug'             => 'bagaimana-langkah-efektif-meningkatkan-partisipasi-pemuda',
                'kategori_id'      => $catSosial,
                'konten'           => 'Pemuda merupakan kunci keberlanjutan setiap gerakan masyarakat. Namun seringkali kita menghadapi tantangan keterlibatan aktif mereka dalam kegiatan-kegiatan sosial. Menurut teman-teman, metode dan pendekatan apa yang paling relevan untuk merangkul pemuda zaman sekarang?',
                'author_member_id' => $member,
                'views_count'      => 320,
                'upvotes_count'    => 15,
                'replies_count'    => 3,
                'status'           => 'published',
                'is_pinned'        => 1,
                'created_at'       => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'judul'            => 'Diskusi: Tantangan dan Peluang Digitalisasi Produk Lokal di Pasar Global',
                'slug'             => 'diskusi-tantangan-dan-peluang-digitalisasi-produk-lokal',
                'kategori_id'      => $catEkonomi,
                'konten'           => 'Produk-produk UMKM kita sebenarnya memiliki kualitas rasa dan pengerjaan yang sangat baik. Mari kita diskusikan kendala apa saja yang paling sering dialami saat mencoba memasarkan produk secara daring dan bagaimana solusinya.',
                'author_member_id' => $member,
                'views_count'      => 185,
                'upvotes_count'    => 8,
                'replies_count'    => 2,
                'status'           => 'published',
                'is_pinned'        => 0,
                'created_at'       => date('Y-m-d H:i:s', strtotime('-4 days')),
            ],
        ];

        foreach ($discussions as $disc) {
            $exists = $db->table('discussions')->where('slug', $disc['slug'])->get()->getRow();
            if (!$exists) {
                $db->table('discussions')->insert($disc);
                $discId = $db->insertID();

                // Add sample replies for first discussion
                if ($disc['slug'] === 'bagaimana-langkah-efektif-meningkatkan-partisipasi-pemuda') {
                    $db->table('discussion_replies')->insert([
                        'discussion_id' => $discId,
                        'member_id'     => $member,
                        'konten'        => 'Pendekatan paling efektif adalah memberikan peran kepemimpinan langsung dan ruang kreasi berbasis proyek (project-based). Pemuda lebih antusias jika mereka diajak merancang sejak awal, bukan hanya mengeksekusi.',
                        'upvotes_count' => 5,
                        'is_best_answer'=> 1,
                        'created_at'    => date('Y-m-d H:i:s', strtotime('-12 hours')),
                    ]);
                    $db->table('discussion_replies')->insert([
                        'discussion_id' => $discId,
                        'member_id'     => $member,
                        'konten'        => 'Setuju! Selain itu pemanfaatan media sosial dan konten visual yang menarik juga sangat penting untuk mempublikasikan dampak dari gerakan tersebut.',
                        'upvotes_count' => 2,
                        'is_best_answer'=> 0,
                        'created_at'    => date('Y-m-d H:i:s', strtotime('-6 hours')),
                    ]);
                }
            }
        }

        CLI::write('Seed data for Artikel, Ide, and Diskusi added successfully!', 'green');
    }
}
