<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Label Design</title>
<style>
    body, html {
        margin: 0;
        padding: 0;
        width: 100%;
    }
    body {
        font-family: 'Courier', monospace; /* Ensure the font name matches */
        font-size: 10px;
        color: black;
        font-weight: 500;
    }
    .label {
        width: 80mm;
        padding: 0;
        box-sizing: border-box;
    }
    .tg {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        margin: 0;
        padding: 0;
    }
    .tg td, .tg th {
        overflow: hidden;
        word-break: break-word;
        padding: 1mm;
        text-align: left;
    }
    .tg th {
        font-weight: bold;
    }
    .center-text {
        text-align: center !important;
    }
    .right-text {
        text-align: right;
        padding-right: 2mm;
    }
</style>
</head>
<body>
<div class="label">
    <table class="tg">
        <thead>
            <tr>
                <th class="tg-0lax center-text" colspan="4">MANSYUR DTF</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tg-baqh center-text" colspan="4">DTF Premium #rasakangsablon</td>
            </tr>
            <tr>
                <td class="tg-baqh center-text" colspan="4">Jalan Setia Budi, Komplek Ruko<br>Milala Mas Blok B no 24</td>
            </tr>
            <tr>
                <td class="tg-baqh center-text" colspan="4">Invoice Code : {{ $order->invoice_code }}</td>
            </tr>
            <tr>
                <td class="tg-0lax center-text" colspan="4">Date : {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="tg-0lax center-text" colspan="4">To : {{ $order->customer->name }}</td>
            </tr>
            <tr>
                <td class="tg-0lax center-text" colspan="4"></td>
            </tr>
            @php $tempTotal = 0; @endphp
            @foreach ($order->purchase_orders as $pembayaran)
                <tr>
                    <td class="tg-0lax" colspan="3">Date : {{ Carbon\Carbon::parse($pembayaran->updated_at)->format('d-m-Y') }} (Invoice Code {{ $pembayaran->invoice_code }})</td>
                    <td class="tg-0lax"></td>
                </tr>
                <tr>
                    <td class="tg-0lax">Produk</td>
                    <td class="tg-0lax">Qty</td>
                    <td class="tg-0lax">Harga</td>
                    <td class="tg-0lax center-text">Subtotal</td>
                </tr>
                <tr>
                    <td class="tg-0lax">{{ $pembayaran->product->nama_produk }}</td>
                    <td class="tg-0lax">{{ number_format($pembayaran->qty, 0, ',', '.') }}</td>
                    <td class="tg-0lax">{{ rupiah_format($pembayaran->product_price) }}</td>
                    <td class="tg-0lax right-text">{{ rupiah_format($pembayaran->product_price) }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax">Jasa Kirim</td>
                    <td class="tg-0lax"></td>
                    <td class="tg-0lax">{{ rupiah_format($pembayaran->expedition_price) }}</td>
                    <td class="tg-0lax right-text">{{ rupiah_format($pembayaran->expedition_price) }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax">Biaya Tambahan</td>
                    <td class="tg-0lax"></td>
                    <td class="tg-0lax">{{ rupiah_format($pembayaran->additional_price) }}</td>
                    <td class="tg-0lax right-text">{{ rupiah_format($pembayaran->additional_price) }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax">Diskon</td>
                    <td class="tg-0lax"></td>
                    <td class="tg-0lax">{{ rupiah_format($pembayaran->discount) }}</td>
                    <td class="tg-0lax right-text">{{ rupiah_format($pembayaran->discount) }}</td>
                </tr>
                @php $tempTotal += $pembayaran->product_price + $pembayaran->expedition_price + $pembayaran->additional_price - $pembayaran->discount; @endphp
            @endforeach
            <tr>
                <td class="tg-0lax" colspan="4">________________________________________________________ +</td>
            </tr>
            <tr>
                <td class="tg-0lax">Grand Total</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax right-text">{{ rupiah_format($tempTotal) }}</td>
            </tr>
            @php $depo_cut = 0; @endphp
            @foreach ($order->purchase_orders as $pembayaran)
                <tr>
                    <td class="tg-0lax">Deposit {{ $loop->index + 1 }}</td>
                    <td class="tg-0lax"></td>
                    <td class="tg-0lax"></td>
                    <td class="tg-0lax right-text">{{ rupiah_format($pembayaran->deposit_cut) }}</td>
                </tr>
                @php $depo_cut += $pembayaran->deposit_cut; @endphp
            @endforeach
            <tr>
                <td class="tg-0lax" colspan="4">________________________________________________________ -</td>
            </tr>
            <tr>
                <td class="tg-0lax">Yang Harus Dibayar</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax right-text">{{ rupiah_format($tempTotal - $depo_cut) }}</td>
            </tr>
            @php $cicilan = $tempTotal - $depo_cut; @endphp
            @foreach($order->payments as $key => $pembayaran)
                <tr>
                    <td class="tg-0lax">Pembayaran {{ $loop->index + 1 }}</td>
                    <td class="tg-0lax"></td>
                    <td class="tg-0lax"></td>
                    <td class="tg-0lax right-text">{{ rupiah_format($pembayaran->amount) }}</td>
                </tr>
                @php $cicilan -= $pembayaran->amount; @endphp
            @endforeach
            <tr>
                <td class="tg-0lax" colspan="4">________________________________________________________ -</td>
            </tr>
            <tr>
                <td class="tg-0lax">Sisa Bayar</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax right-text">{{ rupiah_format($cicilan) }}</td>
            </tr>
            <tr>
                <td class="tg-0lax center-text" colspan="4">Kurir : {{ $order->purchase_orders[0]->expedition->nama_ekspedisi }}</td>
            </tr>
            <tr>
                <td class="tg-0lax center-text" colspan="4">Admin : {{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td class="tg-baqh center-text" colspan="4">Terima Kasih<br>Telah Bertransaksi Disini</td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
