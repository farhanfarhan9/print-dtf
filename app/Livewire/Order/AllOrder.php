<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\Payment;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AllOrder extends Component
{
    use WithPagination;
    public $search;
    public $selectedHistory;
    public $paymentHistories = [];
    public $paymentHistoryModal;



    public function showPaymentHistory(Purchase $purchase)
    {
        $this->selectedHistory = $purchase;
        $this->paymentHistories = Payment::where('purchase_id', $purchase->id)->get();
        $this->paymentHistoryModal = 1;
    }

    public function render()
    {
        $query = Purchase::query();

        // If search query is provided, filter by user name
        if ($this->search) {
            $query->whereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }

        // Order by created_at in descending order and paginate
        $purchases = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.order.all-order', compact('purchases'));
    }
}
