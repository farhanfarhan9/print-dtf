<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Label Design</title>
</head>
<body>

<div class="label">
    <style>
        @font-face {
            font-family: 'DotMatrix';
            src: url('DotMatrix-Regular.ttf') format('truetype'); /* Path to dot matrix font file */
        }
        body {
            margin: 0;
            font-family: 'DotMatrix', monospace; /* Apply Dot Matrix for the thermal print look */
            font-size: 10px; /* Smaller font size to fit content within the width */
        }
        .label {
            width: 148mm; /* Set the width to match thermal paper */
            padding: 2mm; /* Minimal padding to maximize space */
            box-sizing: border-box;
        }
        .tg {
            width: 100%;
            table-layout: fixed; /* Helps with consistent column width */
        }
        .tg td, .tg th {
            overflow: hidden;
            word-break: break-word; /* Break words to prevent overflow */
            padding: 1mm; /* Reduce padding */
        }
        .tg .tg-0lax, .tg .tg-baqh {
            text-align: left; /* Center text for better readability */
            vertical-align: top;
        }
        /* .tg .tg-0lax {
            border-bottom: 1px dashed black; /* Use dashed lines for separation */
        } */
    </style>
        <table class="tg">
        <thead>
          <tr>
            <th class="tg-0lax" colspan="4" style="text-align: center;">MANSYUR DTF</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="tg-baqh" colspan="4" style="text-align: center;">DTF Premium #rasakangsablon</td>
          </tr>
          <tr>
            <td class="tg-baqh" colspan="4" style="text-align: center;">Jalan Setia Budi, Komplek Ruko<br>Milala Mas Blok B no 24</td>
          </tr>
          <tr>
            <td class="tg-baqh" colspan="4" style="text-align: center;">Invoice Code : {{ $order->invoice_code }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4" style="text-align: center;">Date : {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4" style="text-align: center;">To : {{ $order->customer->name }} </td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4"></td>
          </tr>
          @php
              $tempTotal = 0;
          @endphp
            @foreach ($order->purchase_orders as $pembayaran)
                <tr>
                    <td class="tg-0lax" colspan="3">Date : {{ Carbon\Carbon::parse($pembayaran->updated_at)->format('d-m-Y') }} (Invoice Code {{ $pembayaran->invoice_code }})</td>
                    <td class="tg-0lax"></td>
                </tr>
                <tr>
                    <td class="tg-0lax">Produk</td>
                    <td class="tg-0lax">Qty</td>
                    <td class="tg-0lax">Harga</td>
                    <td class="tg-0lax" style="text-align: center;">Subtotal</td>
                </tr>
                <tr>
                    <td class="tg-0lax">{{ $pembayaran->product->nama_produk }}</td>
                    <td class="tg-0lax">{{ number_format($pembayaran->qty, 0, ',', '.') }}</td>
                    <td class="tg-0lax">{{ rupiah_format($pembayaran->product_price) }}</td>
                    <td class="tg-0lax" style="text-align: right; padding-right:20px">{{ rupiah_format($pembayaran->product_price) }}</td>
                </tr>
                <tr>
                <td class="tg-0lax">Jasa Kirim</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax">{{ rupiah_format($pembayaran->expedition_price) }}</td>
                <td class="tg-0lax" style="text-align: right; padding-right:20px">{{ rupiah_format($pembayaran->expedition_price) }}</td>
                </tr>
                <p></p>
                @php
                    $tempTotal += $pembayaran->product_price + $pembayaran->expedition_price;
                @endphp
            @endforeach
            <tr>
                <td class="tg-0lax" colspan="4">_________________________________________________________________________________________ +</td>
            </tr>
            <tr>
                <td class="tg-0lax">Grand Total</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax" style="text-align: right; padding-right:20px">{{ rupiah_format($tempTotal) }}</td>
            </tr>
          @foreach ($order->purchase_orders as $pembayaran)
            <tr>
                <td class="tg-0lax">Deposit {{ $loop->index + 1 }}</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax" style="text-align: right; padding-right:20px">{{ rupiah_format($pembayaran->deposit_cut) }}</td>
            </tr>
          @endforeach
            <tr>
                <td class="tg-0lax" colspan="4">_________________________________________________________________________________________ -</td>
            </tr>
            <tr>
                <td class="tg-0lax">Yang Harus Dibayar</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax" style="text-align: right; padding-right:20px">{{ rupiah_format($tempTotal-$pembayaran->deposit_cut) }}</td>
            </tr>
          @php
              $cicilan = $tempTotal;
          @endphp
          @foreach($order->payments as $key => $pembayaran)
          <tr>
            <td class="tg-0lax">Pembayaran {{ $loop->index + 1 }}</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax" style="text-align: right; padding-right:20px">{{ rupiah_format($pembayaran->amount) }}</td>
          </tr>
          @php
              $cicilan-=$pembayaran->amount;
          @endphp
          @endforeach
          <tr>
            <td class="tg-0lax" colspan="4">_________________________________________________________________________________________ -</td>
          </tr>
          <tr>
            <td class="tg-0lax">Sisa Bayar</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax" style="text-align: right; padding-right:20px">{{ rupiah_format($cicilan) }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4" style="text-align: center">Kurir : {{ $order->purchase_orders[0]->expedition->nama_ekspedisi }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4" style="text-align: center">Admin : {{ Auth::user()->name }}</td>
          </tr>
          <tr>
            <td class="tg-baqh" colspan="4" style="text-align: center">Terima Kasih<br>Telah Bertransaksi Disini</td>
          </tr>
        </tbody>
        </table>
</div>

</body>
</html>
