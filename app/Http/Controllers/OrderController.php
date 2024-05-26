<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use PDF;

class OrderController extends Controller
{

    public function printShippingLabel($orderId)
    {
        $order = Purchase::findOrFail($orderId);
        $pdf = PDF::loadView('orders.shipping-label', compact('order'))
                    // ->setPaper([0, 0, 566.93, 284], 'portrait');
                    ->setPaper('a4', 'portrait');
        return $pdf->stream('shipping-label-' . $orderId . '.pdf');
    }

    public function printInvoiceLabel($purchaseId)
    {
        // Find the PurchaseOrder by purchase_id instead of primary key id
        $order = Purchase::where('id', $purchaseId)->firstOrFail();
        $pdf = PDF::loadView('orders.invoice-label', compact('order'));
        $pdf->setPaper([0, 0, 490, 800]);
        return $pdf->stream('invoice-label-' . $purchaseId . '.pdf');
    }

    public function viewInvoiceLabel($purchaseId)
    {
        // Similarly, find by purchase_id when displaying in the browser
        $order = Purchase::where('id', $purchaseId)->firstOrFail();
        return view('orders.invoice-label', compact('order'));
    }

    public function viewShippingLabel($orderId)
    {
        $order = PurchaseOrder::findOrFail($orderId);
        // Return a view directly for the browser display.
        return view('orders.shipping-label', compact('order'));
    }
}
