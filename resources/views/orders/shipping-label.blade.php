<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Design</title>
    <style type="text/css">
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        .tg td,
        .tg th {
            font-family: Arial, sans-serif;
            font-size: 9px;
            padding: 3px 10px;
            word-break: normal;
            border: none; /* Remove all borders */
        }

        .tg th {
            font-weight: normal;
        }

        .tg .tg-pb0m {
            text-align: center;
            vertical-align: bottom;
        }

        .tg .tg-lboi {
            text-align: left;
            vertical-align: middle;
        }

        .tg .tg-9wq8 {
            text-align: center;
            vertical-align: middle;
            border: 1px solid black;
        }

        .tg .tg-0pky {
            text-align: left;
            vertical-align: top;
        }

        .table-container {
            width: 100%;
            margin: 0 auto;
            border: 2px solid black;
        }

        .borda {
            margin: 0 auto;
            border: 2px solid black;
        }

        .label {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="label">
        <div class="table-container">
            <table class="tg">
                <thead>
                    <tr>
                        <th class="tg-0pky"></th>
                        <th class="tg-0pky" colspan="3"></th>
                        <th class="tg-0pky"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tg-0pky" rowspan="8"></td>
                        <td class="tg-pb0m" rowspan="2">
                            <img src="{{ asset('img/dtf.jpg') }}" alt="Logo DTF Medan" width="100">
                        </td>
                        <td class="tg-0pky">
                            Kepada :
                            <p>{{ ucwords(strtolower($order->customer->name)) }}</p>
                            Alamat :<br>
                            {{ ucwords(strtolower($order->customer->address)) }}<br>
                            Kec. {{ $order->customer->district ? ucwords(strtolower($order->customer->kecamatans->dis_name)) : ucwords(strtolower($order->customer->district_name)) }},
                            Kota {{ $order->customer->city ? ucwords(strtolower($order->customer->kota->city_name)) : ucwords(strtolower($order->customer->city_name)) }},
                            Provinsi {{ $order->customer->provinsi ? ucwords(strtolower($order->customer->province->prov_name)) : ucwords(strtolower($order->customer->provinsi_name)) }}
                            <p>Telp: (+62){{ $order->customer->phone }}</p>
                        </td>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky" rowspan="8"></td>
                    </tr>
                    <tr>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky"></td>
                    </tr>
                    <tr>
                        <td class="tg-pb0m">MANSYUR DTF</td>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky"></td>
                    </tr>
                    <tr>
                        <td class="tg-pb0m">DTF PREMIUM</td>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky"></td>
                    </tr>
                    <tr>
                        <td class="tg-pb0m">#rasakangsablon</td>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky"></td>
                    </tr>
                    <tr>
                        <td class="tg-0pky" rowspan="3"></td>
                        <td class="tg-lboi">Pengirim</td>
                        <td class="tg-0pky"></td>
                    </tr>
                    <tr>
                        <td class="tg-lboi">Mansyur DTF</td>
                        <td class="tg-9wq8" rowspan="2">
                            Jasa Ekspedisi <br> {{ $order->purchase_orders[0]->expedition->nama_ekspedisi }}
                        </td>
                    </tr>
                    <tr>
                        <td class="tg-lboi">(+62) 858-3130-2223</td>
                    </tr>
                    <tr>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky" colspan="3"></td>
                        <td class="tg-0pky"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
