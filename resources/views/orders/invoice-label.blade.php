<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Label Design</title>
</head>
<body>

<div class="label">
    <style type="text/css">
    @font-face {
        font-family: 'DotMatrix';
        src: url('DotMatrix-Regular.ttf') format('truetype'); /* Local path to dot matrix font file */
        font-weight: normal;
        font-style: normal;
    }
    .tg  {border-collapse: collapse;border-spacing: 0;width: 100%;}
    .tg td, .tg th {
        border-style: none; /* Remove borders */
        font-family: 'DotMatrix', monospace; /* Use Dot Matrix font */
        font-size: 14px; /* Adjusted for Dot Matrix font visibility */
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }
    .tg .tg-baqh{text-align: center;vertical-align: top}
    .tg .tg-0lax{text-align: left;vertical-align: top}
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
            <td class="tg-0lax" colspan="4">___________________________________________________________________________ +</td>
          </tr>
          @php
              $cicilan = 0;
          @endphp
          @foreach($order->payments as $key => $pembayaran)
          <tr>
            <td class="tg-0lax">Pembayaran {{ $key+1 }}</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax">Rp. {{ number_format($pembayaran['amount'], 0, ',', '.') }}</td>
          </tr>
          @php
              $cicilan += $pembayaran['amount'];
          @endphp
          @endforeach
          <tr>
            <td class="tg-0lax" colspan="4">___________________________________________________________________________ -</td>
          </tr>
          <tr>
            <td class="tg-0lax">Total</td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax"></td>
            <td class="tg-0lax">Rp. {{ number_format(($order->product_price + $order->expedition->ongkir)-$cicilan, 0, ',', '.') }}</td>
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
