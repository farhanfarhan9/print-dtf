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

class DebtCustomerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithPreCalculateFormulas
{
    private $debtCustomers;
    private $currentRow = 0;
    private $totalDebt = 0;

    public function __construct($debtCustomers)
    {
        // Filter out any customers with zero or negative debt
        $this->debtCustomers = $debtCustomers->filter(function($item) {
            $remainingDebt = is_object($item) ?
                ($item->total_debt - $item->total_paid) :
                ($item['total_debt'] - $item['total_paid']);
            return $remainingDebt > 0;
        });

        // Calculate total debt
        $this->totalDebt = $this->debtCustomers->sum(function($item) {
            return is_object($item) ? ($item->total_debt - $item->total_paid) : ($item['total_debt'] - $item['total_paid']);
        });
    }

    public function collection()
    {
        // Return the filtered collection
        return $this->debtCustomers;
    }

    public function headings(): array
    {
        return [
            ['Debt Customer Export'], // Title
            [], // Empty row for spacing
            ['NO', 'Customer ID', 'Customer Name', 'Sisa Hutang', 'Tanggal Terakhir Bayar'], // Column headings
        ];
    }

    public function map($customer): array
    {
        $this->currentRow++;

        // Format the date
        $lastPaymentDate = is_object($customer) ? $customer->last_payment_date : $customer['last_payment_date'];
        $formattedDate = $lastPaymentDate ?
            Carbon::parse($lastPaymentDate)->format('d M Y') :
            'Belum ada pembayaran';

        // Calculate remaining debt
        $remainingDebt = is_object($customer) ?
            ($customer->total_debt - $customer->total_paid) :
            ($customer['total_debt'] - $customer['total_paid']);

        // Format the debt amount with rupiah format
        $formattedDebt = 'Rp ' . number_format($remainingDebt, 0, ',', '.');

        return [
            $this->currentRow,
            is_object($customer) ? $customer->customer_id : $customer['customer_id'],
            is_object($customer) ? $customer->customer_name : $customer['customer_name'],
            $formattedDebt,
            $formattedDate,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the last row number
        $lastRow = 3 + count($this->debtCustomers);
        $totalRow = $lastRow + 1;

        // Set the title row styles
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFont()->setSize(14);
        $sheet->mergeCells('A1:E1'); // Merge title cells
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set header row styles
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add total row in column C with the value in column D
        $sheet->setCellValue('C' . $totalRow, 'Total Hutang');
        $sheet->setCellValue('D' . $totalRow, 'Rp ' . number_format($this->totalDebt, 0, ',', '.'));
        $sheet->getStyle('C' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('D' . $totalRow)->getFont()->setBold(true);

        // Define the style array for borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Apply the style from the third row to the end of the data including total row
        $sheet->getStyle('A3:E' . $totalRow)->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Debt Customer';
    }
}
