@extends('layouts.app')

@section('title', 'Reporte de Salidas')

@section('content')
<div class="container-fluid my-4">
    <h1>Reporte de Salidas</h1>
    
    <!-- Botón para exportar a Excel -->
    <div class="mb-3">
        <a href="{{ route('exportar.excel') }}" class="btn btn-success">Exportar a Excel</a>
    </div>

    <!-- Formulario de búsqueda y filtrado -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reporte.index') }}">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="fecha-desde" class="form-label">Fecha Desde:</label>
                        <input type="date" name="desde" id="fecha-desde" class="form-control" value="{{ $fechaDesde }}">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha-hasta" class="form-label">Fecha Hasta:</label>
                        <input type="date" name="hasta" id="fecha-hasta" class="form-control" value="{{ $fechaHasta }}">
                    </div>
                    <div class="col-md-4">
                        <label for="busqueda" class="form-label">Buscar:</label>
                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, DNI, motivo o lugar" value="{{ $busqueda }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            </form>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Nro.</th>
                    <th>Fecha de visita</th>
                    <th>Visitante</th>
                    <th>Documento</th>
                    <th>Hora Ingreso</th>
                    <th>Hora Salida</th>
                    <th>Motivo</th>
                    <th>Lugar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitas as $index => $visita)
                <tr>
                    <td>{{ $index + 1 + ($visitas->currentPage() - 1) * $visitas->perPage() }}</td>
                    <td>{{ $visita->fecha }}</td>
                    <td>{{ $visita->nombre }}</td>
                    <td>{{ $visita->dni }}</td>
                    <td>{{ $visita->hora_ingreso }}</td>
                    <td>{{ $visita->hora_salida }}</td>
                    <td>{{ $visita->smotivo }}</td>
                    <td>{{ $visita->lugar }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No se encontraron resultados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $visitas->appends(request()->query())->links() }}
    </div>
</div>
@endsection
