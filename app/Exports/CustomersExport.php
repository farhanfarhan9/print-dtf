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

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithPreCalculateFormulas
{
    private $customerOrders;
    private $startDate;
    private $endDate;
    private $currentRow = 0;

    public function __construct($customerOrders, $startDate = null, $endDate = null)
    {
        $this->customerOrders = $customerOrders;
        $this->startDate = $startDate ? Carbon::parse($startDate)->isoFormat('dddd, D MMM YYYY') : null;
        $this->endDate = $endDate ? Carbon::parse($endDate)->isoFormat('dddd, D MMM YYYY') : null;
    }

    public function collection()
    {
        // We don't need to set 'no' here anymore, we will handle it in map method
        return collect($this->customerOrders);
    }

    public function headings(): array
    {
        // Construct the title with or without dates
        $title = 'Data Best Customer';
        if ($this->startDate && $this->endDate) {
            $title .= " periode {$this->startDate} sampai {$this->endDate}";
        }

        return [
            [$title], // Title with formatted dates or without dates
            [], // Empty row for spacing
            ['No', 'Jumlah Order', 'Nama', 'Alamat', 'No Tlpn', 'Email'], // Actual column headings
        ];
    }

    public function map($order): array
    {
        $this->currentRow++; // Increment the current row count for each item
        return [
            $this->currentRow,
            $order['jumlah_order'],
            $order['nama_customer'],
            $order['alamat'],
            $order['phone'],
            $order['email']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set the title row styles
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFont()->setSize(14);
        $sheet->mergeCells('A1:F1'); // Merge title cells

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
        $sheet->getStyle('A3:F' . (3 + count($this->customerOrders)))->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Customer Data';
    }
}
