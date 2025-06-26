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

class DebtCustomer extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Use a more efficient query with subqueries for better performance
        $debtCustomers = DB::table('purchase_orders')
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
            )
            ->when($this->search, function ($query) {
                return $query->where(function($q) {
                    $q->where('customers.name', 'like', '%' . $this->search . '%')
                      ->orWhere('products.nama_produk', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('purchase_orders.created_at', 'desc')
            ->paginate(10);

        return view('livewire.debt-customer', [
            'debtCustomers' => $debtCustomers
        ]);
    }
}
