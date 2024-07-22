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
    private $totals = [];

    public function __construct($purchasesData, $startDate = null, $endDate = null)
    {
        $this->purchasesData = $purchasesData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $data = collect($this->purchasesData);
        $grouped = $data->groupBy(function ($item) {
            $date = Carbon::parse($item['purchase_date'])->format('Y-m-d');
            $time = Carbon::parse($item['purchase_date'])->format('H:i');
            $shift = (strtotime($time) >= strtotime('09:00') && strtotime($time) <= strtotime('16:59')) ? 'Shift 1' : 'Shift 2';
            return $date . ' ' . $shift;
        });

        foreach ($grouped as $key => $group) {
            $this->totals[$key] = $group->sum('amount');
        }
    }

    public function collection()
    {
        $data = collect($this->purchasesData);
        $total = $data->sum('amount');

        // Calculate the total for cash purchases
        $totalCash = $data->where('bank_detail', 'CASH')->sum('amount');

        // Add rows for the total and total cash at the end
        $data->push([
            'customer_name' => 'Total',
            'amount' => $total,
            'bank_detail' => '',
            'purchase_date' => '',
            'shift' => '',
            'total_per_shift_per_day' => ''
        ]);

        $data->push([
            'customer_name' => 'Total Cash',
            'amount' => $totalCash,
            'bank_detail' => '',
            'purchase_date' => '',
            'shift' => '',
            'total_per_shift_per_day' => ''
        ]);

        $data->push([
            'customer_name' => 'Total Tanpa Cash',
            'amount' => $total - $totalCash,
            'bank_detail' => '',
            'purchase_date' => '',
            'shift' => '',
            'total_per_shift_per_day' => ''
        ]);

        return $data;
    }

    public function headings(): array
    {
        $title = 'Data Pembukuan';
        if ($this->startDate && $this->endDate) {
            $title2 = "Periode {$this->startDate} sampai {$this->endDate}";
        } else {
            $title2 = "Data Keseluruhan";
        }

        return [
            [$title],
            [$title2],
            [], // Empty row for spacing
            ['No', 'Nama Customer', 'Harga', 'Bank', 'Tanggal Pembelian', 'Shift', 'Total Per Shift Per Day'], // Updated column headings
        ];
    }

    public function map($bookkeeping): array
    {
        // Determine the shift based on purchase time
        $shift = '';
        $totalPerShiftPerDay = '';
        if ($bookkeeping['purchase_date']) {
            $date = Carbon::parse($bookkeeping['purchase_date'])->format('Y-m-d');
            $time = Carbon::parse($bookkeeping['purchase_date'])->format('H:i');
            $shift = (strtotime($time) >= strtotime('09:00') && strtotime($time) <= strtotime('16:59')) ? 'Shift 1' : 'Shift 2';
            $key = $date . ' ' . $shift;
            $totalPerShiftPerDay = isset($this->totals[$key]) ? rupiah_format($this->totals[$key]) : '';
        }

        // Check if it's the total row
        if ($bookkeeping['customer_name'] == 'Total' || $bookkeeping['customer_name'] == 'Total Cash' || $bookkeeping['customer_name'] == 'Total Tanpa Cash') {
            return [
                '', // No number for total
                $bookkeeping['customer_name'],
                rupiah_format($bookkeeping['amount']),
                '',
                '',
                '',
                ''
            ];
        }

        return [
            ++$this->currentRow,
            $bookkeeping['customer_name'],
            rupiah_format($bookkeeping['amount']),
            $bookkeeping['bank_detail'],
            $bookkeeping['purchase_date'],
            $shift,
            $totalPerShiftPerDay
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the title row
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('left');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);

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
        $sheet->getStyle('A4:G' . (4 + count($this->purchasesData)))->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Merge cells for the "Total Per Shift Per Day" column
        $this->mergeTotalPerShiftPerDayCells($sheet);

        $sheet->getStyle('H5')->getAlignment()->setHorizontal('right');
    }

    private function mergeTotalPerShiftPerDayCells(Worksheet $sheet)
    {
        $data = $this->purchasesData;
        $row = 5; // Starting row of the data (excluding headers)
        $previousKey = null;
        $mergeStartRow = $row;

        foreach ($data as $item) {
            $date = Carbon::parse($item['purchase_date'])->format('Y-m-d');
            $time = Carbon::parse($item['purchase_date'])->format('H:i');
            $shift = (strtotime($time) >= strtotime('09:00') && strtotime($time) <= strtotime('16:59')) ? 'Shift 1' : 'Shift 2';
            $key = $date . ' ' . $shift;

            if ($previousKey && $previousKey != $key) {
                $sheet->mergeCells("G{$mergeStartRow}:G" . ($row - 1));
                $mergeStartRow = $row;
            }

            $previousKey = $key;
            $row++;
        }

        // Merge the last group of cells
        $sheet->mergeCells("G{$mergeStartRow}:G" . ($row - 1));
    }

    public function title(): string
    {
        return 'Bookkeeping Data';
    }
}
