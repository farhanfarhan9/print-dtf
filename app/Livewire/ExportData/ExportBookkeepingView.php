<?php

namespace App\Livewire\ExportData;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use App\Models\Payment;
use App\Exports\BookkeepingExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;

class ExportBookkeepingView extends Component
{
    public $search;
    public $startDate;
    public $endDate;
    public $viewMode = 'daily';  // Default to daily

    public function switchToDaily()
    {
        $this->viewMode = 'daily';
        $this->startDate = null;
        $this->endDate = null;
    }

    public function switchToMonthly()
    {
        $this->viewMode = 'monthly';
        $this->startDate = null;
        $this->endDate = null;
    }

    public function exportExcel()
    {
        if (($this->startDate == null && $this->endDate == null) || ($this->startDate != null && $this->endDate != null)) {
            // Set Carbon's locale to Indonesian
            Carbon::setLocale('id');

            if($this->viewMode == 'monthly'){
                $formattedStartDate = $this->startDate ? Carbon::createFromFormat('Y-m', $this->startDate)->format('m-Y') : '';
                $formattedEndDate = $this->endDate ? Carbon::createFromFormat('Y-m', $this->endDate)->format('m-Y') : '';
            }else{
                $formattedStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->format('d-m-Y') : '';
                $formattedEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->format('d-m-Y') : '';
            }
            $filename = 'data_pembukuan';
            $filename .= $formattedStartDate ? "_{$formattedStartDate}" : '';
            $filename .= $formattedEndDate ? "_-_$formattedEndDate" : '';
            $filename .= '.xlsx';

            $bookkeepingDaily = $this->getPurchasesData();

            // Convert the original date format to a display format only if dates are set
            if($this->viewMode == 'monthly'){
                $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m', $this->startDate)->isoFormat('MMMM YYYY') : null;
                $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m', $this->endDate)->isoFormat('MMMM YYYY') : null;
            }else{
                $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->isoFormat('dddd, D MMMM YYYY') : null;
                $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->isoFormat('dddd, D MMMM YYYY') : null;
            }
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

        // Check if start and end dates are set and add them to the query
        if ($this->startDate && $this->endDate) {
            if($this->viewMode == 'monthly'){
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
            }else{
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
            }

            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->groupBy('product_id')->get()->map(function ($order) {
            return [
                'total_sold' => $order->total_sold,
                'total_omzet' => $order->total_omzet,
                'product_name' => optional($order->product)->nama_produk, // Assuming the related product has a 'name' attribute
            ];
        });
    }

    private function getGroupDailyPurchasesData()
    {
        $query = Payment::orderBy('created_at', 'asc');

        // Check if start and end dates are set and add them to the query
        if ($this->startDate && $this->endDate) {
            if($this->viewMode == 'monthly'){
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
            }else{
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
            }

            $query->whereBetween('created_at', [$start, $end]);
        }

        // Retrieve the payments, apply transformations
        $purchases = $query->get()->map(function ($purchase) {
            return [
                'customer_name' => optional($purchase->purchase->customer)->name, // Safe access using optional
                'amount' => optional($purchase)->amount, // Safe access to amount
                'bank_detail' => optional($purchase)->bank_detail, // Safe access to bank detail
                'purchase_date' => $purchase->created_at->format('Y-m-d'), // Format the date as Y-m-d
            ];
        });

        // Group the data by purchase_date
        // This will group all purchase details under each date
        $groupedPurchases = $purchases->groupBy('purchase_date')->map(function ($datePurchases) {
            return $datePurchases->map(function ($purchase) {
                return [
                    'customer_name' => $purchase['customer_name'],
                    'amount' => $purchase['amount'],
                    'bank_detail' => $purchase['bank_detail'],
                ];
            });
        });

        return $groupedPurchases;
    }

    private function getGroupMonthlyPurchasesData()
    {
        $query = Payment::orderBy('created_at', 'asc');

        // Check if start and end dates are set and add them to the query
        if ($this->startDate && $this->endDate) {
            if($this->viewMode == 'monthly'){
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay();  // Adjusts time to 23:59:59
            }else{
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfMonth()->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfMonth()->endOfDay();  // Adjusts time to 23:59:59
            }


            $query->whereBetween('created_at', [$start, $end]);
        }

        // Retrieve the payments, apply transformations
        $purchases = $query->get()->map(function ($purchase) {
            return [
                'customer_name' => optional($purchase->purchase->customer)->name, // Safe access using optional
                'amount' => optional($purchase)->amount, // Safe access to amount
                'bank_detail' => optional($purchase)->bank_detail, // Safe access to bank detail
                'purchase_month' => $purchase->created_at->format('Y-m'), // Format the date as Year-Month
            ];
        });

        // Group the data by purchase_month
        $groupedPurchases = $purchases->groupBy('purchase_month')->map(function ($monthPurchases) {
            return $monthPurchases->map(function ($purchase) {
                return [
                    'customer_name' => $purchase['customer_name'],
                    'amount' => $purchase['amount'],
                    'bank_detail' => $purchase['bank_detail'],
                ];
            });
        });

        return $groupedPurchases;
    }

    private function getPurchasesData()
    {
        $query = Payment::orderBy('created_at', 'asc');

        // Check if start and end dates are set and add them to the query
        if ($this->startDate && $this->endDate) {
            if($this->viewMode == 'monthly'){
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay();  // Adjusts time to 23:59:59
            }else{
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
            }
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Retrieve the payments, apply transformations
        $purchases = $query->get()->map(function ($purchase) {
            return [
                'customer_name' => optional($purchase->purchase->customer)->name, // Safe access using optional
                'amount' => optional($purchase)->amount, // Safe access to amount
                'bank_detail' => optional($purchase)->bank_detail, // Safe access to amount
                'purchase_date' => $purchase->created_at->format('Y-m-d, H:i:s'), // Format the date as desired
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
        $dailyGroupPurchases = $this->getGroupDailyPurchasesData();
        $monthlyGroupPurchases = $this->getGroupMonthlyPurchasesData();

        return view('livewire.export-data.export-bookkeeping-view', [
            'productsSold' => $productsSold,
            'dailyPurchases' => $dailyPurchases,
            'dailyGroupPurchases' => $dailyGroupPurchases,
            'monthlyGroupPurchases' => $monthlyGroupPurchases,
            'viewMode' => $this->viewMode,
        ]);
    }
}
