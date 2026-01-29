<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $admin = new User();
        $admin->name = 'Admin User';
        $admin->email = 'admin@intelli-library.com';
        $admin->password = Hash::make('password123');
        $admin->role = User::ROLE_ADMIN;
        $admin->save();

        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@test.com';
        $user->password = Hash::make('password');
        $user->role = User::ROLE_USER;
        $user->save();
    }
}
