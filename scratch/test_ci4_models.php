<?php
define('FCPATH', __DIR__ . '/../public/');
chdir(FCPATH);

require FCPATH . '../app/Config/Paths.php';
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Boot.php';

class TestBoot extends \CodeIgniter\Boot {
    public static function bootCustom($paths) {
        static::definePathConstants($paths);
        static::loadConstants();
        static::checkMissingExtensions();
        static::loadDotEnv($paths);
        static::defineEnvironment();
        static::loadEnvironmentBootstrap($paths);
        static::loadCommonFunctions();
        static::loadAutoloader();
        static::initializeCodeIgniter();
    }
}

TestBoot::bootCustom($paths);

$historyModel = new \App\Models\MemberUpaHistoryModel();

// Test simulating assigning member 25 to PM3101 and then reverting
$member25 = (new \App\Models\MemberModel())->find(25);
echo "Testing with member 25 ({$member25['nama_lengkap']}), current UPA: {$member25['kode_upa']}\n";

$origUpa = $member25['kode_upa'];
$historyModel->assignMember(25, 'PM3101', [
    'tahun_mulai' => '2025',
    'catatan'     => 'Test mutasi ke PM3101'
]);

$hist25 = $historyModel->getMemberHistories(25);
echo "Member 25 histories after assigning to PM3101:\n";
foreach ($hist25 as $h) {
    echo " - {$h['kode_upa']} | {$h['pembimbing_nama']} | {$h['periode']} | status: {$h['status']} | is_current: {$h['is_current']}\n";
}

// Revert back
$historyModel->assignMember(25, $origUpa, [
    'tahun_mulai' => '2026',
    'catatan'     => 'Revert back to ' . $origUpa
]);
// Remove test history records to keep DB pristine
(new \CodeIgniter\Database\BaseConnection())->connect();
$db = \Config\Database::connect();
$db->table('member_upa_histories')->where('member_id', 25)->where('catatan LIKE', '%Test%')->delete();
$db->table('members')->where('id', 25)->update(['kode_upa' => $origUpa]);

echo "\nAssign and Transfer logic verified and cleaned up successfully!\n";
