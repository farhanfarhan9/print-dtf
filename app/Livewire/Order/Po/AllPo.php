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
    public $paymentHistoryModal;
    public $paymentModal;

    public $paymentHistories = [];

    public $selectedPoHistory;
    public $selectedPo;

    // Payment
    public $amount;
    public $file;
    public $maxAmount;

    public function rules()
    {
        return [
            'amount' => 'required|numeric|max:' . $this->maxAmount,
            'file' => 'nullable|file|max:2000'
        ];
    }

    // public function mount()
    // {
    //     $this->purchase_orders = PurchaseOrder::where('purchase_id', $this->order)->orderBy('created_at', 'desc')->paginate(15);
    // }
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


        // if ($po->po_status == 'close' && $po->purchase->purchase_orders->count() == 1) {
        //     $po->purchase->update([
        //         'payment_status' => 'close'
        //     ]);
        // }

        $this->reset('paymentModal', 'amount', 'file');
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
        dd('tes');
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
        }

        $po->internal_process->delete();
        $po->product->update([
            'stok' => $po->product->stok + $po->qty,
        ]);
        $this->notification([
            'title'       => 'Sukses',
            'description' => "'Berhasil membatalkan pesanan pada INV' .$po->invoice_code",
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
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
        ]);
    }
}
