<?php

namespace App\Livewire\ExportData;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use App\Models\Customer;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use DB;

class ExportCustomerView extends Component
{
    use Actions;
    use WithPagination;

    public $search;
    public $startDate;
    public $endDate;

    public function exportExcel()
    {
        // Assuming you have methods to determine the start and end date
        $startDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->isoFormat('dddd, D MMMM YYYY') : null;
        $endDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->isoFormat('dddd, D MMMM YYYY') : null;
        // Fetch the customer orders data as per your existing logic
        $customerOrders = $this->getCustomerOrders(); // Make sure this method exists and returns the correct data

        // Return the download response
        return Excel::download(new CustomersExport($customerOrders, $startDate, $endDate), 'customers.xlsx');
    }

    private function getCustomerOrders()
    {
        $query = PurchaseOrder::query()
            ->select('purchase_id', DB::raw('SUM(qty) as total_qty'))
            ->where('status', 'Lunas');

        // Apply date range filtering if both start and end dates are set
        if ($this->startDate && $this->endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
        }

        $purchasesWithTotalQty = $query->groupBy('purchase_id')->pluck('total_qty', 'purchase_id');

        // Get the customer IDs from the purchases
        $purchaseCustomerMapping = Purchase::query()
            ->whereIn('id', $purchasesWithTotalQty->keys())
            ->pluck('customer_id', 'id');

        // Calculate the total QTY for each customer
        $customerTotals = [];
        foreach ($purchasesWithTotalQty as $purchaseId => $totalQty) {
            $customerId = $purchaseCustomerMapping[$purchaseId] ?? null;
            if ($customerId) {
                if (!isset($customerTotals[$customerId])) {
                    $customerTotals[$customerId] = 0;
                }
                $customerTotals[$customerId] += $totalQty;
            }
        }

        // Sort customer totals in descending order of total quantity
        arsort($customerTotals);

        // Get customer details and compile the final data for the export
        $customers = Customer::query()
            ->whereIn('id', array_keys($customerTotals))
            ->get()
            ->keyBy('id');

        // Prepare the array for export
        $customerOrders = array_map(function ($customerId) use ($customers, $customerTotals) {
            $customer = $customers->get($customerId);
            return [
                'jumlah_order' => $customerTotals[$customerId],
                'nama_customer' => $customer ? $customer->name : 'N/A',
                'alamat' => $customer ? $customer->address : 'N/A',
                'phone' => $customer ? $customer->phone : 'N/A',
                // Assuming the email is also a part of your Customer model
                'email' => $customer ? $customer->email : 'N/A',
            ];
        }, array_keys($customerTotals));

        return $customerOrders;
    }

    public function render()
    {
        // Get the sorted customer orders for display in the view
        $customerOrders = $this->getCustomerOrders();

        return view('livewire.export-data.export-customer-view', [
            'customerOrders' => $customerOrders
        ]);
    }
}
