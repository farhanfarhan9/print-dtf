<?php

namespace App\Livewire\ExportData;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;

class ExportProductView extends Component
{
    public $search;
    public $startDate;
    public $endDate;

    public function exportExcel()
    {
        $formattedStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->format('d-m-Y') : '';
        $formattedEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->format('d-m-Y') : '';

        $filename = 'data_produk';
        $filename .= $formattedStartDate ? "_{$formattedStartDate}" : '';
        $filename .= $formattedEndDate ? "_-_$formattedEndDate" : '';
        $filename .= '.xlsx';

        $productsSold = $this->getProductsSold();

        // Convert the original date format to a display format only if dates are set
        $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->isoFormat('dddd, D MMMM YYYY') : null;
        $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->isoFormat('dddd, D MMMM YYYY') : null;

        return Excel::download(new ProductsExport($productsSold, $displayStartDate, $displayEndDate), $filename);
    }

    private function getProductsSold()
    {
        $query = PurchaseOrder::query()
            ->with('product')
            ->select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(product_price) as total_omzet'))
            ->where('status', 'Lunas');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
        }

        return $query->groupBy('product_id')->get()->map(function ($order) {
            return [
                'total_sold' => $order->total_sold,
                'total_omzet' => $order->total_omzet,
                'product_name' => optional($order->product)->nama_produk, // Assuming the related product has a 'name' attribute
            ];
        });
    }

    public function render()
    {
        $productsSold = $this->getProductsSold();

        return view('livewire.export-data.export-product-view', [
            'productsSold' => $productsSold
        ]);
    }
}
