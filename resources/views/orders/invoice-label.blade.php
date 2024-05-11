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
            text-align: center; /* Center text for better readability */
            vertical-align: top;
        }
        .tg .tg-0lax {
            border-bottom: 1px dashed black; /* Use dashed lines for separation */
        }
    </style>
        <table class="tg">
        <thead>
          <tr>
            <th class="tg-0lax" colspan="4">MANSYUR DTF</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="tg-baqh" colspan="4">DTF Premium #rasakangsablon</td>
          </tr>
          <tr>
            <td class="tg-baqh" colspan="4">Jalan Setia Budi, Komplek Ruko<br>Milala Mas Blok B no 24</td>
          </tr>
          <tr>
            <td class="tg-baqh" colspan="4">Invoice Code : {{ $order->invoice_code }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4">Date : {{ Carbon\Carbon::parse($order->purchase->updated_at)->format('d-m-Y') }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4">To : {{ $order->purchase->customer->name }} </td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4"></td>
          </tr>
          <tr>
            <td class="tg-0lax">Produk</td>
            <td class="tg-0lax">Qty</td>
            <td class="tg-0lax">Harga</td>
            <td class="tg-0lax">Subtotal</td>
          </tr>
        <tr>
            <td class="tg-0lax">{{ $order->product->nama_produk }}</td>
            <td class="tg-0lax">{{ number_format($order->qty, 0, ',', '.') }}</td>
            <td class="tg-0lax">Rp. {{ number_format($order->product_price, 0, ',', '.') }}</td>
            <td class="tg-0lax">Rp. {{ number_format($order->product_price, 0, ',', '.') }}</td>
        </tr>
          <tr>
            <td class="tg-0lax">Jasa Kirim</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax">Rp. {{ number_format($order->expedition->ongkir, 0, ',', '.') }}</td>
            <td class="tg-0lax">Rp. {{ number_format($order->expedition->ongkir, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4">______________________________________________________________________________________________ +</td>
          </tr>
          <tr>
            <td class="tg-0lax">Deposit</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax">Rp. {{ number_format($order->deposit_cut, 0, ',', '.') }}</td>
          </tr>
          @php
              $cicilan = 0;
          @endphp
        @php
            $invoice = $order->get();
        @endphp
          @foreach($invoice as $key => $pembayaran)
          <tr>
            <td class="tg-0lax">Pembayaran {{ $loop->index + 1 }}</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax">Rp. {{ $pembayaran->paymentsid }}</td>
            {{-- <td class="tg-0lax">Rp. {{ number_format($pembayaran->paymentsid, 0, ',', '.') }}</td> --}}
          </tr>
          @php
              $cicilan += $pembayaran->amount;
          @endphp
          @endforeach
          <tr>
            <td class="tg-0lax" colspan="4">______________________________________________________________________________________________ -</td>
          </tr>
          <tr>
            <td class="tg-0lax">Total</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax">Rp. {{ number_format((($order->product_price + $order->expedition->ongkir)-$order->deposit_cut)-$cicilan, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4">Kurir : {{ $order->expedition->nama_ekspedisi }}</td>
          </tr>
          <tr>
            <td class="tg-0lax" colspan="4">Admin : {{ Auth::user()->name }}</td>
          </tr>
          <tr>
            <td class="tg-baqh" colspan="4">Terima Kasih<br>Telah Bertransaksi Disini</td>
          </tr>
        </tbody>
        </table>
</div>

</body>
</html>
