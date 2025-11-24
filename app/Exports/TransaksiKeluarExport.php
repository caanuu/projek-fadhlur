<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransaksiKeluarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        return ['No', 'Kode Transaksi', 'Tanggal', 'Customer', 'Detail Barang', 'Total Qty', 'Total Pendapatan', 'Keterangan'];
    }

    public function map($trx): array
    {
        $detailStr = $trx->details->map(fn($d) => "• " . $d->barang->nama_barang . " (" . $d->jumlah . "x @ " . number_format($d->harga_jual) . ")")->join("\n");
        $total = $trx->details->sum(fn($d) => $d->jumlah * $d->harga_jual);

        return [
            $trx->id,
            $trx->kode_transaksi,
            $trx->created_at->format('d-m-Y H:i'),
            $trx->customer,
            $detailStr,
            $trx->qty,
            "Rp " . number_format($total, 0, ',', '.'),
            $trx->keterangan_keluar
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:H$lastRow")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP]
        ]);
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '16A34A']], // HIJAU
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle("E2:E$lastRow")->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('E')->setWidth(45);
    }
}
