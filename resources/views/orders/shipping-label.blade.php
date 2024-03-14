<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Label Design</title>
<style>
    body {
      font-family: Arial, sans-serif;
      margin: 0; /* Remove default margin */
    }
    .label {
      border: 2px solid black;
      padding: 20px;
      width: 95%; /* Full width */
      box-sizing: border-box; /* Include padding in the width */
    }
    .table-container {
      border: none; /* Make sure there's no border on the container */
    }
    .header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .address {
      font-size: 16px;
    }
    .footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
    }
    .weight {
      font-weight: bold;
    }
    .tg {
      border-collapse: collapse;
      border-spacing: 0;
      width: 100%; /* Make the table take up full width */
    }
    .tg td, .tg th {
      border: none; /* Remove borders from cells */
      font-family: Arial, sans-serif;
      font-size: 14px;
      overflow: hidden;
      word-break: normal;
    }
    .tg .tg-9wq8 {
      border: 1px solid black;
      border-color: inherit;
      text-align: center;
      vertical-align: middle;
    }
    .tg .tg-c3ow {
      border-color: inherit;
      text-align: center;
      vertical-align: top;
    }
    .tg .tg-0pky {
      border-color: inherit;
      text-align: left;
      vertical-align: top;
      padding-top: 5px;
      padding-bottom: 5px;
    }
    .tg .tg-0pkys {
        text-align: center;
    }
    @media print {
      .tg .tg-0pky img {
        display: none; /* Hide the image when printing */
      }
    }
  </style>
</head>
<body>

<div class="label">
    <div class="table-container">
        <table class="tg" style="border: none;">
            <thead>
                <tr>
                    <th class="tg-0pky" rowspan="11" style="width: 20%"></th>
                    <th class="tg-0pky">Kepada :</th>
                    <th class="tg-c3ow"></th>
                </tr>
                <tr>
                    <th class="tg-0pky">{{ $order->purchase->customer->name }}</th>
                    <th class="tg-c3ow"></th>
                </tr>
                <tr>
                    <th class="tg-0pky">{{ $order->purchase->customer->address }}</th>
                    <th class="tg-c3ow"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tg-0pky tg-0pkys"><img src="{{ asset('img/dtf.jpg') }}" alt="Company Logo" style="width:200px;"></td>
                    <td class="tg-0pky">Kec.  {{ $order->purchase->customer->kecamatans->dis_name }}, Kota {{ $order->purchase->customer->kota->city_name }}, O<br>Provinsi {{ $order->purchase->customer->province->prov_name }}<br>Telp: +62 {{ $order->purchase->customer->phone }}</td>
                    <td class="tg-c3ow"></td>
                </tr>
                <tr>
                    <td class="tg-0pky tg-0pkys">MANSYUR DTF</td>
                    <td class="tg-0pky"></td>
                    <td class="tg-c3ow"></td>
                </tr>
                <tr>
                    <td class="tg-0pky tg-0pkys">DTF Premium</td>
                    <td class="tg-0pky"></td>
                    <td class="tg-c3ow"></td>
                </tr>
                <tr>
                    <td class="tg-0pky tg-0pkys">#rasakangsablon</td>
                    <td class="tg-0pky"></td>
                    <td class="tg-c3ow"></td>
                </tr>
                <tr>
                    <td class="tg-0pky" rowspan="4"></td>
                    <td class="tg-0pky">Pengirim</td>
                    <td class="tg-c3ow"></td>
                </tr>
                <tr>
                    <td class="tg-0pky">Mansyur DTF</td>
                    <td class="tg-9wq8" rowspan="2">Kereta api (1.05 Kg)</td>
                </tr>
                <tr>
                    <td class="tg-0pky">012378342394</td>
                </tr>
                <tr>
                    <td class="tg-0pky" colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
