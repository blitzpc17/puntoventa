<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsLayoutExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        // Retornar colección vacía para el layout
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'CODIGO B/CLAVE',       
            'NOMBRE',
            'DESCRIPCION',
            'PRECIO_COMPRA',
            'PRECIO_VENTA',
            'PRECIO_MAYOREO',
            'INVENTARIO', //CANTIDAD
            'INVENTARIO_MINIMO'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para los encabezados
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3498DB'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Autoajustar el ancho de las columnas
        foreach(range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Congelar la primera fila (encabezados)
        $sheet->freezePane('A2');

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'PRODUCTOS';
    }
}
