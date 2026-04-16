<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // condition
            [
                'name' => 'Admin',
                'user_name' => 'Admin',
                'role' => 1, // 1 = Admin
                'password' => Hash::make('Admin'), // default password
            ]
        );
    }
}

