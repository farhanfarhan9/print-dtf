<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Products;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DebtCustomerExport;

class DebtCustomer extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'remaining_debt';
    public $sortDirection = 'desc';
    public $perPage = 10;
    protected $paginationTheme = 'tailwind';

    // Disable automatic Livewire rendering on property updates
    protected $disableRenderOnPropertyUpdate = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'remaining_debt'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
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

    public function exportExcel()
    {
        // Get all debt customer data without pagination
        $debtCustomers = $this->getDebtCustomersData(false);

        // Generate filename with date
        $filename = 'debt_customers_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        // Return the download response
        return Excel::download(new DebtCustomerExport($debtCustomers), $filename);
    }

    public function getDebtCustomersData($paginate = true)
    {
        // Calculate debt per customer based on open purchases and their payments
        $query = DB::table('customers')
            ->join('purchases', 'customers.id', '=', 'purchases.customer_id')
            ->leftJoin(DB::raw('(SELECT
                                    purchase_id,
                                    SUM(amount) as total_paid,
                                    MAX(created_at) as last_payment_date
                                FROM payments
                                GROUP BY purchase_id) as payment_summary'),
                      'purchases.id', '=', 'payment_summary.purchase_id')
            ->where('purchases.payment_status', 'open')
            ->select(
                'customers.id as customer_id',
                'customers.name as customer_name',
                DB::raw('SUM(purchases.total_payment) as total_debt'),
                DB::raw('COALESCE(SUM(payment_summary.total_paid), 0) as total_paid'),
                DB::raw('MAX(payment_summary.last_payment_date) as last_payment_date'),
                DB::raw('MIN(purchases.created_at) as first_purchase_date'),
                DB::raw('COUNT(purchases.id) as open_purchases_count'),
                DB::raw('(SUM(purchases.total_payment) - COALESCE(SUM(payment_summary.total_paid), 0)) as remaining_debt')
            )
            ->groupBy('customers.id', 'customers.name')
            ->having(DB::raw('(SUM(purchases.total_payment) - COALESCE(SUM(payment_summary.total_paid), 0))'), '>', 0);

        // Apply search filter if search term is provided
        if (!empty($this->search)) {
            $query->where('customers.name', 'like', '%' . $this->search . '%');
        }

        // Apply sorting
        switch ($this->sortField) {
            case 'customer_name':
                $query->orderBy('customers.name', $this->sortDirection);
                break;
            case 'total_debt':
                $query->orderBy('total_debt', $this->sortDirection);
                break;
            case 'total_paid':
                $query->orderBy('total_paid', $this->sortDirection);
                break;
            case 'remaining_debt':
                $query->orderBy('remaining_debt', $this->sortDirection);
                break;
            case 'last_payment_date':
                $query->orderBy('last_payment_date', $this->sortDirection);
                break;
            case 'first_purchase_date':
                $query->orderBy('first_purchase_date', $this->sortDirection);
                break;
            case 'open_purchases_count':
                $query->orderBy('open_purchases_count', $this->sortDirection);
                break;
            default:
                $query->orderBy('remaining_debt', $this->sortDirection);
                break;
        }

        if ($paginate) {
            return $query->paginate($this->perPage);
        } else {
            return $query->get();
        }
    }

    public function render()
    {
        $debtCustomers = $this->getDebtCustomersData();

        return view('livewire.debt-customer', [
            'debtCustomers' => $debtCustomers
        ]);
    }
}
