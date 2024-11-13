<?php
// app/Admin/Controllers/VisitasAdminController.php

namespace App\Admin\Controllers;

use App\Models\Visita;
use Illuminate\Support\Facades\DB;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Widgets\Box;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Layout\Column;
use OpenAdmin\Admin\Layout\Row;

class VisitasAdminController extends AdminController
{
    protected function title()
    {
        return 'Visitas';
    }

    public function index(Content $content)
    {
        // Datos para el gráfico
        $visitasPorMes = DB::table('visitas')
            ->select(DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes"), DB::raw('count(*) as total'))
            ->groupBy('mes')
            ->get();

        $data = [
            'labels' => $visitasPorMes->pluck('mes'),
            'data' => $visitasPorMes->pluck('total'),
        ];

        // Gráfico como Box
        $box = new Box('Estadísticas de Visitas por Mes', view('admin.visitas_estadisticas', compact('data')));

        // Estructura de contenido con columnas
        return $content
            ->title($this->title())
            ->row(function (Row $row) use ($box) {
                $row->column(4, $box);  // Coloca el gráfico en la columna izquierda
                $row->column(8, $this->grid()); // Coloca el grid en la columna derecha
            });
    }

    protected function grid()
    {
        $grid = new Grid(new Visita());

        $grid->column('id', __('ID'));
        $grid->column('fecha', __('Fecha de Visita'));
        $grid->column('nombre', __('Visitante'));
        $grid->column('dni', __('Documento del Visitante'));
        $grid->column('hora_ingreso', __('Hora de Ingreso'));
        $grid->column('hora_salida', __('Hora de Salida'));
        $grid->column('smotivo', __('Motivo'));
        $grid->column('tipopersona', __('Tipo de Persona'));
        $grid->column('lugar', __('Lugar'));

        $grid->filter(function ($filter) {
            $filter->like('nombre', 'Nombre');
            $filter->between('fecha', 'Fecha')->date();
            $filter->like('dni', 'Documento');
            $filter->like('smotivo', 'Motivo');
        });

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Visita());

        $form->text('nombre', 'Nombre');
        $form->date('fecha', 'Fecha de Visita');
        $form->text('dni', 'Documento del Visitante');
        $form->time('hora_ingreso', 'Hora de Ingreso');
        $form->time('hora_salida', 'Hora de Salida');
        $form->textarea('smotivo', 'Motivo');
        $form->text('tipopersona', 'Tipo de Persona');
        $form->text('lugar', 'Lugar');

        return $form;
    }
}
