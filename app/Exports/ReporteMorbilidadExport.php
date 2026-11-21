<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReporteMorbilidadExport implements FromArray, WithHeadings, WithStyles
{
    protected $resultados;
    protected $fechaInicio;
    protected $fechaFin;
    protected $totales;

    public function __construct($resultados, $fechaInicio, $fechaFin, $totales)
    {
        $this->resultados = $resultados;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->totales = $totales;
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE MORBILIDAD'],
            ['Período:', $this->fechaInicio . ' al ' . $this->fechaFin],
            ['Total Casos:', $this->totales['total_casos'] ?? 0, 'Hombres:', $this->totales['total_masculino'] ?? 0, 'Mujeres:', $this->totales['total_femenino'] ?? 0],
            [], // Fila vacía
            ['Capítulo', 'Código CIE-10', 'Diagnóstico', 'Total Casos', 'Masculino', 'Femenino'],
        ];
    }

    public function array(): array
    {
        $data = [];

        // Iterar sobre la colección agrupada (Capítulo => Diagnósticos)
        foreach ($this->resultados as $capitulo => $diagnosticos) {
            // Añadir fila del Capítulo
            $data[] = [
                $capitulo ?? 'SIN CAPÍTULO',
                '',
                '',
                '',
                '',
                '',
            ];

            foreach ($diagnosticos as $diag) {
                // Como los resultados ahora son arrays (por el toArray()), accedemos con corchetes
                $data[] = [
                    '', // El capítulo ya se mostró en la fila anterior
                    $diag['cie10_codigo'],
                    $diag['cie10_descripcion'],
                    $diag['total_casos'],
                    $diag['total_masculino'],
                    $diag['total_femenino'],
                ];
            }
        }

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        // Estilo para el título
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Estilo para los encabezados de la tabla (Fila 5)
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');

        // Ancho de columnas para que se lea bien
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        return [];
    }
}
