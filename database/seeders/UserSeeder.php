<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@ehb.be',
            'password' => 'Password!321',
            'is_admin' => true,
        ]);

        NotificationSetting::create([
            'user_id' => $admin->id,
        ]);
    }
}
