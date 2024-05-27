<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Design</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family:  monospace;
            /* font-size: .6em; */
            font-size: 10px;
            /* Increased font size */
            /* line-height: 5; */
            /* Adjusted line height */
        }


        body {
            /* font-size: 100pt; */
            /* font-size: 1000%; */
        }

        .receipt {
            width: 100%;
            padding: 0;
            box-sizing: border-box;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-table td,
        .receipt-table th {
            padding: 0;
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    {{-- <div class="label">
        <table class="tg"> --}}
    <div class="receipt">
        <table class="receipt-table" style="overflow: wrap">
            <thead>
                <tr>
                    <th class="tg-0lax center" colspan="4" style="text-align: center">MANSYUR DTF</th>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tg-baqh center" style="text-align: center" colspan="4">DTF Premium #rasakangsablon
                    </td>
                </tr>
                <tr>
                    <td class="tg-baqh center" style="text-align: center" colspan="4">Jalan Setia Budi, Komplek
                        Ruko<br>Milala Mas Blok B
                        no 24</td>
                </tr>
                <tr>
                    <td class="tg-baqh center" style="text-align: center" colspan="4">Invoice Code :
                        {{ $order->invoice_code }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">Date :
                        {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">To :
                        {{ $order->customer->name }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                @php $tempTotal = 0; @endphp
                @foreach ($order->purchase_orders as $pembayaran)
                    <tr>
                        <td class="tg-0lax" colspan="3">Date :
                            {{ Carbon\Carbon::parse($pembayaran->updated_at)->format('d-m-Y') }} (Invoice Code
                            {{ $pembayaran->invoice_code }})</td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="" style="white-space: nowrap;">Produk</td>
                        <td class="" style="white-space: nowrap;">Qty <span>&nbsp;</span></td>
                        <td class="" style="white-space: nowrap;">Harga</td>
                        <td class=" center-text" style="white-space: nowrap;">Subtotal</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax" style="white-space: nowrap;">{{ $pembayaran->product->nama_produk }}</td>
                        <td class="tg-0lax" style="white-space: nowrap;">{{ number_format($pembayaran->qty, 0, ',', '.') }}</td>
                        <td class="tg-0lax" style="white-space: nowrap;">{{ rupiah_format($pembayaran->product_price / $pembayaran->qty) }}<span>&nbsp;</span></td>
                        <td class="tg-0lax right-text" style="white-space: nowrap;">{{ rupiah_format($pembayaran->product_price) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax" style="white-space: nowrap;">Jasa Kirim</td>
                        <td class="tg-0lax" style="white-space: nowrap;"></td>
                        <td class="tg-0lax" style="white-space: nowrap;">{{ rupiah_format($pembayaran->expedition_price) }}</td>
                        <td class="tg-0lax right-text" style="white-space: nowrap;">{{ rupiah_format($pembayaran->expedition_price) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax" style="white-space: nowrap;">Biaya Tambahan</td>
                        <td class="tg-0lax" style="white-space: nowrap;"></td>
                        <td class="tg-0lax" style="white-space: nowrap;">{{ rupiah_format($pembayaran->additional_price) }}<span>&nbsp;</span></td>
                        <td class="tg-0lax right-text" style="white-space: nowrap;">{{ rupiah_format($pembayaran->additional_price) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax" style="white-space: nowrap;">Diskon</td>
                        <td class="tg-0lax" style="white-space: nowrap;"></td>
                        <td class="tg-0lax" style="white-space: nowrap;">{{ rupiah_format($pembayaran->discount) }}<span>&nbsp;</span></td>
                        <td class="tg-0lax right-text" style="white-space: nowrap;">{{ rupiah_format($pembayaran->discount) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    @php $tempTotal += $pembayaran->product_price + $pembayaran->expedition_price + $pembayaran->additional_price - $pembayaran->discount; @endphp
                @endforeach
                <tr>
                    <td class="tg-0lax" colspan="4">____________________________________ +</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="tg-0lax" colspan="2" style="white-space: nowrap;">Grand Total</td>
                    <td class="tg-0lax right-text" colspan="2" style="white-space: nowrap;">{{ rupiah_format($tempTotal) }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                @php $depo_cut = 0; @endphp
                @foreach ($order->purchase_orders as $pembayaran)
                    <tr>
                        <td class="tg-0lax" colspan="2" style="white-space: nowrap;">Deposit {{ $loop->index + 1 }}</td>
                        <td class="tg-0lax right-text" colspan="2" style="white-space: nowrap;">{{ rupiah_format($pembayaran->deposit_cut) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    @php $depo_cut += $pembayaran->deposit_cut; @endphp
                @endforeach
                <tr>
                    <td class="tg-0lax" colspan="4">____________________________________ -</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="tg-0lax" colspan="2" style="white-space: nowrap;">Yang Harus Dibayar</td>
                    <td class="tg-0lax right-text" colspan="2" style="white-space: nowrap;">{{ rupiah_format($tempTotal - $depo_cut) }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                @php $cicilan = $tempTotal - $depo_cut; @endphp
                @foreach ($order->payments as $key => $pembayaran)
                    <tr>
                        <td class="tg-0lax" colspan="2" style="white-space: nowrap;">Pembayaran {{ $loop->index + 1 }}</td>
                        <td class="tg-0lax right-text" colspan="2" style="white-space: nowrap;">{{ rupiah_format($pembayaran->amount) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    @php $cicilan -= $pembayaran->amount; @endphp
                @endforeach
                <tr>
                    <td class="tg-0lax" colspan="4">____________________________________ -</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="tg-0lax" colspan="2" style="white-space: nowrap;">Sisa Bayar</td>
                    <td class="tg-0lax right-text" colspan="2" style="white-space: nowrap;">{{ rupiah_format($cicilan) }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="tg-0lax center-text" style="text-align: center" colspan="4">Kurir :
                        {{ $order->purchase_orders[0]->expedition->nama_ekspedisi }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax center-text" style="text-align: center" colspan="4">Admin :
                        {{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <td class="tg-baqh center-text" style="text-align: center" colspan="4">Terima Kasih<br>Telah
                        Bertransaksi Disini</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
