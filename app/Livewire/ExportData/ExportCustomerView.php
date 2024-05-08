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
use Carbon\Carbon;

class ExportCustomerView extends Component
{
    use Actions;
    use WithPagination;

    public $search;
    public $startDate;
    public $endDate;

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
            $customerOrders = $this->getCustomerOrders(); // Ensure this method exists and returns the correct data

            // Convert the original date format to a display format only if dates are set
            $displayStartDate = $this->startDate ? Carbon::createFromFormat('Y-m-d', $this->startDate)->isoFormat('dddd, D MMMM YYYY') : null;
            $displayEndDate = $this->endDate ? Carbon::createFromFormat('Y-m-d', $this->endDate)->isoFormat('dddd, D MMMM YYYY') : null;

            // Return the download response
            return Excel::download(new CustomersExport($customerOrders, $displayStartDate, $displayEndDate), $filename);
        }else if ($this->startDate == null || $this->endDate == null){
            session()->flash('exportFailed');
            $this->redirect(route('export-customer.index'), navigate: true);
        }
    }

    private function getCustomerOrders()
    {
        $query = PurchaseOrder::query()
            ->select('purchase_id', DB::raw('SUM(qty) as total_qty'));

        // Apply date range filtering if both start and end dates are set
        if ($this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();  // Ensures the time is at 00:00:00
            $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();  // Adjusts time to 23:59:59
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
        }

        $purchasesWithTotalQty = $query->groupBy('purchase_id')->pluck('total_qty', 'purchase_id');

        // Get the customer IDs from the purchases
        $purchaseCustomerMapping = Purchase::query()
            ->whereIn('id', $purchasesWithTotalQty->keys())
            ->pluck('customer_id', 'id');

        // Menghitung frekuensi pembelian setiap pelanggan dengan mempertimbangkan tanggal
        $customerFrequencies = Purchase::query()
            ->select('customer_id', DB::raw('COUNT(*) as frequency'))
            ->whereIn('id', $purchasesWithTotalQty->keys())
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
            })
            ->groupBy('customer_id')
            ->pluck('frequency', 'customer_id');

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
        $customerOrders = array_map(function ($customerId) use ($customers, $customerTotals, $customerFrequencies) {
            $customer = $customers->get($customerId);
            return [
                'jumlah_order' => $customerTotals[$customerId] ?? 0,
                'nama_customer' => $customer ? $customer->name : 'N/A',
                'frekuensi' => $customerFrequencies[$customerId] ?? 0,
                // 'alamat' => $customer ? $customer->address : 'N/A',
                // 'phone' => $customer ? $customer->phone : 'N/A',
                // Assuming the email is also a part of your Customer model
                // 'email' => $customer ? $customer->email : 'N/A',
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
