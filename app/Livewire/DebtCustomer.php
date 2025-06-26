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

    public function render()
    {
        // Get purchase orders with "Belum Bayar" status
        $debtCustomers = PurchaseOrder::where('status', 'Belum Bayar')
            ->join('purchases', 'purchase_orders.purchase_id', '=', 'purchases.id')
            ->join('customers', 'purchases.customer_id', '=', 'customers.id')
            ->leftJoin('products', 'purchase_orders.product_id', '=', 'products.id')
            ->leftJoin('payments', 'purchases.id', '=', 'payments.purchase_id')
            ->select(
                'purchase_orders.id',
                'customers.name as customer_name',
                'products.nama_produk',
                'purchase_orders.total_price as debt_amount',
                'purchases.total_payment as paid_amount',
                DB::raw('MAX(payments.created_at) as last_payment_date'),
                'purchase_orders.created_at as purchase_date'
            )
            ->when($this->search, function ($query) {
                return $query->where('customers.name', 'like', '%' . $this->search . '%')
                    ->orWhere('products.nama_produk', 'like', '%' . $this->search . '%');
            })
            ->groupBy(
                'purchase_orders.id',
                'customers.name',
                'products.nama_produk',
                'purchase_orders.total_price',
                'purchases.total_payment',
                'purchase_orders.created_at'
            )
            ->orderBy('purchase_orders.created_at', 'desc')
            ->paginate(10);

        return view('livewire.debt-customer', [
            'debtCustomers' => $debtCustomers
        ]);
    }
}
