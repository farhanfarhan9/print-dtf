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
            height: 5cm !important;
        }

        .tg td,
        .tg th {
            font-family: Arial, sans-serif;
            font-size: 17px;
            padding: 3px 10px;
            word-break: normal;
            border: none;
            /* border: 1px solid black; */
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
                        <th class="tg-0pky" colspan="8"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tg-0pky" rowspan="8"></td>
                        <td class="tg-pb0m" rowspan="5">
                            <img src="{{ asset('img/dtf.jpg') }}" alt="Logo DTF Medan" width="100" style="margin-bottom: 7px">
                            <div style="margin-bottom: 5px;font-weight:bold">MANSYUR DTF</div>
                            <div style="margin-bottom: 5px;font-size:12px">DTF PREMIUM</div>
                            <div style="font-size:16.3px">#rasakangsablon</div>
                        </td>
                        <td class="tg-0pky" rowspan="5" colspan="3">
                            Kepada :
                            <p>{{ ucwords(strtolower($order->customer->name)) }}</p>
                            Alamat :<br>
                            {{ ucwords(strtolower($order->customer->address)) }}<br>
                            @if ($order->customer->district && $order->customer->city && $order->customer->provinsi)
                                Kec.
                                {{ $order->customer->district ? ucwords(strtolower($order->customer->kecamatans->dis_name)) : ucwords(strtolower($order->customer->district_name)) }},
                                Kota
                                {{ $order->customer->city ? ucwords(strtolower($order->customer->kota->city_name)) : ucwords(strtolower($order->customer->city_name)) }},
                                Provinsi
                                {{ $order->customer->provinsi ? ucwords(strtolower($order->customer->province->prov_name)) : ucwords(strtolower($order->customer->provinsi_name)) }}
                            @endif
                            <p>Telp: (+62){{ $order->customer->phone }}</p>
                        </td>
                        <td class="tg-0pky" rowspan="5"></td>
                        <td class="tg-0pky" rowspan="5"></td>
                        <td class="tg-0pky" rowspan="8"></td>
                    </tr>
                    <tr>
                        {{-- Must Empty Ignore This --}}
                    </tr>
                    <tr>
                        {{-- Must Empty Ignore This --}}
                    </tr>
                    <tr>
                        {{-- Must Empty Ignore This --}}
                    </tr>
                    <tr>
                        {{-- Must Empty Ignore This --}}
                    </tr>
                    <tr>
                        <td class="tg-0pky" rowspan="3"></td>
                        <td class="tg-lboi" style="font-weight:bold">Pengirim</td>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky"></td>
                        <td class="tg-0pky"></td>
                    </tr>
                    <tr>
                        <td class="tg-lboi" style="font-size:14px">Mansyur DTF</td>
                        <td class="tg-lboi"></td>
                        <td class="tg-9wq8" rowspan="2" colspan="3" style="font-size:15px;font-weight:bold">
                            Jasa Ekspedisi <br> {{ $order->purchase_orders[0]->expedition->nama_ekspedisi }}
                        </td>
                    </tr>
                    <tr>
                        <td class="tg-lboi" style="font-size:14px">(+62) 858-3130-2223</td>
                        <td class="tg-lboi"></td>
                    </tr>
                    <tr>
                        <td class="tg-0pky" colspan="8"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
