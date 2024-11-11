<?php

namespace App\Exports;

use App\Models\Receso;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RecesoExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $busqueda;
    protected $fechaDesde;
    protected $fechaHasta;

    public function __construct($busqueda = '', $fechaDesde = '', $fechaHasta = '')
    {
        $this->busqueda = $busqueda;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
    }

    public function query()
    {
        $query = Receso::query();

        if ($this->busqueda) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('dni', 'like', '%' . $this->busqueda . '%');
            });
        }

        if ($this->fechaDesde && $this->fechaHasta) {
            $query->whereBetween('hora_receso', [$this->fechaDesde, $this->fechaHasta]);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'DNI',
            'Hora Receso',
            // Añade otros encabezados que correspondan a las columnas
        ];
    }
}
