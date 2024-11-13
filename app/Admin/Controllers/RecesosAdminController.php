<?php
// app/Admin/Controllers/RecesosAdminController.php

namespace App\Admin\Controllers;

use App\Models\Receso;
use Illuminate\Support\Facades\DB;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Widgets\Box;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Layout\Column;
use OpenAdmin\Admin\Layout\Row;

class RecesosAdminController extends AdminController
{
    protected function title()
    {
        return 'Recesos';
    }

    public function index(Content $content)
    {
        // Datos para el cuadro estadístico: recuento de recesos por mes
        $recesosPorMes = DB::table('recesos')
            ->select(DB::raw("DATE_FORMAT(hora_receso, '%Y-%m') as mes"), DB::raw('count(*) as total'))
            ->groupBy('mes')
            ->get();

        $data = [
            'labels' => $recesosPorMes->pluck('mes'),
            'data' => $recesosPorMes->pluck('total'),
        ];

        // Cuadro estadístico como Box
        $box = new Box('Estadísticas de Recesos por Mes', view('admin.recesos_estadisticas', compact('data')));

        // Estructura de contenido con columnas
        return $content
            ->title($this->title())
            ->row(function (Row $row) use ($box) {
                $row->column(4, $box);  // Coloca el cuadro estadístico en la columna izquierda
                $row->column(8, $this->grid()); // Coloca el grid en la columna derecha
            });
    }

    protected function grid()
    {
        $grid = new Grid(new Receso());

        // Añadir todas las columnas de la tabla `recesos`
        $grid->column('id', __('ID'));
        $grid->column('trabajador_id', __('ID Trabajador'));
        $grid->column('nombre', __('Nombre'));
        $grid->column('dni', __('DNI'));
        $grid->column('hora_receso', __('Hora Receso'));
        $grid->column('hora_vuelta', __('Hora Vuelta'));
        $grid->column('duracion', __('Duración'));
        $grid->column('estado', __('Estado'));
        $grid->column('exceso', __('Exceso'));

        // Filtros de búsqueda
        $grid->filter(function ($filter) {
            $filter->like('nombre', 'Nombre');
            $filter->like('dni', 'DNI');
            $filter->like('motivo', 'Motivo');
            $filter->between('fecha', 'Fecha')->date();
        });

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Receso());

        // Campos en el formulario de edición
        $form->number('trabajador_id', 'ID Trabajador');
        $form->text('nombre', 'Nombre');
        $form->text('dni', 'DNI');
        $form->text('motivo', 'Motivo');
        $form->time('hora_receso', 'Hora Receso');
        $form->time('hora_vuelta', 'Hora Vuelta');
        $form->number('duracion', 'Duración');
        $form->select('estado', 'Estado')->options([
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
        ]);
        $form->text('exceso', 'Exceso');

        return $form;
    }
}
