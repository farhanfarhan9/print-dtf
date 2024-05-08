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

class BookkeepingExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithPreCalculateFormulas
{
    private $purchasesData;
    private $startDate;
    private $endDate;
    private $currentRow = 0;

    public function __construct($purchasesData, $startDate = null, $endDate = null)
    {
        $this->purchasesData = $purchasesData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data = collect($this->purchasesData);
        $total = $data->sum('amount'); // Assuming 'amount' is a key in your array

        // Add a row for the total at the end
        $data->push([
            'customer_name' => 'Total',
            'amount' => $total,
            'bank_detail' => '',
            'purchase_date' => ''
        ]);

        return $data;
    }

    public function headings(): array
    {
        $title = 'Data Pembukuan';
        if ($this->startDate && $this->endDate) {
            $title2 = "Periode {$this->startDate} sampai {$this->endDate}";
        }else{
            $title2 = "Data Keseluruhan";
        }

        return [
            [$title],
            [$title2],
            [], // Empty row for spacing
            ['No', 'Nama Customer', 'Harga', 'Bank', 'Tanggal Pembelian'], // Actual column headings for customer
            // ['No', 'Nama Customer', 'Status Pembelian', 'Tanggal Pembelian'], // Actual column headings for customer
        ];
    }

    public function map($bookkeeping): array
    {
        // Check if it's the total row
        if ($bookkeeping['customer_name'] == 'Total') {
            return [
                '', // No number for total
                $bookkeeping['customer_name'],
                rupiah_format($bookkeeping['amount']),
                '',
                ''
            ];
        }

        return [
            ++$this->currentRow,
            $bookkeeping['customer_name'],
            rupiah_format($bookkeeping['amount']),
            $bookkeeping['bank_detail'],
            $bookkeeping['purchase_date']
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
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);

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
        $sheet->getStyle('A4:E' . (4 + count($this->purchasesData)))->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->getStyle('F5')->getAlignment()->setHorizontal('right');
    }

    public function title(): string
    {
        return 'Bookkeeping Data';
    }
}
