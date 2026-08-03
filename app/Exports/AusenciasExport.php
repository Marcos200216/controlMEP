<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AusenciasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected Collection $registros;

    public function __construct(Collection $registros)
    {
        $this->registros = $registros;
    }

    public function collection()
    {
        return $this->registros;
    }

    public function headings(): array
    {
        return ['Estudiante', 'Docente', 'Materia', 'Sección', 'Fecha', 'Cantidad'];
    }

    public function map($registro): array
    {
        return [
            $registro->estudiante->nombre_completo,
            $registro->docente->nombre,
            $registro->materia->nombre,
            $registro->seccion->nombre,
            $registro->fecha->format('d/m/Y'),
            $registro->cantidad,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}