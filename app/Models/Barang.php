<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_barang', 'nama_barang', 'jenis_barang',
        'keterangan', 'stok_baik', 'stok_rusak', 'harga_default'
    ];


    // relasi ke DetailBarang
    public function details()
    {
        return $this->hasMany(DetailBarang::class);
    }

    public function mutasiKondisis()
    {
        return $this->hasMany(MutasiKondisi::class);
    }
}

