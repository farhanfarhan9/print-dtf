<?php

namespace App\Livewire\ExportData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use App\Models\Payment;
use App\Exports\BookkeepingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator; // Import LengthAwarePaginator
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExportBookkeepingView extends Component
{
    use WithPagination;
    public $search;
    public $startDate;
    public $endDate;
    public $viewMode = 'daily';  // Default to daily
    public $sortField = 'created_at'; // Default sort field
    public $sortDirection = 'desc'; // Default sort direction
    public $loadingTime = 0;
    public $ramUsage = 0;
    public $dataSize = 0;
    public $type;
    public $isAdmin = false;

    public function switchToDaily()
    {
        return $this->redirect(route('export-bookkeeping.index', ['type' => 'daily']), navigate: true);
    }

    public function switchToMonthly()
    {
        // Don't allow admin to switch to monthly view
        if ($this->isAdmin) {
            return;
        }

        return $this->redirect(route('export-bookkeeping.index', ['type' => 'monthly']), navigate: true);
    }

    public function applyFilter()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->resetPage();
    }

    public function mount($type = null)
    {
        // Check if user is admin
        $this->isAdmin = Auth::user()->roles === 'admin';

        // For admin users, always set to daily view but allow date selection
        if ($this->isAdmin) {
            $this->viewMode = 'daily';

            // Set default date range to today and 2 days back for admin if no dates are selected
            if (!$this->startDate && !$this->endDate) {
                $today = Carbon::today();
                $this->endDate = $today->format('Y-m-d');
                $this->startDate = $today->copy()->subDays(2)->format('Y-m-d');
            }
        } else {
            // Set the viewMode based on the type parameter for non-admin users
            if ($type === 'monthly') {
                $this->viewMode = 'monthly';
            } else {
                $this->viewMode = 'daily'; // Default to daily if type is null or anything else
            }
        }
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
        // Start measuring time and memory usage
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Define a unique cache key based on the current parameters
        $cacheKey = 'daily_purchases_' . $this->startDate . '_' . $this->endDate . '_' . $this->viewMode . '_' . $this->sortField . '_' . $this->sortDirection . '_' . ($this->isAdmin ? 'admin' : 'user');

        // Use Cache::remember to cache the results with longer duration (5 minutes instead of 1)
        $dailyPurchases = Cache::remember($cacheKey, 300, function () {
            // Start building the query with eager loading to reduce N+1 queries
            $query = Payment::with(['purchase.customer'])
                ->select('payments.*')
                ->orderBy($this->sortField, $this->sortDirection);

            // For admin users, enforce max 3-day range
            if ($this->isAdmin && $this->startDate && $this->endDate) {
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

                // Calculate the difference in days
                $diffInDays = $end->diffInDays($start);

                // If the range is more than 3 days, limit it to 3 days from the end date
                if ($diffInDays > 2) {
                    $start = $end->copy()->subDays(2)->startOfDay();
                }

                $query->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')]);
            }
            // For non-admin users, use the selected date range
            else if ($this->startDate && $this->endDate) {
                if ($this->viewMode == 'monthly') {
                    $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay()->format('Y-m-d H:i:s');
                    $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');
                } else {
                    $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay()->format('Y-m-d H:i:s');
                    $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay()->format('Y-m-d H:i:s');
                }

                $query->whereBetween('created_at', [$start, $end]);
            }

            // Use chunk processing for large datasets to reduce memory usage
            $result = collect();
            $index = 0;

            $query->chunk(200, function ($payments) use (&$result, &$index) {
                foreach ($payments as $payment) {
                    $customerName = optional(optional($payment->purchase)->customer)->name;

                    $result->push([
                        'number' => ++$index,
                        'customer_name' => $customerName ?? 'Unknown',
                        'amount' => $payment->amount ?? 'Unknown',
                        'bank_detail' => $payment->bank_detail ?? 'Unknown',
                        'purchase_date' => $payment->created_at->format('Y-m-d'),
                        'purchase_time' => $payment->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            return $result;
        });

        // Group the data by purchase_date - use efficient grouping
        $groupedByDate = $dailyPurchases->groupBy('purchase_date');

        // Convert the grouped data to a flat array for pagination
        $flatGroupeddailyPurchases = $groupedByDate->map(function ($items, $date) {
            return [
                'purchase_date' => $date,
                'dailyPurchases' => $items,
                'customer_count' => $items->count(),
            ];
        })->values()->all();

        // Paginate the flat grouped dailyPurchases
        $perPage = 2; // Number of days per page
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($flatGroupeddailyPurchases, ($currentPage - 1) * $perPage, $perPage);
        $paginatedGroupeddailyPurchases = new LengthAwarePaginator(
            $currentItems,
            count($flatGroupeddailyPurchases),
            $perPage,
            $currentPage,
            ['path' => route('export-bookkeeping.index', ['type' => $this->viewMode])]
        );

        // End measuring time and memory usage
        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        // Calculate loading time and memory usage
        $this->loadingTime = round($endTime - $startTime, 2);
        $this->ramUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2);
        $this->dataSize = round(strlen(serialize($dailyPurchases)) / 1024, 2);

        return $paginatedGroupeddailyPurchases;
    }

    private function getGroupMonthlyPurchasesData()
    {
        // Start measuring time and memory usage
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Define a unique cache key for monthly data
        $cacheKey = 'monthly_purchases_' . $this->startDate . '_' . $this->endDate . '_' . $this->viewMode . '_' . $this->sortField . '_' . $this->sortDirection;

        // Use Cache::remember with 5 minute duration
        $monthlyPurchases = Cache::remember($cacheKey, 300, function () {
            // Start building the query with eager loading
            $query = Payment::with(['purchase.customer'])
                ->select('payments.*')
                ->orderBy($this->sortField, $this->sortDirection);

            // Check if start and end dates are set and add them to the query
            if ($this->startDate && $this->endDate) {
                if ($this->viewMode == 'monthly') {
                    $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay()->format('Y-m-d H:i:s');
                    $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');
                } else {
                    $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay()->format('Y-m-d H:i:s');
                    $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay()->format('Y-m-d H:i:s');
                }

                $query->whereBetween('created_at', [$start, $end]);
            }

            // Use chunk processing for large datasets
            $result = collect();
            $index = 0;

            $query->chunk(200, function ($payments) use (&$result, &$index) {
                foreach ($payments as $payment) {
                    $customerName = optional(optional($payment->purchase)->customer)->name;

                    $result->push([
                        'number' => ++$index,
                        'customer_name' => $customerName ?? 'Unknown',
                        'amount' => $payment->amount ?? 0,
                        'bank_detail' => $payment->bank_detail ?? 'Unknown',
                        'purchase_month' => $payment->created_at->format('Y-m'),
                        'purchase_time' => $payment->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            return $result;
        });

        // Group the data by purchase_month more efficiently
        $groupedByMonth = $monthlyPurchases->groupBy('purchase_month');

        // Convert the grouped data to a flat array for pagination
        $flatGroupedMonthlyPurchases = $groupedByMonth->map(function ($items, $month) {
            return [
                'purchase_month' => $month,
                'monthlyPurchases' => $items,
                'customer_count' => $items->count(),
            ];
        })->values()->all();

        // Paginate the flat grouped MonthlyPurchases
        $perPage = 1; // Number of Months per page
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($flatGroupedMonthlyPurchases, ($currentPage - 1) * $perPage, $perPage);
        $paginatedGroupedMonthlyPurchases = new LengthAwarePaginator(
            $currentItems,
            count($flatGroupedMonthlyPurchases),
            $perPage,
            $currentPage,
            ['path' => route('export-bookkeeping.index', ['type' => $this->viewMode])]
        );

        // End measuring time and memory usage
        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        // Calculate loading time and memory usage
        $this->loadingTime = round($endTime - $startTime, 2);
        $this->ramUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2);
        $this->dataSize = round(strlen(serialize($monthlyPurchases)) / 1024, 2);

        return $paginatedGroupedMonthlyPurchases;
    }

    private function getPurchasesData()
    {
        // Use eager loading to reduce N+1 queries
        $query = Payment::with(['purchase.customer'])
            ->select('payments.*')
            ->orderBy('created_at', 'desc');

        // For admin users, enforce max 3-day range
        if ($this->isAdmin && $this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

            // Calculate the difference in days
            $diffInDays = $end->diffInDays($start);

            // If the range is more than 3 days, limit it to 3 days from the end date
            if ($diffInDays > 2) {
                $start = $end->copy()->subDays(2)->startOfDay();
            }

            $query->whereBetween('created_at', [$start, $end]);
        }
        // For non-admin users, use the selected date range
        else if ($this->startDate && $this->endDate) {
            if ($this->viewMode == 'monthly') {
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay();
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay();
            } else {
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();
            }
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Use chunk processing for large datasets
        $purchases = collect();

        $query->chunk(500, function ($payments) use (&$purchases) {
            foreach ($payments as $payment) {
                $purchases->push([
                    'customer_name' => optional($payment->purchase->customer)->name ?? 'Unknown',
                    'amount' => $payment->amount ?? 0,
                    'bank_detail' => $payment->bank_detail ?? 'Unknown',
                    'purchase_date' => $payment->created_at->format('Y-m-d, H:i:s'),
                ]);
            }
        });

        return $purchases;
    }

    private function getAdditionalPrices()
    {
        // Use direct SQL aggregation for better performance

        // For admin users, enforce max 3-day range
        if ($this->isAdmin && $this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

            // Calculate the difference in days
            $diffInDays = $end->diffInDays($start);

            // If the range is more than 3 days, limit it to 3 days from the end date
            if ($diffInDays > 2) {
                $start = $end->copy()->subDays(2)->startOfDay();
            }

            // Use a direct DB query with sum for better performance
            $totalAdditionalPrices = DB::table('purchase_orders')
                ->whereBetween('created_at', [$start, $end])
                ->sum('additional_price');
        }
        // For non-admin users, use the selected date range
        else if ($this->startDate && $this->endDate) {
            if ($this->viewMode == 'monthly') {
                $start = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay();
                $end = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay();
            } else {
                $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();
            }

            // Use a direct DB query with sum for better performance
            $totalAdditionalPrices = DB::table('purchase_orders')
                ->whereBetween('created_at', [$start, $end])
                ->sum('additional_price');
        } else {
            // If no date range, get all
            $totalAdditionalPrices = DB::table('purchase_orders')
                ->sum('additional_price');
        }

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
        // Only load the dataset for the active viewMode to prevent double table rendering
        $dailyGroupPurchases = $this->viewMode === 'daily' ? $this->getGroupDailyPurchasesData() : collect();
        $monthlyGroupPurchases = $this->viewMode === 'monthly' ? $this->getGroupMonthlyPurchasesData() : collect();

        return view('livewire.export-data.export-bookkeeping-view', [
            'dailyGroupPurchases' => $dailyGroupPurchases,
            'monthlyGroupPurchases' => $monthlyGroupPurchases,
            'viewMode' => $this->viewMode,
            'isAdmin' => $this->isAdmin,
        ]);
    }
}
