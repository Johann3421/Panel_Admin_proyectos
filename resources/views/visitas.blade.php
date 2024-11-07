@extends('layouts.app')

@section('title', 'Registro de Visitas')

@section('content')
<div class="container-fluid my-4">
    <h1>Registro de Visitas</h1>

    <!-- Formulario de Registro de Visita -->
    <form id="frmvisita" method="POST" action="{{ route('visitas.store') }}" onsubmit="return validarFormulario();">
        @csrf
        <div class="row">
            <!-- Datos de visitante -->
            <div class="col-sm-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos de visitante</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <label for="dni">DNI:</label>
                                <input type="text" maxlength="8" class="form-control form-control-sm" name="dni" id="dni" placeholder="Nro Documento" onkeypress="return esNumerico(event)" onblur="buscarPorDNI()">
                                <div id="dni_error" class="text-danger" style="font-size: 12px;"></div>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-12">
                                <label for="nombre">Nombres y Apellidos:</label>
                                <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" placeholder="Nombres y Apellidos">
                                <div id="nombre_error" class="text-danger" style="font-size: 12px;"></div>
                            </div>
                            <div class="col-lg-12">
                                <label for="tipopersona">Tipo:</label>
                                <div class="form-group">
                                    <label><input type="radio" id="personaNatural" name="tipopersona" value="Persona Natural"> Persona Natural</label>
                                    <label><input type="radio" id="entidadPublica" name="tipopersona" value="Entidad Publica"> Entidad Publica</label>
                                    <label><input type="radio" id="entidadPrivada" name="tipopersona" value="Entidad Privada"> Entidad Privada</label>
                                </div>
                                <div id="tipopersona_error" class="text-danger" style="font-size: 12px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Oficina a visitar -->
            <div class="col-sm-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Oficina a visitar</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nomoficina">Oficina:</label>
                            <select id="nomoficina" name="nomoficina" class="form-control form-control-sm">
                                <option value="SELECCIONE" selected>&lt;&lt; SELECCIONE &gt;&gt;</option>
                                <option value="ABASTECIMIENTO">ABASTECIMIENTO</option>
                                <option value="ALMACEN">ALMACEN</option>
                                <option value="ARCHIVO">ARCHIVO</option>
                                <!-- Agregar más opciones según sea necesario -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="smotivo">Motivo de visita:</label>
                            <div id="motivo-buttons" class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Reunion de trabajo')">Reunión de trabajo</button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Provision de servicios')">Provisión de servicios</button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Gestion de intereses')">Gestión de intereses</button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Motivo personal')">Motivo personal</button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Tramite documentario')">Trámite documentario</button>
                                <button type="button" class="btn btn-outline-primary" onclick="selectMotivo(this, 'Otros')">Otros</button>
                            </div>
                            <input type="hidden" id="smotivo" name="smotivo">
                            <div id="motivo_error" class="text-danger" style="font-size: 12px;"></div>
                        </div>
                        <div class="form-group">
                            <label for="lugar">Lugar:</label>
                            <input type="text" class="form-control form-control-sm" id="lugar" name="lugar" placeholder="ID de la Oficina">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right">
                            <i class="fa fa-upload"></i> Registrar visita
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Lista de Visitas -->
    <div class="card card-info mt-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-table"></i> LISTA DE VISITAS</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('visitas.index') }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, DNI, motivo o lugar" value="{{ request('busqueda') }}">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Acción</th>
                            <th>Nro.</th>
                            <th>Fecha de visita</th>
                            <th>Visitante</th>
                            <th>Entidad del visitante</th>
                            <th>Documento del visitante</th>
                            <th>Hora Ingreso</th>
                            <th>Hora Salida</th>
                            <th>Motivo</th>
                            <th>Lugar Específico</th>
                            <th>Imprimir Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visitas as $index => $visita)
                        <tr>
                            <td><button class="btn btn-primary" onclick="registrarSalida({{ $visita->id }})"><i class="material-icons">exit_to_app</i></button></td>
                            <td>{{ $index + 1 + ($visitas->currentPage() - 1) * $visitas->perPage() }}</td>
                            <td>{{ $visita->fecha }}</td>
                            <td>{{ $visita->nombre }}</td>
                            <td>{{ $visita->tipopersona }}</td>
                            <td>{{ $visita->dni }}</td>
                            <td>{{ $visita->hora_ingreso }}</td>
                            <td>{{ $visita->hora_salida }}</td>
                            <td>{{ $visita->smotivo }}</td>
                            <td>{{ $visita->lugar }}</td>
                            <td><button class="btn btn-success" onclick="imprimirTicket({{ $visita->id }})"><i class="material-icons">print</i></button></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11">No hay datos disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $visitas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
