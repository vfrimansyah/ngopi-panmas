<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Shield\Entities\User;

class CreateSeksiUsers extends BaseCommand
{
    protected $group       = 'Users';
    protected $name        = 'users:create-seksi';
    protected $description = 'Create or update 10 seksi users with default password dpcP4nmas';

    public function run(array $params)
    {
        $userList = [
            ['username' => 'skap',    'email' => 'skap@userpanmas.com'],
            ['username' => 'bipeka',  'email' => 'bipeka@userpanmas.com'],
            ['username' => 'skbn',    'email' => 'skbn@userpanmas.com'],
            ['username' => 'sp3',     'email' => 'sp3@userpanmas.com'],
            ['username' => 'sppm',    'email' => 'sppm@userpanmas.com'],
            ['username' => 'komdigi', 'email' => 'komdigi@userpanmas.com'],
            ['username' => 'spdk',    'email' => 'spdk@userpanmas.com'],
            ['username' => 'sumkraf', 'email' => 'sumkraf@userpanmas.com'],
            ['username' => 'spukb',   'email' => 'spukb@userpanmas.com'],
            ['username' => 'sppn',    'email' => 'sppn@userpanmas.com'],
        ];

        $password = 'dpcP4nmas';
        $role     = 'seksi';

        $userModel = model('UserModel');
        $db        = \Config\Database::connect();

        CLI::write("Memproses pembuatan 10 user seksi...", 'yellow');

        foreach ($userList as $u) {
            $existingUser  = $userModel->where('username', $u['username'])->first();
            $existingEmail = $db->table('auth_identities')->where('secret', $u['email'])->get()->getRowArray();

            if ($existingUser || $existingEmail) {
                $userId = $existingUser ? $existingUser->id : $existingEmail['user_id'];
                $userObj = $userModel->findById($userId);
                if ($userObj) {
                    $userObj->setPassword($password);
                    $userModel->save($userObj);
                    $userObj->syncGroups($role);
                    $db->table('users')->where('id', $userId)->update([
                        'active' => 1,
                        'force_password_reset' => 0,
                    ]);
                    CLI::write("[UPDATED] {$u['username']} ({$u['email']}) -> ID: {$userId}, Password: '{$password}', Role: '{$role}'", 'green');
                }
                continue;
            }

            $user = new User([
                'username' => $u['username'],
                'active'   => 1,
            ]);
            $user->setEmail($u['email']);
            $user->setPassword($password);

            $userModel->save($user);
            $userId = $userModel->getInsertID();

            $newUser = $userModel->findById($userId);
            $newUser->addGroup($role);

            $db->table('users')->where('id', $userId)->update([
                'active' => 1,
                'force_password_reset' => 0,
            ]);

            CLI::write("[CREATED] {$u['username']} ({$u['email']}) -> ID: {$userId}, Password: '{$password}', Role: '{$role}'", 'green');
        }

        CLI::write("\nSelesai! Semua 10 user seksi berhasil dibuat/diperbarui.", 'cyan');
    }
}
