<?php

namespace App\Livewire\ExportData;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Customer;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use DB;
use Carbon\Carbon;

class ExportCustomerView extends Component
{
    use Actions;
    use WithPagination;

    public $search;
    public $startDate;
    public $endDate;
    public $viewMode = 'daily';  // Default to daily
    public $sortField = 'newest_date';  // Default sort field
    public $sortDirection = 'desc';    // Default sort direction

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

            // Format the start and end dates or leave them as an empty string if not set
            $formattedStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->format('d-m-Y') : '';
            $formattedEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->format('d-m-Y') : '';

            // Construct the filename using the formatted dates
            $filename = 'data_customers';
            $filename .= $formattedStartDate ? "_{$formattedStartDate}" : '';
            $filename .= $formattedEndDate ? "_-_$formattedEndDate" : '';
            $filename .= '.xlsx';

            // Fetch the data for export
            $customerOrders = $this->getCustomerOrderData(); // Ensure this method exists and returns the correct data

            // Convert the original date format to a display format only if dates are set
            $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->isoFormat('dddd, D MMMM YYYY') : null;
            $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->isoFormat('dddd, D MMMM YYYY') : null;

            // Return the download response
            return Excel::download(new CustomersExport($customerOrders, $displayStartDate, $displayEndDate), $filename);
        } else if ($this->startDate == null || $this->endDate == null) {
            session()->flash('exportFailed');
            $this->redirect(route('export-customer.index'), navigate: true);
        }
    }

    // Old Function Below

    // private function getCustomerOrders()
    // {
    //     $query = PurchaseOrder::query()
    //         ->select('purchase_id', DB::raw('SUM(qty) as total_qty'));

    //     // Apply date range filtering if both start and end dates are set
    //     if ($this->startDate && $this->endDate) {
    //         $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
    //         $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
    //         $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
    //     }

    //     $purchasesWithTotalQty = $query->groupBy('purchase_id')->pluck('total_qty', 'purchase_id');

    //     // Get the customer IDs from the purchases
    //     $purchaseCustomerMapping = Purchase::query()
    //         ->whereIn('id', $purchasesWithTotalQty->keys())
    //         ->pluck('customer_id', 'id');

    //     // Menghitung frekuensi pembelian setiap pelanggan dengan mempertimbangkan tanggal
    //     $customerFrequencies = Purchase::query()
    //         ->select('customer_id', DB::raw('COUNT(*) as frequency'))
    //         ->whereIn('id', $purchasesWithTotalQty->keys())
    //         ->when($this->startDate && $this->endDate, function ($query) {
    //             $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
    //         })
    //         ->groupBy('customer_id')
    //         ->pluck('frequency', 'customer_id');

    //     // Calculate the total QTY for each customer
    //     $customerTotals = [];
    //     foreach ($purchasesWithTotalQty as $purchaseId => $totalQty) {
    //         $customerId = $purchaseCustomerMapping[$purchaseId] ?? null;
    //         if ($customerId) {
    //             if (!isset($customerTotals[$customerId])) {
    //                 $customerTotals[$customerId] = 0;
    //             }
    //             $customerTotals[$customerId] += $totalQty;
    //         }
    //     }

    //     // Sort customer totals in descending order of total quantity
    //     arsort($customerTotals);

    //     // Get customer details and compile the final data for the export
    //     $customers = Customer::query()
    //         ->whereIn('id', array_keys($customerTotals))
    //         ->get()
    //         ->keyBy('id');

    //     // Prepare the array for export
    //     $customerOrders = array_map(function ($customerId) use ($customers, $customerTotals, $customerFrequencies) {
    //         $customer = $customers->get($customerId);
    //         return [
    //             'jumlah_order' => $customerTotals[$customerId] ?? 0,
    //             'nama_customer' => $customer ? $customer->name : 'N/A',
    //             'frekuensi' => $customerFrequencies[$customerId] ?? 0,
    //             // 'alamat' => $customer ? $customer->address : 'N/A',
    //             // 'phone' => $customer ? $customer->phone : 'N/A',
    //             // Assuming the email is also a part of your Customer model
    //             // 'email' => $customer ? $customer->email : 'N/A',
    //         ];
    //     }, array_keys($customerTotals));

    //     return $customerOrders;
    // }

    // private function getCustomerOrders()
    // {
    //     // Retrieve all PurchaseOrder entries and sum quantities grouped by purchase_id.
    //     $query = PurchaseOrder::query()
    //                     ->select('purchase_id')
    //                     ->selectRaw('SUM(qty) as total_qty')
    //                     ->groupBy('purchase_id');

    //     // Apply date range filtering if both start and end dates are set
    //     if ($this->startDate && $this->endDate) {
    //         $query->whereBetween('created_at', [
    //             Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay(),
    //             Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay()
    //         ]);
    //     }

    //     $purchasesWithTotalQty = $query->get()->pluck('total_qty', 'purchase_id');

    //     // First, get unique purchase_ids from Payments table
    //     $validPurchaseIds = Payment::select('purchase_id')
    //                                 ->distinct()
    //                                 ->when($this->startDate && $this->endDate, function ($query) {
    //                                     $query->whereBetween('created_at', [
    //                                         Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay(),
    //                                         Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay()
    //                                     ]);
    //                                 })
    //                                 ->pluck('purchase_id');

    //     // Filter purchases that are actually paid for
    //     $purchaseCustomerMapping = Purchase::whereIn('id', $validPurchaseIds)
    //                                        ->get()
    //                                        ->pluck('customer_id', 'id');

    //     // Counting the frequency of valid purchases per customer
    //     $customerFrequencies = Purchase::whereIn('id', $validPurchaseIds)
    //                                    ->groupBy('customer_id')
    //                                    ->select('customer_id')
    //                                    ->selectRaw('COUNT(*) as frequency')
    //                                    ->get()
    //                                    ->pluck('frequency', 'customer_id');

    //     // Calculate the total QTY for each customer, adjusted for valid purchase IDs
    //     $customerTotals = [];
    //     foreach ($validPurchaseIds as $purchaseId) {
    //         $totalQty = $purchasesWithTotalQty[$purchaseId] ?? 0;
    //         $customerId = $purchaseCustomerMapping[$purchaseId] ?? null;
    //         if ($customerId) {
    //             if (!isset($customerTotals[$customerId])) {
    //                 $customerTotals[$customerId] = 0;
    //             }
    //             $customerTotals[$customerId] += $totalQty;
    //         }
    //     }

    //     // Sort customer totals in descending order of total quantity
    //     arsort($customerTotals);

    //     // Fetch customer details from the database
    //     $customers = Customer::findMany(array_keys($customerTotals))
    //                          ->keyBy('id');

    //     // Prepare the array for export
    //     $customerOrders = array_map(function ($customerId) use ($customers, $customerTotals, $customerFrequencies) {
    //         $customer = $customers->get($customerId);
    //         return [
    //             'jumlah_order' => $customerTotals[$customerId] ?? 0,
    //             'nama_customer' => $customer ? $customer->name : 'N/A',
    //             'frekuensi' => $customerFrequencies[$customerId] ?? 0,
    //         ];
    //     }, array_keys($customerTotals));

    //     return $customerOrders;
    // }

    // Old Function Above

    // New Function Below
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getCustomerOrderData()
    {
        $query = PurchaseOrder::query()
            ->select('purchase_orders.*', 'purchases.customer_id', 'customers.name as customer_name')
            ->join('purchases', 'purchases.id', '=', 'purchase_orders.purchase_id')
            ->join('customers', 'customers.id', '=', 'purchases.customer_id')
            ->with(['purchase.customer']);

        if ($this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();
            $query->whereBetween('purchase_orders.created_at', [$start, $end]);
        }

        if (!empty($this->search)) {
            $query->where('customers.name', 'like', '%' . $this->search . '%');
        }

        $purchaseOrders = $query->get();

        $customerData = $purchaseOrders->groupBy('purchase.customer_id')->map(function ($orders, $customerId) {
            return [
                'jumlah_order' => $orders->sum('qty'),
                'frekuensi' => $orders->count(),
                'nama_customer' => $orders->first()->purchase->customer->name,
                'newest_date' => $orders->max('created_at'),
            ];
        });

        if (in_array($this->sortField, ['jumlah_order', 'frekuensi', 'nama_customer'])) {
            $customerData = $customerData->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc');
        } else {
            $customerData = $customerData->sortByDesc('newest_date');
        }

        return $customerData;
    }



    private function getCustomerOrdersNew()
    {
        $query = Payment::query()
            ->orderBy('created_at', 'asc')
            ->with(['purchase.customer', 'purchase.purchase_orders'])  // Eager load associated purchases, customers, and purchase orders
            ->select('payments.*');  // Ensure we're selecting from payments

        // Date filtering
        if ($this->startDate && $this->endDate) {
            $start = $this->viewMode == 'monthly'
                ? Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth()->startOfDay()
                : Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
            $end = $this->viewMode == 'monthly'
                ? Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth()->endOfDay()
                : Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Retrieve the payments
        $payments = $query->get();

        // Organize data by customer_id
        $customerData = $payments->groupBy(function ($payment) {
            return $payment->purchase->customer_id;  // Group by the customer_id from each payment's purchase
        })->mapWithKeys(function ($payments, $customerId) {
            $customer = $payments->first()->purchase->customer;
            $totalQty = $payments->reduce(function ($carry, $payment) {
                // Sum up all quantities in purchase_orders related to each payment's purchase
                return $carry + $payment->purchase->purchase_orders->sum('qty');
            }, 0);
            return [$customerId => [
                'jumlah_order' => $totalQty,  // Total quantity from purchase_orders
                'nama_customer' => $customer ? $customer->name : 'Unknown',  // Get the customer's name
                'frekuensi' => $payments->pluck('purchase_id')->unique()->count(),  // Unique purchase counts
            ]];
        });

        return $customerData;
    }

    // New Function Above

    public function render()
    {
        // Get the sorted customer orders for display in the view
        $customerOrders = $this->getCustomerOrderData();
        $newCustomerOrders = $this->getCustomerOrdersNew();

        return view('livewire.export-data.export-customer-view', [
            'newCustomerOrders' => $newCustomerOrders,
            'customerOrders' => $customerOrders,
        ]);
    }
}
