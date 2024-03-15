<?php

namespace Domain\Clients\Exports;

use Illuminate\Database\Eloquent\Builder;

class ClientsExport
{

    /**
     * @param Builder $records
     * @return string|null
     */
    public function __invoke(Builder $records): ?string
    {
        $name = uniqid() . '.csv';
        $handle = fopen(storage_path('app/' . $name), 'w');
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($handle, [
            'Email',
            'Identificación',
            'Nombre',
            'Teléfono',
            'Cumpleaños',
            'Dirección',
            'Código Postal',
            'LOPD',
            'Localidad',
            'Provincia',
            'Creado',
            'Última Modificación'
        ]);

        // Rows
        $records->chunk(1000, function ($items) use ($handle) {
            foreach ($items as $item) {
                $lopd = $item->lopd_agree;
                if (empty($lopd)) {
                    $lopd = 'Pendiente';
                } else {
                    $lopd = $lopd === 1 ? 'Firmada' : 'No datos';
                }

                fputcsv($handle, [
                    $item->email,
                    $item->document,
                    $item->name,
                    $item->phone,
                    $item->birthdate ? \Carbon\Carbon::parse($item->birthdate)->format('d/m/Y') : null,
                    $item->address,
                    $item->postcode,
                    $lopd,
                    $item->locality ? $item->locality->singular_entity_name : null,
                    $item->locality && $item->locality->province ? $item->locality->province->name : null,
                    \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i'),
                    $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') : null,
                ]);
            };
        });

        fclose($handle);

        return $name;
    }
}
