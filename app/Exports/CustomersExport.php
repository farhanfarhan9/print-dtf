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
use Illuminate\Support\Collection;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithPreCalculateFormulas
{
    private $customerOrders;
    private $startDate;
    private $endDate;
    private $currentRow = 0;

    public function __construct($customerOrders, $startDate = null, $endDate = null)
    {
        $this->customerOrders = $customerOrders;
        $this->startDate = $startDate ? $startDate : null;
        $this->endDate = $endDate ? $endDate : null;
    }

    public function collection()
    {
        // Convert to collection if it's not already
        $data = $this->customerOrders instanceof Collection
            ? $this->customerOrders
            : collect($this->customerOrders);

        // Calculate totals - handle both object and array access
        $totalOrder = $data->sum(function($item) {
            return is_object($item) ? $item->jumlah_order : $item['jumlah_order'];
        });

        $totalFrekuensi = $data->sum(function($item) {
            return is_object($item) ? $item->frekuensi : $item['frekuensi'];
        });

        // Add a summary row as an object to match the data format
        $data->push((object)[
            'jumlah_order' => $totalOrder,
            'nama_customer' => '.',
            'frekuensi' => $totalFrekuensi,
        ]);

        return $data;
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
            ['No', 'Jumlah Order (m)', 'Nama', 'Frekuensi Pembelian'], // Actual column headings
        ];
    }

    public function map($order): array
    {
        // Check if we're dealing with the summary row (last row)
        if (is_object($order) && $order->nama_customer == '.') {
            // Format the jumlah_order with thousands separator and add (m)
            $formattedJumlahOrder = number_format($order->jumlah_order, 0, ',', '.') . ' (m)';

            return [
                '',
                $formattedJumlahOrder,
                $order->nama_customer,
                $order->frekuensi,
            ];
        } elseif (is_array($order) && $order['nama_customer'] == '.') {
            // Handle array format for backward compatibility
            $formattedJumlahOrder = number_format($order['jumlah_order'], 0, ',', '.') . ' (m)';

            return [
                '',
                $formattedJumlahOrder,
                $order['nama_customer'],
                $order['frekuensi'],
            ];
        }

        $this->currentRow++; // Increment the current row count for each item

        // Handle both object and array formats
        if (is_object($order)) {
            return [
                $this->currentRow,
                $order->jumlah_order,
                $order->nama_customer,
                $order->frekuensi,
            ];
        } else {
            return [
                $this->currentRow,
                $order['jumlah_order'],
                $order['nama_customer'],
                $order['frekuensi'],
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Set the title row styles
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFont()->setSize(14);
        $sheet->mergeCells('A1:D1'); // Merge title cells

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
        $sheet->getStyle('A3:D' . (3 + count($this->customerOrders)))->applyFromArray($styleArray);

        // Set auto-sizing for the columns
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Customer Data';
    }
}
