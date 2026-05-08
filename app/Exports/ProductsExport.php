<?php

namespace App\Exports;

use App\Models\Product;
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

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Data Produk';
    }

    public function collection(): Collection
    {
        return Product::with('category')->get();
    }

    public function headings(): array
    {
        return ['SKU', 'Barcode', 'Nama', 'Kategori', 'Stok', 'Min Stok', 'Harga Beli', 'Harga Jual', 'Status'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // SKU
            'B' => 18,  // Barcode
            'C' => 30,  // Nama
            'D' => 18,  // Kategori
            'E' => 10,  // Stok
            'F' => 12,  // Min Stok
            'G' => 15,  // Harga Beli
            'H' => 15,  // Harga Jual
            'I' => 12,  // Status
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Border semua data
        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        // Zebra striping
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF9FAFB'],
                    ],
                ]);
            }
        }

        // Warna status: Aktif = hijau, Nonaktif = merah
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = $sheet->getCell('I' . $row)->getValue();
            $color  = $status === 'Aktif' ? 'FFD1FAE5' : 'FFFEE2E2';
            $sheet->getStyle('I' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
            ]);
        }

        // Style header
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF059669'], // emerald/green
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function map($product): array
    {
        return [
            $product->sku,
            $product->barcode,
            $product->name,
            $product->category->name ?? '',
            $product->stock_quantity,
            $product->min_stock,
            $product->purchase_price,
            $product->selling_price,
            $product->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
