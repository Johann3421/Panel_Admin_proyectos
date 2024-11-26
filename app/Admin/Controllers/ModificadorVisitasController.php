<?php
namespace App\Admin\Controllers;

use App\Models\VisitaField;
use Illuminate\Http\Request;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Layout\Content;

class ModificadorVisitasController extends AdminController
{
    // Muestra la lista de campos
    public function index(Content $content)
    {
        return $content
            ->title('Modificador de Visitas')
            ->description('Administra los campos del formulario de visitas')
            ->body($this->grid());
    }

    // Configura el grid para mostrar los registros
    protected function grid()
{
    $grid = new Grid(new VisitaField());

    $grid->column('id', __('ID'));
    $grid->column('label', __('Etiqueta'));
    $grid->column('name', __('Nombre del Campo'));
    $grid->column('type', __('Tipo de Campo'));

    // Verifica si options es un array antes de hacer implode
    $grid->column('options', __('Opciones'))->display(function ($options) {
        $optionsArray = json_decode($options, true);
    
        // Si no es un array, devuelve el valor original
        if (!is_array($optionsArray)) {
            return $options;
        }
    
        // Limita a las primeras 3 opciones
        $displayOptions = array_slice($optionsArray, 0, 3);
        $result = implode(', ', $displayOptions);
    
        // Agrega '...' si hay más opciones
        if (count($optionsArray) > 3) {
            $result .= '...';
        }
    
        return $result;
    });
    

    $grid->column('required', __('Requerido'))->display(function ($required) {
        return $required ? 'Sí' : 'No';
    });

    $grid->actions(function ($actions) {
        $actions->disableView();
        $actions->disableDelete();
    });

    return $grid;
}


    // Muestra el formulario para crear un nuevo campo
    public function create(Content $content)
    {
        return $content
            ->title('Crear Nuevo Campo')
            ->body($this->form());
    }

    // Muestra el formulario para editar un campo existente
    public function edit($id, Content $content)
    {
        return $content
            ->title('Editar Campo')
            ->body($this->form()->edit($id));
    }

    // Configura el formulario de creación y edición
    protected function form()
    {
        $form = new Form(new VisitaField());

        $form->text('label', 'Etiqueta')->required();
        $form->text('name', 'Nombre del Campo')->required();
        $form->select('type', 'Tipo de Campo')->options([
            'text' => 'Texto',
            'textarea' => 'Área de Texto',
            'select' => 'Seleccionable',
            'radio' => 'Radio',
        ])->required();

        $form->textarea('options', 'Opciones (JSON)')
             ->help('Solo para select o radio (formato JSON)')
             ->rules('nullable|json');

        $form->switch('required', 'Requerido');

        return $form;
    }

    // Elimina un campo de la base de datos
    public function destroy($id)
    {
        VisitaField::destroy($id);
        return response()->json(['status' => 'success', 'message' => 'Campo eliminado correctamente']);
    }
    
}
