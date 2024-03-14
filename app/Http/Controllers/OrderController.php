<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use PDF;

class OrderController extends Controller
{
    public function printShippingLabel($orderId)
    {
        $order = PurchaseOrder::findOrFail($orderId);
        $pdf = PDF::loadView('orders.shipping-label', compact('order'))
                    ->setPaper('a4', 'landscape');
        return $pdf->download('shipping-label-' . $orderId . '.pdf');
    }

    public function printInvoiceLabel($orderId)
    {
        $order = PurchaseOrder::findOrFail($orderId);
        $pdf = PDF::loadView('orders.invoice-label', compact('order'));
        return $pdf->download('invoice-label-' . $orderId . '.pdf');
    }

    public function viewInvoiceLabel($orderId)
    {
        $order = PurchaseOrder::findOrFail($orderId);
        // Return a view directly for the browser display.
        return view('orders.invoice-label', compact('order'));
    }
    public function viewShippingLabel($orderId)
    {
        $order = PurchaseOrder::findOrFail($orderId);
        // Return a view directly for the browser display.
        return view('orders.shipping-label', compact('order'));
    }
}
