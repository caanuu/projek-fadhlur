<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MutasiKondisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id', 'tanggal', 'jumlah', 'from_status', 'to_status', 'keterangan'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
