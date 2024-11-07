@extends('layouts.app')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Receso de Trabajadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/style.css', 'resources/js/script.js'])
</head>

<body onload="iniciarContadores()" class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Contenido principal -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <h1>Control de Receso de Trabajadores</h1>
                    
                    <!-- Tabla de trabajadores en receso -->
                    <div class="table-responsive">
                        <table id="tblvisita" class="table display table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nro.</th>
                                    <th>Trabajador</th>
                                    <th>Documento</th>
                                    <th>Hora de Receso</th>
                                    <th>Hora de Vuelta</th>
                                    <th>Duración</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trabajadores as $index => $trabajador)
                                    <tr id="fila_{{ $trabajador->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $trabajador->nombre }}</td>
                                        <td>{{ $trabajador->dni }}</td>
                                        <td>{{ $trabajador->hora_receso }}</td>
                                        <td>{{ $trabajador->hora_vuelta ?? 'N/A' }}</td>
                                        <td>
                                            <span id="contador-{{ $trabajador->id }}" class="contador contador-verde"></span>
                                        </td>
                                        <td>
                                            <form action="{{ route('cronometro.finalizar', $trabajador->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-stop"></i> Finalizar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">No hay datos disponibles</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
