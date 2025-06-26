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

class DebtCustomer extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'purchase_date';
    public $sortDirection = 'desc';
    public $perPage = 10;
    protected $paginationTheme = 'tailwind';

    // Disable automatic Livewire rendering on property updates
    protected $disableRenderOnPropertyUpdate = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'purchase_date'],
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

    public function getDebtCustomersData()
    {
        // Use a more efficient query with subqueries for better performance
        $query = DB::table('purchase_orders')
            ->where('purchase_orders.status', 'Belum Bayar')
            ->join('purchases', 'purchase_orders.purchase_id', '=', 'purchases.id')
            ->join('customers', 'purchases.customer_id', '=', 'customers.id')
            ->leftJoin('products', 'purchase_orders.product_id', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT
                                    purchase_id,
                                    SUM(amount) as total_paid,
                                    MAX(created_at) as last_payment_date
                                FROM payments
                                GROUP BY purchase_id) as payment_summary'),
                      'purchases.id', '=', 'payment_summary.purchase_id')
            ->select(
                'purchase_orders.id',
                'customers.name as customer_name',
                'products.nama_produk',
                'purchase_orders.total_price as debt_amount',
                DB::raw('COALESCE(payment_summary.total_paid, 0) as paid_amount'),
                'payment_summary.last_payment_date',
                'purchase_orders.created_at as purchase_date'
            );

        // Apply search filter if search term is provided
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('customers.name', 'like', '%' . $this->search . '%')
                  ->orWhere('products.nama_produk', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        switch ($this->sortField) {
            case 'customer_name':
                $query->orderBy('customers.name', $this->sortDirection);
                break;
            case 'nama_produk':
                $query->orderBy('products.nama_produk', $this->sortDirection);
                break;
            case 'debt_amount':
                $query->orderBy('purchase_orders.total_price', $this->sortDirection);
                break;
            case 'paid_amount':
                $query->orderBy('paid_amount', $this->sortDirection);
                break;
            case 'last_payment_date':
                $query->orderBy('payment_summary.last_payment_date', $this->sortDirection);
                break;
            case 'purchase_date':
            default:
                $query->orderBy('purchase_orders.created_at', $this->sortDirection);
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $debtCustomers = $this->getDebtCustomersData();

        return view('livewire.debt-customer', [
            'debtCustomers' => $debtCustomers
        ]);
    }
}
