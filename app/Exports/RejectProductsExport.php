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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RejectProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithPreCalculateFormulas
{
    private $rejectProducts;
    private $currentRow = 0;
    private $totalStok = 0;
    private $productSummary = [];

    public function __construct($rejectProducts)
    {
        $this->rejectProducts = $rejectProducts;

        // Calculate total stok
        $this->totalStok = $this->rejectProducts->sum('stok');

        // Calculate summary by product
        $this->calculateProductSummary();
    }

    private function calculateProductSummary()
    {
        // Group by product and sum stok
        $groupedProducts = $this->rejectProducts->groupBy('product.nama_produk');

        foreach ($groupedProducts as $productName => $items) {
            $this->productSummary[] = [
                'product_name' => $productName,
                'total_stok' => $items->sum('stok')
            ];
        }
    }

    public function collection()
    {
        return $this->rejectProducts;
    }

    public function headings(): array
    {
        return [
            ['Reject Product'], // Title
            [], // Empty row for spacing
            ['No', 'NAMA PRODUK', 'STOK', 'TANGGAL'], // Column headings
        ];
    }

    public function map($reject): array
    {
        return [
            ++$this->currentRow,
            $reject->product->nama_produk,
            $reject->stok,
            Carbon::parse($reject->created_at)->format('d F Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set the title row styles
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->mergeCells('A1:D1'); // Merge title cells
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set the header row styles
        $sheet->getStyle('A3:D3')->getFont()->setBold(true);

        // Define the style array for borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Apply the style to the data range
        $lastDataRow = 3 + count($this->rejectProducts);
        $sheet->getStyle('A3:D' . $lastDataRow)->applyFromArray($styleArray);

        // Add total row
        $totalRow = $lastDataRow + 1;
        $sheet->setCellValue('B' . $totalRow, 'TOTAL');
        $sheet->setCellValue('C' . $totalRow, $this->totalStok);
        $sheet->getStyle('B' . $totalRow . ':C' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . $totalRow . ':C' . $totalRow)->applyFromArray($styleArray);

        // Add product summary section after 2 rows
        $summaryStartRow = $totalRow + 3;
        $sheet->setCellValue('A' . $summaryStartRow, 'PRODUCT SUMMARY');
        $sheet->getStyle('A' . $summaryStartRow)->getFont()->setBold(true);
        $sheet->mergeCells('A' . $summaryStartRow . ':D' . $summaryStartRow);

        // Add product summary headers
        $summaryHeaderRow = $summaryStartRow + 1;
        $sheet->setCellValue('A' . $summaryHeaderRow, 'NAMA PRODUK');
        $sheet->setCellValue('B' . $summaryHeaderRow, 'TOTAL REJECT');
        $sheet->getStyle('A' . $summaryHeaderRow . ':B' . $summaryHeaderRow)->getFont()->setBold(true);

        // Add product summary data
        $currentSummaryRow = $summaryHeaderRow + 1;
        foreach ($this->productSummary as $item) {
            $sheet->setCellValue('A' . $currentSummaryRow, $item['product_name']);
            $sheet->setCellValue('B' . $currentSummaryRow, $item['total_stok']);
            $currentSummaryRow++;
        }

        // Apply borders to summary section
        $sheet->getStyle('A' . $summaryHeaderRow . ':B' . ($currentSummaryRow - 1))->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Reject Products';
    }
}
