<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Resumen Informe de Producción</title>

    <style>
        table,
        th,
        td {
            border: none;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div style="font-size: 10px; border-bottom: 1px #333333 solid">
        <div>Balneario Thermas de Griñon</div>
    </div>
    <div style="width: 100%; text-align: center; font-weight: bold; font-size: 14px; border-bottom: 1px #333333 solid">
        RESUMEN INFORME DE PRODUCCIÓN
    </div>
    <div
        style="display: inline-block; border-bottom: 1px #333333 solid; font-size: 12px; margin-top: 5px; margin-bottom: 5px">
        <div style="display: inline-block; margin-right: 20px"><b>FECHA: </b>{!! date('d/m/Y') !!}</div>
        <div style="display: inline-block; margin-right: 20px"><b>DESDE: </b>{{ array_key_exists('created_at_from', $filters) ? $filters['created_at_from']:'' }}</div>
        <div style="display: inline-block; margin-right: 20px"><b>HASTA: </b>{{ array_key_exists('created_at_to', $filters) ? $filters['created_at_to']:'' }}</div>
    </div>
    <table style="width: 100%">
        <tr class="font-12">
            <th style="text-align: left;">Concepto</th>
            <th>Cantidad</th>
            <th>Importe</th>
        </tr>

        @php($totalAmount = 0)
        @php($totalQuantity = 0)

        @foreach ($data['records'] as $detail)
            @php($totalAmount += $detail['amount'])
            @php($totalQuantity += $detail['quantity'])
            <tr>
                <td style="text-align: left">{{ $detail['product_name'] }}</td>
                <td>{{ $detail['quantity'] }}</td>
                <td>{{ number_format($detail['amount'], 2, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <td>&nbsp;</td>
            <td style="border-top: 1px #333333 solid">{{ number_format($totalQuantity, 0, ',', '.') }}</td>
            <td style="border-top: 1px #333333 solid">{{ number_format($totalAmount, 2, ',', '.') }}</td>
        </tr>
    </table>
</body>
