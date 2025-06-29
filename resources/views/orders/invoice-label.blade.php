<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: monospace;
            /* font-size: .6em; */
            font-size: 12px;
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
                @foreach ($order->purchase_orders->where('po_status', '!=', 'cancel') as $pembayaran)
                    <tr>
                        <td class="tg-0lax center" style="text-align: center">Produk</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="2">
                            {{ $pembayaran->product->nama_produk }}
                        </td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
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
                        {{-- <td class="" style="text-align: center">Produk</td> --}}
                        {{-- <td class="" style="text-align: center">&nbsp;</td> --}}
                        <td class="" style="width: 33.33%;">Qty</td>
                        <td class="" style="width: 33.33%;">Harga</td>
                        <td class="" style="width: 33.33%;">Subtotal</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        {{-- <td class="tg-0lax">{{ $pembayaran->product->nama_produk }}</td> --}}
                        {{-- <td class="tg-0lax">&nbsp;</td> --}}
                        <td class="tg-0lax" style="font-size: 9px">{{ number_format($pembayaran->qty, 0, ',', '.') }}
                        </td>
                        <td class="tg-0lax" style="font-size: 9px">
                            @if ($pembayaran->product_price > 0)
                                {{ rupiah_format($pembayaran->product_price / $pembayaran->qty) }}
                            @else
                                0
                            @endif
                            {{-- @dump($pembayaran->product_price) --}}
                        </td>
                        <td class="tg-0lax right-text" style="font-size: 9px">
                            {{ rupiah_format($pembayaran->product_price) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax" style="font-size: 9px">Jasa Kirim</td>
                        {{-- <td class="tg-0lax"></td> --}}
                        <td class="tg-0lax" style="font-size: 9px">{{ rupiah_format($pembayaran->expedition_price) }}
                        </td>
                        <td class="tg-0lax right-text" style="font-size: 9px">
                            {{ rupiah_format($pembayaran->expedition_price) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax" style="font-size: 9px">Biaya Tambahan</td>
                        {{-- <td class="tg-0lax"></td> --}}
                        <td class="tg-0lax" style="font-size: 9px">{{ rupiah_format($pembayaran->additional_price) }}
                        </td>
                        <td class="tg-0lax right-text" style="font-size: 9px">
                            {{ rupiah_format($pembayaran->additional_price) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax" style="font-size: 9px">Diskon</td>
                        <td class="tg-0lax" style="font-size: 9px"></td>
                        <td class="tg-0lax" style="font-size: 9px">{{ rupiah_format($pembayaran->discount) }}</td>
                        <td class="tg-0lax right-text" style="font-size: 9px">
                            {{ rupiah_format($pembayaran->discount) }}</td>
                    </tr>

                    @php $tempTotal += $pembayaran->product_price + $pembayaran->expedition_price + $pembayaran->additional_price - $pembayaran->discount; @endphp
                @endforeach
                <tr>
                    <td class="tg-0lax" colspan="4">________________________ +</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="tg-0lax" colspan="1">Grand Total</td>
                    <td class="tg-0lax right-text" colspan="3" style="text-align: center">
                        {{ rupiah_format($tempTotal) }}</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                @php $depo_cut = 0; @endphp
                @foreach ($order->purchase_orders->where('po_status', '!=', 'cancel') as $pembayaran)
                    <tr>
                        <td class="tg-0lax" colspan="1">Deposit {{ $loop->index + 1 }}</td>
                        <td class="tg-0lax right-text" colspan="3" style="text-align: center">
                            {{ rupiah_format($pembayaran->deposit_cut) }}
                        </td>
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
                    <td class="tg-0lax" colspan="4">________________________ -</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                {{-- <tr>
                    <td class="tg-0lax" colspan="1">Yang Harus Dibayar</td>
                    <td class="tg-0lax right-text" colspan="3">{{ rupiah_format($tempTotal - $depo_cut) }}</td>
                </tr> --}}
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                @php $cicilan = $tempTotal - $depo_cut; @endphp
                @php
                    $hasDP = $order->payments->contains('is_dp', 1);
                @endphp

                @forelse ($order->payments as $key => $pembayaran)
                    <tr>
                        @if ($hasDP && $pembayaran->is_dp == 1)
                            <td class="tg-0lax">DP</td>
                        @else
                            @if ($hasDP)
                                <td class="tg-0lax">Cicilan {{ $loop->index }}</td>
                            @else
                                <td class="tg-0lax">Cicilan {{ $loop->index + 1 }}</td>
                            @endif
                        @endif
                        <td class="tg-0lax right-text" colspan="3" style="text-align: center">
                            {{ rupiah_format($pembayaran->amount) }}</td>
                    </tr>
                    <tr>
                        <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    </tr>
                    @php $cicilan -= $pembayaran->amount; @endphp
                @empty
                    <tr>
                        <td class="tg-0lax" colspan="4">Belum ada pembayaran</td>
                    </tr>
                @endforelse
                <tr>
                    <td class="tg-0lax" colspan="4">________________________ -</td>
                </tr>
                <tr>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                    <td class="tg-0lax center" style="text-align: center" colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="tg-0lax" colspan="1">Sisa</td>
                    <td class="tg-0lax right-text" colspan="3" style="text-align: center">
                        {{ rupiah_format($cicilan) }}</td>
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
