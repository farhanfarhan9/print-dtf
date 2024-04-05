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
        $title = 'Data Best Produk';
        if ($this->startDate && $this->endDate) {
            $title .= " periode {$this->startDate} sampai {$this->endDate}";
        }

        return [
            [$title],
            [], // Empty row for spacing
            ['No', 'Product Terjual', 'Nama Product'], // Actual column headings for products
        ];
    }

    public function map($product): array
    {
        return [
            ++$this->currentRow, // No
            $product['total_sold'], // Product Terjual
            $product['product_name'], // Nama Product
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the title row
        $sheet->mergeCells('A1:j1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

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
        $sheet->getStyle('A3:C' . (3 + count($this->productsSold)))->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Product Sales Data';
    }
}
