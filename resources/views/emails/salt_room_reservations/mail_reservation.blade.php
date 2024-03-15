@extends('emails.layouts.email')

@section('content')
    <style>
        p{
            color: black;
        }
        li{
            color: black;
        }
    </style>

    <p>Estimado cliente,</p>
    <p>Le recordamos su cita con nosotros el día {{ Carbon\Carbon::parse($record->date)->format('d/m/Y') }} a las {{ $record->time }} horas y para {{$record->adults}} ADULTOS y {{ $record->children}} menores en nuestra Sala de sal.</p>

    <ul>
        <li style="margin-bottom: 5px">El uso de calzado está prohibido. Se debe acceder con calcetines o calzas (si no tuvieran aquí las tenemos a la venta por 2 euros).</li>
        <li style="margin-bottom: 5px">A la sala se debe acceder con ropa cómoda, seca y, preferiblemente, clara.</li>
        <li style="margin-bottom: 5px">Se debe mantener el orden y el silencio durante la sesión. Los menores deben estar siempre acompañados por un adulto.</li>
        <li style="margin-bottom: 5px">Recomendamos NO usar dispositivos móviles dentro de la sala.</li>
        <li style="margin-bottom: 5px">Está prohibido comer dentro de la sala. Se recomienda beber agua antes y después de la sesión.</li>
    </ul>

    <p>Muchas gracias</p>

@endsection

