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
            ->select(
                'purchase_orders.id',
                'customers.name as customer_name',
                'products.nama_produk',
                'purchase_orders.total_price as debt_amount',
                'purchase_orders.purchase_id',
                'purchase_orders.created_at as purchase_date'
            )
            ->when($this->search, function ($query) {
                return $query->where('customers.name', 'like', '%' . $this->search . '%')
                    ->orWhere('products.nama_produk', 'like', '%' . $this->search . '%');
            })
            ->orderBy('purchase_orders.created_at', 'desc')
            ->get();

        // Process the results to include payment information
        $processedDebtCustomers = $debtCustomers->map(function ($debtCustomer) {
            // Get all payments for this purchase
            $payments = Payment::where('purchase_id', $debtCustomer->purchase_id)->get();

            // Calculate total paid amount
            $paidAmount = $payments->sum('amount');

            // Get the latest payment date
            $lastPaymentDate = $payments->max('created_at');

            // Add these values to the debt customer object
            $debtCustomer->paid_amount = $paidAmount;
            $debtCustomer->last_payment_date = $lastPaymentDate;

            return $debtCustomer;
        });

        // Paginate the processed results manually
        $page = request()->get('page', 1);
        $perPage = 10;
        $collection = collect($processedDebtCustomers);
        $paginatedItems = $collection->forPage($page, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.debt-customer', [
            'debtCustomers' => $paginator
        ]);
    }
}
