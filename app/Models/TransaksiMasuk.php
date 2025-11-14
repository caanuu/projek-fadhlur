<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TransaksiMasuk extends Model
{
    use HasFactory;
        protected $fillable = [
        'kode_transaksi', 
        'qty', 
        'supplier', 
        'pegawai_penerima',
        'keterangan_masuk'
    ];

    public function detailBarangs()
    {
        return $this->hasMany(DetailBarang::class, 'transaksi_masuk_id');
    }
    
    public function details()
    {
        return $this->hasMany(DetailBarang::class);
    }
}
