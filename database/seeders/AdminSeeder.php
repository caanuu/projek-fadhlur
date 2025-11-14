<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@ranggasoly.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@ranggasoly.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
