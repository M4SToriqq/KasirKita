<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected string $dateFrom;
    protected string $dateTo;

    public function __construct(string $dateFrom, string $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }

    public function collection(): Collection
    {
        return Transaction::with(['user', 'details.product'])
            ->completed()
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return ['Invoice', 'Tanggal', 'Kasir', 'Total', 'Dibayar', 'Kembalian', 'Metode Pembayaran', 'Produk'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Invoice
            'B' => 22,  // Tanggal
            'C' => 15,  // Kasir
            'D' => 15,  // Total
            'E' => 15,  // Dibayar
            'F' => 15,  // Kembalian
            'G' => 20,  // Metode Pembayaran
            'H' => 60,  // Produk
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Border semua data
        $sheet->getStyle('A1:H' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        // Wrap text kolom Produk
        $sheet->getStyle('H2:H' . $lastRow)->getAlignment()->setWrapText(true);

        // Zebra striping baris data
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF9FAFB'],
                    ],
                ]);
            }
        }

        // Style header (baris 1)
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'], // indigo
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function map(Transaction $transaction): array
    {
        $details = $transaction->details ?? collect();

        $products = $details->map(function ($d) {
            $productName = $d->product?->name ?? 'Deleted';
            $qty         = is_numeric($d->quantity) ? (int) $d->quantity : $d->quantity;
            return $productName . ' x' . $qty;
        })->implode(' | ');

        return [
            (string) ($transaction->invoice_number ?? ''),
            optional($transaction->created_at)->format('Y-m-d H:i:s') ?? '',
            (string) ($transaction->user?->name ?? 'Unknown'),
            (float) ($transaction->total_price ?? 0),
            (float) ($transaction->total_paid ?? 0),
            (float) ($transaction->change ?? 0),
            (string) ($transaction->payment_method ?? ''),
            (string) $products,
        ];
    }
}
