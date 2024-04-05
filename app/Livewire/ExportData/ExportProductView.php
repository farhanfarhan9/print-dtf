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
        $startDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->format('Y-m-d') : null;
        $endDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->format('Y-m-d') : null;

        $productsSold = $this->getProductsSold();

        return Excel::download(new ProductsExport($productsSold, $startDate, $endDate), 'products_sold.xlsx');
    }

    private function getProductsSold()
    {
        $query = PurchaseOrder::query()
            ->with('product')
            ->select('product_id', DB::raw('SUM(qty) as total_sold'))
            ->where('status', 'Lunas');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
        }

        return $query->groupBy('product_id')->get()->map(function ($order) {
            return [
                'total_sold' => $order->total_sold,
                'product_name' => $order->product->nama_produk, // Assuming the related product has a 'name' attribute
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
