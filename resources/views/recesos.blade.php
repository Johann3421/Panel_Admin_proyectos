@extends('layouts.app')

@section('title', 'Reporte de Recesos')

@section('content')
<div class="container-fluid my-4">
    <h1>Reporte de Recesos</h1>

    <!-- Botón para exportar a Excel -->
    <div class="mb-3">
        <a href="{{ route('recesos.export', ['busqueda' => request('busqueda'), 'desde' => request('desde'), 'hasta' => request('hasta')]) }}" class="btn btn-success">
            Exportar a Excel
        </a>
        <a href="{{ route('recesos.vaciar') }}" class="btn btn-danger">
            Vaciar
        </a>
        <a href="{{ route('recesos.restaurar') }}" class="btn btn-warning">
            Restaurar
        </a>
    </div>
    

    <!-- Formulario de búsqueda y filtrado -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('recesos.index') }}" id="filtro-form">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="fecha-desde" class="form-label">Fecha Desde:</label>
                        <input type="date" name="desde" id="fecha-desde" class="form-control" value="{{ request('desde') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha-hasta" class="form-label">Fecha Hasta:</label>
                        <input type="date" name="hasta" id="fecha-hasta" class="form-control" value="{{ request('hasta') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="busqueda" class="form-label">Buscar:</label>
                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre o DNI" value="{{ request('busqueda') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Buscar</button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">Limpiar</button>
            </form>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Nro.</th>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Hora de Receso</th>
                    <th>Hora de Vuelta</th>
                    <th>Duración</th>
                    <th>Exceso</th> <!-- Nueva columna para 'exceso' -->
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recesos as $index => $receso)
                <tr>
                    <td>{{ $index + 1 + ($recesos->currentPage() - 1) * $recesos->perPage() }}</td>
                    <td>{{ $receso->nombre }}</td>
                    <td>{{ $receso->dni }}</td>
                    <td>{{ $receso->hora_receso }}</td>
                    <td>{{ $receso->hora_vuelta }}</td>
                    <td>{{ $receso->duracion }}</td>
                    <td>{{ $receso->exceso }} min</td> <!-- Mostrar el campo 'exceso' -->
                    <td>{{ $receso->estado }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No se encontraron resultados.</td> <!-- Ajustado a 8 columnas -->
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación Simple sin Íconos -->
<div class="d-flex justify-content-center">
    <div class="pagination">
        @if ($recesos->onFirstPage())
        <span class="btn btn-secondary disabled">Anterior</span>
        @else
        <a href="{{ $recesos->previousPageUrl() }}" class="btn btn-primary">Anterior</a>
        @endif

        <span class="mx-2">Página {{ $recesos->currentPage() }} de {{ $recesos->lastPage() }}</span>

        @if ($recesos->hasMorePages())
        <a href="{{ $recesos->nextPageUrl() }}" class="btn btn-primary">Siguiente</a>
        @else
        <span class="btn btn-secondary disabled">Siguiente</span>
        @endif
    </div>
</div>
</div>

<script>
    function limpiarFiltros() {
        window.location.href = "{{ route('recesos.index') }}";
    }
</script>
@endsection