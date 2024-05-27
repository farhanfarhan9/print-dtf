<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contoh Tabel</title>
    <style type="text/css">
        body, html {
            margin: 5 15 10 15;
            padding: 0;
        }
        .tg {
        border-collapse: collapse;
        border-spacing: 0;
        max-height: 5cm;
        }

        .tg td,
        .tg th {
            border: none;
            /* border: 1px solid black; */
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
                        <img src="https://print-dtf.dev/img/dtf.jpg" alt="Logo DTF Medan" width="120" height="65">

                        <div style="padding-top:5px;font-weight:bold;font-size:16px">MANSYUR DTF</div>
                        <div style="padding-top:5px;">DTF PREMIUM</div>
                        <div style="padding-top:5px;">#rasakangsablon</div>

                            </td>
                    <td class="tg-0pky" rowspan="6"></td>
                    <td class="tg-54m6" style="line-height: 1.5;" rowspan="2"><b>Kepada :</b><br>{{ ucwords(strtolower($order->customer->name)) }}<br>
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
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                </tr>
                <tr>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
                    <td class="tg-sej6"></td>
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
                    <td class="tg-kwn7" colspan="3" rowspan="2" style="padding-left: 40px;padding-right: 40px">Jasa Ekspedisi JNE</td>
                    <td class="tg-sej6"></td>
                </tr>
                <tr>
                    <td class="tg-sej6"></td>
                    <td class="tg-7rv2">085831302223</td>
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
