<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class RecesosExport implements FromArray, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function array(): array
    {
        // Consulta para obtener los datos de recesos, incluyendo 'exceso'
        $query = DB::table('recesos')
            ->select('nombre', 'dni', 'hora_receso', 'hora_vuelta', 'duracion', 'exceso', 'estado');

        if (!empty($this->filters['busqueda'])) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->filters['busqueda'] . '%')
                  ->orWhere('dni', 'like', '%' . $this->filters['busqueda'] . '%');
            });
        }

        if (!empty($this->filters['desde'])) {
            $query->whereDate('hora_receso', '>=', $this->filters['desde']);
        }

        if (!empty($this->filters['hasta'])) {
            $query->whereDate('hora_receso', '<=', $this->filters['hasta']);
        }

        // Convertimos cada registro a array con `exceso` como cadena
        $recesos = $query->get()->map(function ($receso) {
            return [
                'nombre' => $receso->nombre,
                'dni' => $receso->dni,
                'hora_receso' => $receso->hora_receso,
                'hora_vuelta' => $receso->hora_vuelta,
                'duracion' => $receso->duracion,
                'exceso' => (string) $receso->exceso,  // Convertimos 'exceso' a cadena para asegurar que se exporte
                'estado' => $receso->estado,
            ];
        })->toArray();

        return $recesos;
    }

    public function headings(): array
    {
        return ["Nombre", "DNI", "Hora de Receso", "Hora de Vuelta", "Duración", "Exceso", "Estado"];
    }
}
