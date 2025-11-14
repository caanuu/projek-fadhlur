<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    public function run()
    {
        // User Kasir
        DB::table('users')->updateOrInsert(
            ['email' => 'kasir@ranggasoly.com'],
            [
                'name' => 'Kasir User',
                'email' => 'kasir@ranggasoly.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // User Gudang
        DB::table('users')->updateOrInsert(
            ['email' => 'gudang@ranggasoly.com'],
            [
                'name' => 'Gudang User',
                'email' => 'gudang@ranggasoly.com',
                'password' => Hash::make('password123'),
                'role' => 'gudang',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
