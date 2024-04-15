<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithPreCalculateFormulas
{
    private $productsSold;
    private $startDate;
    private $endDate;
    private $currentRow = 0;

    public function __construct($productsSold, $startDate = null, $endDate = null)
    {
        $this->productsSold = $productsSold;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return collect($this->productsSold);
    }

    public function headings(): array
    {
        $title = 'Top Produk';
        if ($this->startDate && $this->endDate) {
            $title2 = "Periode {$this->startDate} sampai {$this->endDate}";
        }else{
            $title2 = "Data Keseluruhan";
        }

        return [
            [$title],
            [$title2],
            [], // Empty row for spacing
            ['No', 'Nama Product', 'Deskripsi', 'Jenis Produk', 'Jumlah Terjual', 'Total Omzet (Net Sales)'], // Actual column headings for products
        ];
    }

    public function map($product): array
    {
        $formattedOmzet = "Rp " . number_format($product['total_omzet'], 0, ',', '.');
        return [
            ++$this->currentRow, // No
            $product['product_name'], // Nama Product
            '',
            'Stok Sendiri',
            $product['total_sold'], // Product Terjual
            $formattedOmzet, // Total Omzet
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the title row
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('left');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);

        // Define the style array for borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Apply the style from the third row to the end of the data
        $sheet->getStyle('A4:F' . (4 + count($this->productsSold)))->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->getStyle('F5')->getAlignment()->setHorizontal('right');
    }

    public function title(): string
    {
        return 'Product Sales Data';
    }
}
