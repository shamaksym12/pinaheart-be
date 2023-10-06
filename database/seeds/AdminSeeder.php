<?php

use Illuminate\Database\Seeder;
use App\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminData = [
            'status' => User::STATUS_ACTIVE,
            'email' => 'admin@div-art.com',
            'first_name' => 'admin',
            'last_name' => 'admin',
            'sex' => 'M',
            'role' => User::ROLE_ADMIN,
        ];
        $admin = User::firstOrNew($adminData);
        if( ! $admin->exists) {
            $admin->password = bcrypt('secret');
            $admin->email_verified_at = now();
        }
        $admin->save();
    }
}
