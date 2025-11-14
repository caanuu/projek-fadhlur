<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_masuk_id',
        'barang_id',
        'status',
        'jumlah',
        'harga_beli',
        'harga_jual',
        'transaksi_keluar_id'
    ];

    // relasi ke TransaksiMasuk
    public function transaksiMasuk()
    {
        return $this->belongsTo(TransaksiMasuk::class);
    }

    public function transaksiKeluar()
    {
        return $this->belongsTo(TransaksiKeluar::class);
    }

    // relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
    
}
