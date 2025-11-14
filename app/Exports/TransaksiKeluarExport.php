<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiKeluarExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Customer',
            'Tanggal',
            'Barang',
            'Jumlah',
            'Harga Jual',
            'Total Pendapatan',
        ];
    }

    public function map($trx): array
    {
        $barangList = $trx->details->pluck('barang.nama_barang')->join(', ');
        $jumlahList = $trx->details->pluck('jumlah')->join(', ');
        $hargaList  = $trx->details->pluck('harga_jual')->join(', ');

        $totalPendapatan = $trx->details->sum(fn($d) => $d->jumlah * $d->harga_jual);

        return [
            $trx->kode_transaksi,
            $trx->customer,
            $trx->created_at->format('Y-m-d H:i'),
            $barangList,
            $jumlahList,
            $hargaList,
            'Rp ' . number_format($totalPendapatan, 0, ',', '.'),
        ];
    }
}
