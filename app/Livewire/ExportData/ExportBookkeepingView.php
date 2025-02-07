<?php

namespace App\Livewire\ExportData;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use App\Models\Payment;
use App\Exports\BookkeepingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator; // Import LengthAwarePaginator
use Carbon\Carbon;
use DB;

class ExportBookkeepingView extends Component
{
    public $search;
    public $startDate;
    public $endDate;
    public $viewMode = 'daily';  // Default to daily
    public $sortField = 'created_at'; // Default sort field
    public $sortDirection = 'desc'; // Default sort direction

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

            // Format the dates for the filename
            if ($this->viewMode == 'monthly') {
                $formattedStartDate = $this->startDate ? Carbon::createFromFormat('Y-m', $this->startDate)->format('m-Y') : '';
                $formattedEndDate = $this->endDate ? Carbon::createFromFormat('Y-m', $this->endDate)->format('m-Y') : '';
            } else {
                $formattedStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->format('d-m-Y') : '';
                $formattedEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->format('d-m-Y') : '';
            }

            $filename = 'data_pembukuan';
            $filename .= $formattedStartDate ? "_{$formattedStartDate}" : '';
            $filename .= $formattedEndDate ? "_-_$formattedEndDate" : '';
            $filename .= '.xlsx';

            // Get the purchase data
            $bookkeepingDaily = $this->getPurchasesData();

            // Get the sum of additional prices
            $totalAdditionalPrices = $this->getAdditionalPrices();

            // Convert the original date format to a display format only if dates are set
            if ($this->viewMode == 'monthly') {
                $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m', $this->startDate)->isoFormat('MMMM YYYY') : null;
                $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m', $this->endDate)->isoFormat('MMMM YYYY') : null;
            } else {
                $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->isoFormat('dddd, D MMMM YYYY') : null;
                $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->isoFormat('dddd, D MMMM YYYY') : null;
            }

            // Export the Excel file, passing both purchase data and additional prices
            return Excel::download(new BookkeepingExport($bookkeepingDaily, $displayStartDate, $displayEndDate, $totalAdditionalPrices), $filename);
        } else if ($this->startDate == null || $this->endDate == null) {
            session()->flash('exportFailed');
            $this->redirect(route('export-bookkeeping.index'), navigate: true);
        }
    }

    private function getGroupDailyPurchasesData()
    {
        // Start building the query
        $query = Payment::orderBy('created_at', 'desc');

        // Check if start and end dates are set and add them to the query
        if ($this->startDate && $this->endDate) {
            if ($this->viewMode == 'monthly') {
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay()->format('Y-m-d H:i:s');  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');  // Adjusts time to 23:59:59
            } else {
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay()->format('Y-m-d H:i:s');  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay()->format('Y-m-d H:i:s');  // Adjusts time to 23:59:59
            }

            $query->whereBetween('created_at', [$start, $end]);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // #######################################
        // Retrieve the payments
        $dailyPurchases = $query->get()->map(function ($dailyPurchase, $index) {
            // Attempt to get customer name from purchaseOrder
            $customerName = optional(optional($dailyPurchase->purchase)->customer)->name;
            // $customerFrekuensi = optional(optional($dailyPurchase->purchase_order)->customer)->purchase_orders->get();
            // $customerFrekuensi = optional(optional(optional($dailyPurchase->purchase_order)->customer)->purchase_orders)->count();

            return [
                'number' => $index + 1, // Start dari 1
                'customer_name' => $customerName ?? 'Unknown', // Fallback to 'Unknown' if still null
                'amount' => optional($dailyPurchase)->amount ?? 'Unknown', // Fallback to 'Unknown' if still null
                'bank_detail' => optional($dailyPurchase)->bank_detail ?? 'Unknown', // Fallback to 'Unknown' if still null
                'purchase_date' => $dailyPurchase->created_at->format('Y-m-d') ?? 'Unknown', // Fallback to 'Unknown' if still null
                'purchase_time' => $dailyPurchase->created_at->format('Y-m-d H:i') ?? 'Unknown', // Fallback to 'Unknown' if still null
            ];
        });

        // Group the data by purchase_date
        $groupeddailyPurchases = $dailyPurchases->groupBy('purchase_date')->map(function ($datedailyPurchases) {
            return $datedailyPurchases->map(function ($dailyPurchase) {
                return [
                    'number' => $dailyPurchase['number'],
                    'customer_name' => $dailyPurchase['customer_name'],
                    'amount' => $dailyPurchase['amount'],
                    'bank_detail' => $dailyPurchase['bank_detail'],
                    'purchase_date' => $dailyPurchase['purchase_date'],
                    'purchase_time' => $dailyPurchase['purchase_time'],
                ];
            });
        });

        // Convert the grouped data to a flat array for pagination
        $flatGroupeddailyPurchases = [];
        foreach ($groupeddailyPurchases as $date => $dailyPurchases) {
            $flatGroupeddailyPurchases[] = [
                'purchase_date' => $date,
                'dailyPurchases' => $dailyPurchases,
                'customer_count' => $dailyPurchases->count(), // Count of customers for the day
            ];
        }

        // Paginate the flat grouped dailyPurchases
        $perPage = 2; // Number of days per page
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($flatGroupeddailyPurchases, ($currentPage - 1) * $perPage, $perPage);
        $paginatedGroupeddailyPurchases = new LengthAwarePaginator($currentItems, count($flatGroupeddailyPurchases), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return $paginatedGroupeddailyPurchases; // Return the paginated instance directly
        // #######################################
    }

    private function getGroupMonthlyPurchasesData()
    {
        $query = Payment::orderBy('created_at', 'desc');

        // Check if start and end dates are set and add them to the query
        if ($this->startDate && $this->endDate) {
            if($this->viewMode == 'monthly'){
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay()->format('Y-m-d H:i:s');  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');  // Adjusts time to 23:59:59
            } else {
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay()->format('Y-m-d H:i:s');  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay()->format('Y-m-d H:i:s');  // Adjusts time to 23:59:59
            }


            $query->whereBetween('created_at', [$start, $end]);
        }

        // Retrieve the payments, apply transformations
        $MonthlyPurchases = $query->get()->map(function ($MonthlyPurchase, $index) {
            $customerName = optional(optional($MonthlyPurchase->purchase)->customer)->name;
            return [
                'number' => $index + 1, // Start dari 1,
                'customer_name' => $customerName ?? 'Unknown', // Safe access using optional
                'amount' => optional($MonthlyPurchase)->amount, // Safe access to amount
                'bank_detail' => optional($MonthlyPurchase)->bank_detail, // Safe access to bank detail
                'purchase_month' => $MonthlyPurchase->created_at->format('Y-m'), // Format the date as Year-Month
                'purchase_time' => $MonthlyPurchase->created_at->format('Y-m-d H:i'), // Format the date as Year-Month
            ];
        });

        // Group the data by purchase_month
        $groupedMonthlyPurchases = $MonthlyPurchases->groupBy('purchase_month')->map(function ($dateMonthlyPurchases) {
            return $dateMonthlyPurchases->map(function ($MonthlyPurchase) {
                return [
                    'customer_name' => $MonthlyPurchase['customer_name'],
                    'amount' => $MonthlyPurchase['amount'],
                    'bank_detail' => $MonthlyPurchase['bank_detail'],
                    'purchase_month' => $MonthlyPurchase['purchase_month'],
                    'purchase_time' => $MonthlyPurchase['purchase_time'],
                ];
            });
        });

        // Convert the grouped data to a flat array for pagination
        $flatGroupedMonthlyPurchases = [];
        foreach ($groupedMonthlyPurchases as $date => $MonthlyPurchases) {
            $flatGroupedMonthlyPurchases[] = [
                'purchase_month' => $date,
                'monthlyPurchases' => $MonthlyPurchases,
                'customer_count' => $MonthlyPurchases->count(), // Count of customers for the day
            ];
        }

        // Paginate the flat grouped MonthlyPurchases
        $perPage = 1; // Number of Months per page
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($flatGroupedMonthlyPurchases, ($currentPage - 1) * $perPage, $perPage);
        $paginatedGroupedMonthlyPurchases = new LengthAwarePaginator($currentItems, count($flatGroupedMonthlyPurchases), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return $paginatedGroupedMonthlyPurchases; // Return the paginated instance directly
    }

    private function getPurchasesData()
    {
        $query = Payment::orderBy('created_at', 'desc');

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

    private function getAdditionalPrices()
    {
        $query = PurchaseOrder::orderBy('created_at', 'desc');

        // Check if start and end dates are set and add them to the query
        if ($this->startDate && $this->endDate) {
            if ($this->viewMode == 'monthly') {
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay();  // Adjusts time to 23:59:59
            } else {
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
            }
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Sum the additional_price column
        $totalAdditionalPrices = $query->sum('additional_price');

        return $totalAdditionalPrices;
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
        $dailyGroupPurchases = $this->getGroupDailyPurchasesData();
        $monthlyGroupPurchases = $this->getGroupMonthlyPurchasesData();

        return view('livewire.export-data.export-bookkeeping-view', [
            'dailyGroupPurchases' => $dailyGroupPurchases,
            'monthlyGroupPurchases' => $monthlyGroupPurchases,
            'viewMode' => $this->viewMode,
        ]);
    }
}
