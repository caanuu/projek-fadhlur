<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barangs')->updateOrInsert(
            ['kode_barang' => 'Gelas-001'], // kondisi unik
            [
                'nama_barang' => 'Gelas Plastik',
                'jenis_barang' => 'Alat Minum',
                'keterangan' => 'Gelas plastik bening',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('barangs')->updateOrInsert(
            ['kode_barang' => 'Piring-002'], // kondisi unik
            [
                'nama_barang' => 'Piring Kaca',
                'jenis_barang' => 'Alat Makan',
                'keterangan' => 'Piring kaca bulat',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

}
