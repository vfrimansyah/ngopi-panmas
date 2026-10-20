<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userProvider = auth()->getProvider();

        $existing = $userProvider->where('username', 'admin')->first();
        if ($existing) {
            echo "User 'admin' already exists. Re-setting password to 'admin123'...\n";
            $existing->setPassword('admin123');
            $userProvider->save($existing);
            echo "Password reset successful.\n";
            return;
        }

        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => 'admin',
            'email'    => 'admin@ngopipanmas.com',
            'password' => 'admin123',
        ]);

        if ($userProvider->save($user)) {
            $user = $userProvider->findById($userProvider->getInsertID());
            $user->addGroup('admin');
            echo "Admin user created successfully!\n";
            echo "Username: admin\n";
            echo "Email: admin@ngopipanmas.com\n";
            echo "Password: admin123\n";
        } else {
            echo "Failed to create admin user.\n";
        }
    }
}
