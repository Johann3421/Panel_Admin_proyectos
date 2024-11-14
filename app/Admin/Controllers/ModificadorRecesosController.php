<?php

namespace App\Admin\Controllers;

use App\Models\RecesoField;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Layout\Content;

class ModificadorRecesosController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->title('Modificador de Recesos')
            ->description('Administra los campos del formulario de recesos')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new RecesoField());

        $grid->column('id', __('ID'));
        $grid->column('label', __('Etiqueta'));
        $grid->column('name', __('Nombre del Campo'));
        $grid->column('type', __('Tipo de Campo'));

        $grid->column('options', __('Opciones'))->display(function ($options) {
            $optionsArray = json_decode($options, true);
            return is_array($optionsArray) ? implode(', ', array_slice($optionsArray, 0, 3)) . (count($optionsArray) > 3 ? '...' : '') : $options;
        });

        $grid->column('required', __('Requerido'))->display(fn($required) => $required ? 'Sí' : 'No');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });

        return $grid;
    }

    public function create(Content $content)
    {
        return $content->title('Crear Nuevo Campo')->body($this->form());
    }

    public function edit($id, Content $content)
    {
        return $content->title('Editar Campo')->body($this->form()->edit($id));
    }

    protected function form()
    {
        $form = new Form(new RecesoField());

        $form->text('label', 'Etiqueta')->required();
        $form->text('name', 'Nombre del Campo')->required();
        $form->select('type', 'Tipo de Campo')->options([
            'text' => 'Texto',
            'textarea' => 'Área de Texto',
            'select' => 'Seleccionable',
            'radio' => 'Radio',
        ])->required();

        $form->textarea('options', 'Opciones (JSON)')->help('Solo para select o radio (formato JSON)')->rules('nullable|json');
        $form->switch('required', 'Requerido');

        return $form;
    }

    public function destroy($id)
    {
        RecesoField::destroy($id);
        return response()->json(['status' => 'success', 'message' => 'Campo eliminado correctamente']);
    }
}
