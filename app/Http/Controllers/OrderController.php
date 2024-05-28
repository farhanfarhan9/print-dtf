<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Purchase;
use PDF;
use Mpdf\Mpdf;

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
        // Cari PurchaseOrder dengan purchase_id
        $order = Purchase::where('id', $purchaseId)->firstOrFail();
        $product = Products::first();
        // dd($order->payments);
        // Render Blade view menjadi HTML string
        $html = view('orders.invoice-label', compact('order','product'))->render();

        // Buat instance mPDF
        $mpdf = new \Mpdf\Mpdf([
            'format' => [48, 800], // POS58 paper width
            // 'mode' => 'utf-8',
            'default_font' => 'monospace',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        // Tambahkan HTML ke mPDF
        $mpdf->WriteHTML($html);

        // Tampilkan PDF
        return $mpdf->Output('invoice-label-' . $purchaseId . '.pdf', 'I');
    }

    // public function printInvoiceLabel($purchaseId)
    // {
    //     // Cari PurchaseOrder dengan purchase_id
    //     $order = Purchase::where('id', $purchaseId)->firstOrFail();

    //     // Render Blade view menjadi HTML string
    //     $html = view('orders.invoice-label', compact('order'))->render();

    //     // Render HTML menjadi PDF
    //     $pdf = PDF::loadHTML($html);

    //     // Atur ukuran kertas untuk PDF
    //     $pdf->setPaper([0, 0, 226.77, 800]);

    //     // Atur DPI menjadi 1200
    //     $pdf->setOption('dpi', 110);

    //     // Tampilkan PDF
    //     return $pdf->stream('invoice-label-' . $purchaseId . '.pdf');
    // }

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
