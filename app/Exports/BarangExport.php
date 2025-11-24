<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BarangExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Ambil barang beserta detail transaksi masuk untuk mencari harga terakhir
        return Barang::with([
            'details' => function ($q) {
                $q->whereNotNull('transaksi_masuk_id')->latest();
            }
        ])->get();
    }

    public function headings(): array
    {
        return ['No', 'Kode Barang', 'Nama Barang', 'Jenis', 'Stok Baik', 'Stok Rusak', 'Harga Beli Terakhir', 'Aset (Stok Baik * Harga)', 'Keterangan', 'Update Terakhir'];
    }

    public function map($barang): array
    {
        $lastDetail = $barang->details->first();
        $hargaTerakhir = $lastDetail ? $lastDetail->harga_beli : 0;
        $nilaiAset = $barang->stok_baik * $hargaTerakhir;

        return [
            $barang->id,
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->jenis_barang,
            $barang->stok_baik,
            $barang->stok_rusak,
            "Rp " . number_format($hargaTerakhir, 0, ',', '.'),
            "Rp " . number_format($nilaiAset, 0, ',', '.'),
            $barang->keterangan,
            $barang->updated_at->format('d-m-Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:J$lastRow")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4B5563']], // ABU GELAP
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }
}
