<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            'admin' => [
                [
                    'email' => 'admin1@gmail.com',
                    'password' => 'secret',                    
                    'first_name' => 'Admin1',
                    'last_name' => 'Admin1',

                ],
            ],
            'manager' => [
                [
                    'email' => 'manager1@gmail.com',
                    'password' => 'secret',                    
                    'first_name' => 'Manager1',
                    'last_name' => 'Manager1',
                ],
                [
                    'email' => 'manager2@gmail.com',
                    'password' => 'secret',
                    'first_name' => 'Manager2',
                    'last_name' => 'Manager2',
                ],
            ],
            'junior' => [
                [
                    'email' => 'junior1@gmail.com',
                    'password' => 'secret',
                    'first_name' => 'Junior1',
                    'last_name' => 'Junior1',
                ],
                [
                    'email' => 'junior2@gmail.com',
                    'password' => 'secret',
                    'first_name' => 'Junior2',
                    'last_name' => 'Junior2',
                ],
            ],
        ];

        collect($users)->each(function($users, $role) {
            collect($users)->each(function($user) use ($role) {
                $user['role'] = $role;
                $user['status'] = 'active';
                $user['password'] = bcrypt($user['password']);
                User::create($user);
            });
        });
    }
}
