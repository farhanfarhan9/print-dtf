<?php

namespace App\Livewire\Order\Po;

use App\Livewire\Product;
use App\Models\Payment;
use App\Models\Purchase;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use Livewire\WithFileUploads;

class AllPo extends Component
{
    use Actions;
    use WithPagination;
    use WithFileUploads;

    public $order;
    // public $purchase;
    public $paymentHistoryModal;
    public $paymentModal;

    public $paymentHistories = [];

    public $selectedPoHistory;
    public $selectedPo;

    // Payment
    public $amount;
    public $file;
    public $maxAmount;
    public $bank_detail;

    public function rules()
    {
        return [
            'amount' => 'required|numeric|max:' . $this->maxAmount,
            'file' => 'nullable|file|max:2000',
            'bank_detail' => 'required',
        ];
    }

    public function mount()
    {
        // $this->purchase_orders = PurchaseOrder::where('purchase_id', $this->order)->orderBy('created_at', 'desc')->paginate(15);
    }
    public function showPaymentHistory(PurchaseOrder $po)
    {

        $this->selectedPoHistory = $po;
        $this->paymentHistories = Payment::where('purchase_order_id', $po->id)->get();
        $this->paymentHistoryModal = 1;
    }

    public function updatePaymentModal(PurchaseOrder $po)
    {
        $this->selectedPo = $po;
        $this->paymentModal = 1;
        $this->maxAmount = $po->total_price;
    }

    public function updatePayment(PurchaseOrder $po)
    {
        // dd($po->payments->sum(;'amount'));
        // dd($po->purchase->purchase_orders->where('status', '!=', 'cancel')->sum('total_price'));
        // dd($po->payments->sum('amount'));
        $this->validate();

        if ($this->file) {
            $this->file = $this->file->store('bukti_pembayaran', 'public');
        }

        Payment::create([
            'purchase_order_id' => $po->id,
            'amount' => $this->amount,
            'is_dp' => 0,
            'file' => $this->file,
            'bank_detail' => $this->bank_detail,
        ]);

        if ($po->payments->sum('amount') >= $po->total_price) {
            $po->update([
                'po_status' => 'close'
            ]);
        }

        if($po->purchase->purchase_orders->where('status', '!=', 'cancel')->sum('total_price') >= $po->payments->sum('amount'))
        {
            $po->purchase->update([
                'payment_status' => 'close'
            ]);
        }

        $this->reset('paymentModal', 'amount', 'file', 'bank_detail');
        $this->notification([
            'title'       => 'Sukses',
            'description' => "'Berhasil menambahkan pembayaran pada INV' .$po->invoice_code",
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }

    public function deleteDialog(PurchaseOrder $po)
    {
        $this->dialog()->confirm([
            'title'       => 'Membatalkan Order',
            'description' => 'Yakin Ingin Membatalkan Order?',
            'acceptLabel' => 'Ya',
            'method'      => 'cancelPo',
            'params'      => $po,
            'timeout'     => 3000
        ]);
    }

    public function cancelPo(PurchaseOrder $po)
    {
        // dd($po->internal_process);
        // dd($po->product);
        $po->update([
            'status' => 'cancel',
            'po_status' => 'cancel'
        ]);

        if ($po->purchase->purchase_orders->count() == 1) {
            $po->purchase->update([
                'payment_status' => 'close'
            ]);
        }else{
            $po->purchase->update([
                'total_payment' => $po->purchase->total_payment - $po->total_price
            ]);
        }

        $po->internal_process->delete();
        $po->product->update([
            'stok' => $po->product->stok + $po->qty,
        ]);

        if ($po->purchase->purchase_orders->count() == 1) {
            session()->flash('orderCanceled', ['Sukses', "'Berhasil membatalkan pesanan pada INV' .$po->invoice_code", 'success']);
            return redirect('/orders');
        }else{
            $this->notification([
                'title'       => 'Sukses',
                'description' => "'Berhasil membatalkan pesanan pada INV' .$po->invoice_code",
                'icon'        => 'success',
                'timeout'     => 3000
            ]);
        }
    }


    public function printViewLabel($orderId)
    {
        // You might need to use the fully qualified route name depending on your Laravel version.
        return redirect()->route('view.shipping.label', ['orderId' => $orderId]);
    }

    public function printViewInvoice($orderId)
    {
        // You might need to use the fully qualified route name depending on your Laravel version.
        return redirect()->route('view.invoice.label', ['orderId' => $orderId]);
    }

    public function printLabel($orderId)
    {
        // You might need to use the fully qualified route name depending on your Laravel version.
        return redirect()->route('print.shipping.label', ['orderId' => $orderId]);
    }

    public function printInvoice($orderId)
    {
        // You might need to use the fully qualified route name depending on your Laravel version.
        return redirect()->route('print.invoice.label', ['orderId' => $orderId]);
    }

    public function render()
    {
        return view('livewire.order.po.all-po', [
            'purchase_orders' => PurchaseOrder::where('purchase_id', $this->order)->where('status', '!=', 'cancel')->orderBy('created_at', 'desc')->paginate(10),
            'purchase' => Purchase::find($this->order),
        ]);
    }
}
