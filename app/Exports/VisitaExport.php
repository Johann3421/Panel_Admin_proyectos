<?php

namespace App\Exports;

use App\Models\Visita1;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VisitaExport implements FromCollection, WithHeadings
{
    protected $busqueda;
    protected $fecha;

    public function __construct($busqueda, $fecha)
    {
        $this->busqueda = $busqueda;
        $this->fecha = $fecha;
    }

    public function collection()
    {
        // Construye la consulta basada en los filtros
        $query = Visita1::query();

        if (!empty($this->busqueda)) {
            $query->where(function ($q) {
                $q->where('nombre', 'LIKE', "%{$this->busqueda}%")
                  ->orWhere('dni', 'LIKE', "%{$this->busqueda}%")
                  ->orWhere('smotivo', 'LIKE', "%{$this->busqueda}%")
                  ->orWhere('lugar', 'LIKE', "%{$this->busqueda}%");
            });
        }

        if (!empty($this->fecha)) {
            $query->whereDate('fecha', $this->fecha);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'DNI',
            'Motivo',
            'Lugar',
            'Fecha',
            'Hora Ingreso',
            // Agrega aquí más encabezados si tu tabla tiene más columnas
        ];
    }
}
