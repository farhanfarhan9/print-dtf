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
use Illuminate\Support\Facades\DB;
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
    protected $paginationTheme = 'tailwind';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'sortField' => ['except' => 'newest_date'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    // Disable automatic Livewire rendering on property updates
    protected $disableRenderOnPropertyUpdate = true;

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function switchToDaily()
    {
        $this->viewMode = 'daily';
        $this->startDate = null;
        $this->endDate = null;
        $this->resetPage();
    }

    public function switchToMonthly()
    {
        $this->viewMode = 'monthly';
        $this->startDate = null;
        $this->endDate = null;
        $this->resetPage();
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
            $customerOrders = $this->getCustomerOrderData(false); // Get all data without pagination

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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getCustomerOrderData($paginate = true)
    {
        // First, get the base query for customer data - optimized version
        $query = DB::table('purchase_orders')
            ->join('purchases', 'purchase_orders.purchase_id', '=', 'purchases.id')
            ->join('customers', 'purchases.customer_id', '=', 'customers.id')
            ->select(
                'customers.id as customer_id',
                'customers.name as nama_customer',
                DB::raw('SUM(purchase_orders.qty) as jumlah_order'),
                DB::raw('COUNT(DISTINCT purchase_orders.id) as frekuensi'),
                DB::raw('MAX(purchase_orders.created_at) as newest_date')
            )
            ->groupBy('customers.id', 'customers.name');

        // Apply date filtering if both start and end dates are set
        if ($this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();
            $query->whereBetween('purchase_orders.created_at', [$start, $end]);
        }

        // Apply search filter if search term is provided
        if (!empty($this->search)) {
            $query->where('customers.name', 'like', '%' . $this->search . '%');
        }

        // Apply sorting - optimized to use index fields when possible
        switch ($this->sortField) {
            case 'jumlah_order':
                $query->orderBy('jumlah_order', $this->sortDirection);
                break;
            case 'nama_customer':
                $query->orderBy('customers.name', $this->sortDirection); // Use the actual column name
                break;
            case 'frekuensi':
                $query->orderBy('frekuensi', $this->sortDirection);
                break;
            default:
                $query->orderBy('newest_date', 'desc'); // Default sort
        }

        // Return paginated or all results
        if ($paginate) {
            // Only select the fields we actually need for display
            return $query->paginate($this->perPage);
        } else {
            return $query->get();
        }
    }

    public function render()
    {
        // Get the paginated customer orders for display in the view
        $customerOrders = $this->getCustomerOrderData();

        return view('livewire.export-data.export-customer-view', [
            'customerOrders' => $customerOrders,
        ]);
    }
}
