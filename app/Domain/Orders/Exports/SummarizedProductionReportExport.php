<?php

namespace Domain\Orders\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SummarizedProductionReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
{
    private $totalAmount = 0;
    private $totalQuantity = 0;

    public function __construct(private readonly Collection $records, readonly array $dates)
    {
    }

    public function collection(): Collection
    {
        return $this->records->transform(function ($item) {

            $this->totalAmount += $item->amount;
            $this->totalQuantity += $item->quantity;

            return [
                $item->product_name,
                $item->quantity,
                number_format($item->amount, 2, ',', '.'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['Balneario Thermas de Griñon'],
            ['RESUMEN INFORME DE PRODUCCIÓN'],
            ['FECHA: ' . date('d/m/Y'), 'DESDE: ' .  $this->dates['from'], 'HASTA: ' .  $this->dates['to']],
            [],
            ['Concepto', 'Cantidad', 'Importe'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $event->sheet->mergeCells('A2:C2');

                $event->sheet->getStyle('A2:C2')->getFont()->setBold(true);
                $event->sheet->getStyle('A3:C3')->getFont()->setBold(true);
                $event->sheet->getStyle('A5:C5')->getFont()->setBold(true);

                $event->sheet->getStyle('A2:C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->getStyle('A2:C2')->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                $event->sheet->getStyle('A2:C2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

                $event->sheet->getDelegate()->freezePane('A6');

                $event->sheet->setCellValue('B' . ($event->sheet->getHighestDataRow() + 1), number_format($this->totalQuantity, 2, ',', '.'));
                $event->sheet->getStyle('B' . ($event->sheet->getHighestDataRow()))->getFont()->setBold(true);
                $event->sheet->getStyle('B' . ($event->sheet->getHighestDataRow()))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('B' . ($event->sheet->getHighestDataRow()))->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                
                $event->sheet->setCellValue('C' . ($event->sheet->getHighestDataRow()), number_format($this->totalAmount, 2, ',', '.'));
                $event->sheet->getStyle('C' . ($event->sheet->getHighestDataRow()))->getFont()->setBold(true);
                $event->sheet->getStyle('C' . ($event->sheet->getHighestDataRow()))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('C' . ($event->sheet->getHighestDataRow()))->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
