<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TransaksiKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi', 'qty', 'customer', 'keterangan_keluar'
    ];

    // public function details()
    // {
    //     // return $this->hasMany(DetailKeluar::class);
    // }

    public function details()
    {
        return $this->hasMany(DetailBarang::class, 'transaksi_keluar_id');
    }
}
