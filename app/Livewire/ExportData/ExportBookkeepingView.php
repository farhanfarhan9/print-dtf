<?php

namespace App\Livewire\ExportData;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use App\Exports\BookkeepingExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;

class ExportBookkeepingView extends Component
{
    public $search;
    public $startDate;
    public $endDate;

    public function exportExcel()
    {
        if (($this->startDate == null && $this->endDate == null) || ($this->startDate != null && $this->endDate != null)) {
            // Set Carbon's locale to Indonesian
            Carbon::setLocale('id');

            $formattedStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->format('d-m-Y') : '';
            $formattedEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->format('d-m-Y') : '';

            $filename = 'data_pembukuan';
            $filename .= $formattedStartDate ? "_{$formattedStartDate}" : '';
            $filename .= $formattedEndDate ? "_-_$formattedEndDate" : '';
            $filename .= '.xlsx';

            $bookkeepingDaily = $this->getPurchasesData();

            // Convert the original date format to a display format only if dates are set
            $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->isoFormat('dddd, D MMMM YYYY') : null;
            $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->isoFormat('dddd, D MMMM YYYY') : null;

            return Excel::download(new BookkeepingExport($bookkeepingDaily, $displayStartDate, $displayEndDate), $filename);
        }else if ($this->startDate == null || $this->endDate == null){
            session()->flash('exportFailed');
            $this->redirect(route('export-bookkeeping.index'), navigate: true);
        }
    }

    private function getProductsSold()
    {
        $query = PurchaseOrder::query()
            ->with('product')
            ->select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(product_price) as total_omzet'))
            ->where('po_status', 'close');

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

    private function getPurchasesData()
    {
        $purchases = Purchase::with('customer')
        ->orderByDesc('created_at')
            ->get()
            ->map(function ($purchase) {
                return [
                    'customer_name' => optional($purchase->customer)->name, // Menggunakan optional untuk menghindari error jika customer tidak ditemukan
                    'payment_status' => $this->translatePaymentStatus($purchase->payment_status),
                    'purchase_date' => $purchase->created_at->format('Y-m-d, H:i:s'), // Format tanggal sesuai yang diinginkan
                ];
            });

        return $purchases;
    }

    /**
     * Translate the payment status from English to Indonesian.
     *
     * @param string $status
     * @return string
     */
    private function translatePaymentStatus($status)
    {
        return $status == 'open' ? 'Lunas' : ($status == 'close' ? 'Belum Lunas' : $status);
    }

    public function render()
    {
        $productsSold = $this->getProductsSold();
        $dailyPurchases = $this->getPurchasesData();

        return view('livewire.export-data.export-bookkeeping-view', [
            'productsSold' => $productsSold,
            'dailyPurchases' => $dailyPurchases
        ]);
    }
}
