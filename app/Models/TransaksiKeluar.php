<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiKeluar extends Model
{
    use HasFactory;

    // Tambahkan 'created_at' ke fillable
    protected $fillable = [
        'kode_transaksi',
        'qty',
        'customer',
        'keterangan_keluar',
        'created_at'
    ];

    public function details()
    {
        return $this->hasMany(DetailBarang::class, 'transaksi_keluar_id');
    }
}
