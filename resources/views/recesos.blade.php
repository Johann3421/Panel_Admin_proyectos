@extends('layouts.app')

@section('title', 'Reporte de Recesos')

@section('content')
<div class="container my-4">
    <h1>Reporte de Recesos</h1>

    <div class="mb-3">
        <a href="{{ route('recesos.export') }}" class="btn btn-success">Exportar a Excel</a>
    </div>

    <form method="GET" action="{{ route('recesos.index') }}" class="row mb-4">
        <div class="col-md-3">
            <label for="fecha-desde" class="form-label">Fecha Desde:</label>
            <input type="date" id="fecha-desde" name="desde" class="form-control" value="{{ $fechaDesde }}">
        </div>
        <div class="col-md-3">
            <label for="fecha-hasta" class="form-label">Fecha Hasta:</label>
            <input type="date" id="fecha-hasta" name="hasta" class="form-control" value="{{ $fechaHasta }}">
        </div>
        <div class="col-md-4">
            <label for="busqueda" class="form-label">Buscar por nombre o DNI:</label>
            <input type="text" id="busqueda" name="busqueda" class="form-control" value="{{ $busqueda }}">
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nro.</th>
                    <th>Hora de Receso</th>
                    <th>Trabajador</th>
                    <th>DNI</th>
                    <th>Duración Usada (min)</th>
                    <th>Exceso (min)</th>
                    <th>Hora de Vuelta</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recesos as $index => $receso)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $receso->hora_receso }}</td>
                        <td>{{ $receso->nombre }}</td>
                        <td>{{ $receso->dni }}</td>
                        <td>{{ $receso->duracion }}</td>
                        <td>{{ $receso->exceso ?? 0 }}</td>
                        <td>{{ $receso->hora_vuelta ?? 'Pendiente' }}</td>
                        <td>{{ $receso->estado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $recesos->appends(request()->query())->links() }}
</div>
@endsection
