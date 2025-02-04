<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Pengiriman</title>
    <style type="text/css">
        body, html {
            margin: 5 15 10 15;
            padding: 0;
        }
        .tg {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        max-height: 5cm;
        }

        .tg td,
        .tg th {
            border: none;
            /* border: 1px solid black; */
        }
        .borda{
            /* border: none; */
            border: 1px solid black !important;
        }
        .table-container {
            width: 100%;
            margin: 0 auto;
            padding: 10 auto;
            border: 2px solid black;
        }
        .tg td {
        font-family: Arial, sans-serif;
        font-size: 14px;
        overflow: hidden;
        padding: 2px 5px;
        word-break: normal;
        }
        .tg th {
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        overflow: hidden;
        padding: 2px 5px;
        word-break: normal;
        }
        .tg .tg-n65g {
        border-color: inherit;
        font-family: Arial, sans-serif, Monaco, monospace !important;
        text-align: center;
        vertical-align: bottom;
        }
        .tg .tg-7rv2 {
        border-color: inherit;
        font-family: Arial, sans-serif, Monaco, monospace !important;
        text-align: left;
        vertical-align: middle;
        }
        .tg .tg-sej6 {
        border-color: inherit;
        font-family: Arial, sans-serif, Monaco, monospace !important;
        text-align: left;
        vertical-align: top;
        }
        .tg .tg-0pky {
        border-color: inherit;
        text-align: left;
        vertical-align: top;
        }
        .tg .tg-54m6 {
        border-color: inherit;
        font-family: Arial, sans-serif, Monaco, monospace !important;
        font-size: 15px;
        text-align: left;
        vertical-align: top;
        }
        .tg .tg-kwn7 {
        border-color: inherit;
        font-family: Arial, sans-serif, Monaco, monospace !important;
        font-weight: bold;
        text-align: left;
        vertical-align: middle;
        }

    </style>
</head>
<body>
    <div class="table-container">
        <table class="tg">
            <thead>
                <tr>
                    <th class="tg-sej6"></th>
                    <th class="tg-sej6"></th>
                    <th class="tg-0pky"></th>
                    <th class="tg-sej6"></th>
                    <th class="tg-sej6"></th>
                    <th class="tg-sej6"></th>
                    <th class="tg-sej6"></th>
                    <th class="tg-sej6"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tg-sej6"></td>
                    <td class="tg-n65g" rowspan="2">
                        <p></p>
                        <img src="{{ asset('img/dtf.png') }}" alt="Logo DTF Medan" width="120" height="65">

                        <div style="padding-top:5px;font-weight:bold;font-size:16px">MANSYUR DTF</div>
                        <div style="padding-top:5px;">DTF PREMIUM</div>
                        <div style="padding-top:5px;">#rasakangsablon</div>

                            </td>
                    <td class="tg-0pky" rowspan="6"></td>
                    <td class="tg-54m6" style="line-height: 1.5;" rowspan="2" colspan="4"><b>Kepada :</b><br>{{ ucwords(strtolower($order->customer->name)) }}<br>
                        {{ ucwords(strtolower($order->customer->address)) }}<br>
                            @if ($order->customer->district && $order->customer->city && $order->customer->provinsi)
                                Kec.
                                {{ $order->customer->district ? ucwords(strtolower($order->customer->kecamatans->dis_name)) : ucwords(strtolower($order->customer->district_name)) }},
                                Kota
                                {{ $order->customer->city ? ucwords(strtolower($order->customer->kota->city_name)) : ucwords(strtolower($order->customer->city_name)) }},
                                Provinsi
                                {{ $order->customer->provinsi ? ucwords(strtolower($order->customer->province->prov_name)) : ucwords(strtolower($order->customer->provinsi_name)) }}
                            @endif
                            <br>
                            Telp: 0{{ $order->customer->phone }}</td>
                    {{-- <td class="tg-sej6">1</td>
                    <td class="tg-sej6">1</td>
                    <td class="tg-sej6">1</td> --}}
                    <td class="tg-sej6"></td>
                </tr>
                <tr>
                    <td class="tg-sej6"></td>
                    {{-- <td class="tg-sej6">1</td>
                    <td class="tg-sej6">1</td>
                    <td class="tg-sej6">1</td> --}}
                    <td class="tg-sej6"></td>
                </tr>
                <tr>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6" rowspan="4"></td>
                    <td class="tg-kwn7">Pengirim :</td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                </tr>
                <tr>
                    <td class="tg-sej6"></td>
                    <td class="tg-7rv2">Mansyur DTF</td>
                    <td class="tg-sej6"></td>
                    <td class="tg-kwn7 borda" colspan="2" rowspan="2" style="width:100%;text-align:center">Jasa Ekspedisi {{ $order->purchase_orders[0]->expedition->nama_ekspedisi }}</td>
                    <td class="tg-sej6"></td>
                </tr>
                <tr>
                    <td class="tg-sej6"></td>
                    <td class="tg-7rv2">0821-7126-0300</td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                </tr>
                <tr>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
