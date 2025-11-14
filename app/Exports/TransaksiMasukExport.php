<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiMasukExport implements FromCollection, WithHeadings, WithMapping
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
            'Supplier',
            'Tanggal',
            'Barang',
            'Jumlah',
            'Harga Beli',
            'Total Pengeluaran',
            'Pegawai'
        ];
    }

    public function map($trx): array
    {
        $barangList = $trx->details->pluck('barang.nama_barang')->join(', ');
        $jumlahList = $trx->details->pluck('jumlah')->join(', ');
        $hargaList  = $trx->details->pluck('harga_beli')->join(', ');

        $totalPengeluaran = $trx->details->sum(fn($d) => $d->jumlah * $d->harga_beli);

        return [
            $trx->kode_transaksi,
            $trx->supplier,
            $trx->created_at->format('Y-m-d H:i'),
            $barangList,
            $jumlahList,
            $hargaList,
            'Rp ' . number_format($totalPengeluaran, 0, ',', '.'),
            $trx->pegawai_penerima,
        ];
    }
}
