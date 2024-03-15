<?php

namespace Domain\Orders\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ProductionReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
{
    private $ivaPercentage, $totalPrice, $totalIva, $totalGrossAmount;

    public function __construct(private readonly Collection $records, readonly array $dates)
    {
        $this->ivaPercentage = config('system.iva');
        $this->totalPrice = 0;
        $this->totalIva = 0;
        $this->totalGrossAmount = 0;
    }

    public function collection(): Collection
    {
        return $this->records->transform(function ($item) {

            $this->totalPrice += $item->price * $item->quantity;
            $iva = $item->price - round($item->price / floatval('1.' . $this->ivaPercentage), 2);
            $this->totalIva += $iva;
            $this->totalGrossAmount += $item->price - $iva;

            $clientName = $item->order->client->name;
            $clientName .= $item->order->telephone_sale_seq ? ' ' . $item->order->telephone_sale_seq : '';
            $clientName .= $item->order->counter_sale_seq ? ' ' . $item->order->counter_sale_seq : '';

            return [
                Carbon::parse($item->created_at)->format('d/m/Y'),
                $item->order->company->name,
                $clientName,
                $item->order->ticket_number,
                $item->product_name,
                $item->quantity,
                number_format($item->price - $iva, 2, ',', '.'),
                number_format($iva, 2, ',', '.'),
                number_format($item->price * $item->quantity, 2, ',', '.'),
                $item->order->discount,
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['Balneario Thermas de Griñon'],
            ['INFORME DE PRODUCCIÓN'],
            ['FECHA: ' . date('d/m/Y'), 'DESDE: ' .  $this->dates['from'], 'HASTA: ' .  $this->dates['to']],
            [],
            ['Fecha', 'Empresa', 'Cliente', 'No . Ticket', 'Concepto', 'Und', 'Precio', 'Iva', 'Importe', 'Dto.'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $event->sheet->mergeCells('A1:B1');
                $event->sheet->mergeCells('A2:I2');

                $event->sheet->getStyle('A2:I2')->getFont()->setBold(true);
                $event->sheet->getStyle('A3:C3')->getFont()->setBold(true);
                $event->sheet->getStyle('A5:I5')->getFont()->setBold(true);

                $event->sheet->getStyle('A2:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->getStyle('A2:I2')->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                $event->sheet->getStyle('A2:I2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

                $event->sheet->getDelegate()->freezePane('A6');

                $event->sheet->setCellValue('G' . ($event->sheet->getHighestDataRow() + 1), number_format($this->totalGrossAmount, 2, ',', '.'));
                $event->sheet->getStyle('G' . ($event->sheet->getHighestDataRow()))->getFont()->setBold(true);
                $event->sheet->getStyle('G' . ($event->sheet->getHighestDataRow()))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('G' . ($event->sheet->getHighestDataRow()))->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

                $event->sheet->setCellValue('H' . ($event->sheet->getHighestDataRow()), number_format($this->totalIva, 2, ',', '.'));
                $event->sheet->getStyle('H' . ($event->sheet->getHighestDataRow()))->getFont()->setBold(true);
                $event->sheet->getStyle('H' . ($event->sheet->getHighestDataRow()))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('H' . ($event->sheet->getHighestDataRow()))->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

                $event->sheet->setCellValue('I' . ($event->sheet->getHighestDataRow()), number_format($this->totalPrice, 2, ',', '.'));
                $event->sheet->getStyle('I' . ($event->sheet->getHighestDataRow()))->getFont()->setBold(true);
                $event->sheet->getStyle('I' . ($event->sheet->getHighestDataRow()))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('I' . ($event->sheet->getHighestDataRow()))->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
