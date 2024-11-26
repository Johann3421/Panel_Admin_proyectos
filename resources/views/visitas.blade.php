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
                        @foreach ($fields as $field)
                            @if (in_array($field->name, ['dni', 'nombre', 'tipopersona']))
                                <div class="col-lg-{{ $field->name == 'dni' ? '3' : '9' }} col-md-6 mb-3">
                                    <label for="{{ $field->name }}">{{ $field->label }} @if($field->required) * @endif</label>
    
                                    @if ($field->type == 'text')
                                        <input type="text" 
                                               name="{{ $field->name }}" 
                                               id="{{ $field->name }}" 
                                               class="form-control form-control-sm" 
                                               placeholder="{{ $field->label }}" 
                                               {{ $field->name == 'nombre' ? 'readonly' : '' }}
                                               {{ $field->name == 'dni' ? 'maxlength=8 onkeypress=esNumerico(event) onkeydown=noSubmitEnter(event) onblur=buscarPorDNI()' : '' }}>
                                    @elseif ($field->type == 'radio')
                                        <div class="form-group">
                                            @foreach (json_decode($field->options, true) as $option)
                                                <label class="d-inline-block mr-3">
                                                    <input type="radio" 
                                                           name="{{ $field->name }}" 
                                                           value="{{ $option }}" 
                                                           class="mr-1"> {{ $option }}
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
    
                                    <div id="{{ $field->name }}_error" class="text-danger" style="font-size: 12px;"></div>
                                </div>
                            @endif
                        @endforeach
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
                    @foreach ($fields as $field)
                        @if (in_array($field->name, ['nomoficina', 'smotivo', 'lugar']))
                            <div class="form-group">
                                <label for="{{ $field->name }}">{{ $field->label }} @if($field->required) * @endif</label>
    
                                @if ($field->type == 'select')
                                    <select id="{{ $field->name }}" 
                                            name="{{ $field->name }}" 
                                            class="form-control form-control-sm"
                                            {{ $field->name == 'nomoficina' ? 'onchange=autocompletarLugar()' : '' }}>
                                        @foreach (json_decode($field->options, true) as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field->type == 'radio')
                                    <div class="form-group">
                                        @foreach (json_decode($field->options, true) as $option)
                                            <label class="d-inline-block mr-3">
                                                <input type="radio" 
                                                       name="{{ $field->name }}" 
                                                       value="{{ $option }}" 
                                                       class="mr-1"> {{ $option }}
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif ($field->type == 'text')
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           id="{{ $field->name }}" 
                                           name="{{ $field->name }}" 
                                           placeholder="{{ $field->label }}">
                                @endif
    
                                <div id="{{ $field->name }}_error" class="text-danger" style="font-size: 12px;"></div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Campos adicionales (debajo del segundo div) -->
                    <div id="campos-dinamicos">
                        @foreach ($fields as $field)
                            @if (!in_array($field->name, ['dni', 'nombre', 'tipopersona', 'nomoficina', 'smotivo', 'lugar']))
                                <div class="form-group">
                                    <label for="{{ $field->name }}">{{ $field->label }} @if($field->required) * @endif</label>
                                    @if ($field->type == 'text')
                                        <input type="text" class="form-control" id="{{ $field->name }}" name="{{ $field->name }}">
                                    @elseif ($field->type == 'select')
                                        <select id="{{ $field->name }}" name="{{ $field->name }}" class="form-control">
                                            @foreach (json_decode($field->options, true) as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($field->type == 'radio')
                                        <div>
                                            @foreach (json_decode($field->options, true) as $option)
                                                <label>
                                                    <input type="radio" name="{{ $field->name }}" value="{{ $option }}">
                                                    {{ $option }}
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div id="{{ $field->name }}_error" class="text-danger"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right">
                            <i class="fa fa-upload"></i> Registrar visita
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
    

    <!-- Lista de Visitas con Buscador -->
    <div class="card card-info mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-table"></i> LISTA DE VISITAS</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <!-- Formulario de Búsqueda -->
                <form method="GET" action="{{ route('visitas.index') }}" class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, DNI, motivo o lugar" value="{{ request('busqueda') }}">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
                <div class="col-md-6 text-right">
                    <!-- Selector de cantidad de resultados por página -->
                    <label>Mostrar
                        <select name="tblvisita_length" aria-controls="tblvisita" class="custom-select custom-select-sm form-control form-control-sm" onchange="window.location.href=this.value;">
                            <option value="{{ route('visitas.index', array_merge(request()->all(), ['limite' => 10])) }}" {{ request('limite') == 10 ? 'selected' : '' }}>10</option>
                            <option value="{{ route('visitas.index', array_merge(request()->all(), ['limite' => 25])) }}" {{ request('limite') == 25 ? 'selected' : '' }}>25</option>
                            <option value="{{ route('visitas.index', array_merge(request()->all(), ['limite' => 50])) }}" {{ request('limite') == 50 ? 'selected' : '' }}>50</option>
                            <option value="{{ route('visitas.index', array_merge(request()->all(), ['limite' => -1])) }}" {{ request('limite') == -1 ? 'selected' : '' }}>Todo</option>
                        </select> entradas
                    </label>
                </div>
            </div>

            <!-- Tabla de Visitas -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabla-visitas">
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
                    <tbody id="tbody-visitas">
                        @forelse ($visitas as $index => $visita)
                        <tr id="fila_{{ $visita->id }}">
                            <td>
                                <button class="btn btn-primary" onclick="registrarSalida({{ $visita->id }})">
                                    <i class="material-icons">exit_to_app</i>
                                </button>
                            </td>
                            <td>{{ $index + 1 + ($visitas->currentPage() - 1) * $visitas->perPage() }}</td>
                            <td>{{ $visita->fecha }}</td>
                            <td>{{ $visita->nombre }}</td>
                            <td>{{ $visita->tipopersona }}</td>
                            <td>{{ $visita->dni }}</td>
                            <td>{{ $visita->hora_ingreso }}</td>
                            <td>{{ $visita->hora_salida ?? 'N/A' }}</td>
                            <td>{{ $visita->smotivo }}</td>
                            <td>{{ $visita->lugar }}</td>
                            <td>
                                <button class="btn btn-success" onclick="imprimirTicket({{ $visita->id }})">
                                    <i class="material-icons">print</i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">No hay datos disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="chat-widget" style="display: none; position: fixed; bottom: 60px; right: 20px; width: 300px; height: 400px; border: 1px solid #ccc; background: white;">
                    <iframe src="/botman/tinker" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $visitas->links() }}
            </div>
        </div>
    </div>

    <!-- Importar script.js usando Vite -->
    @vite('resources/js/script.js')
</div>
@endsection

<script>
    // Buscar información de DNI con XMLHttpRequest
    function buscarPorDNI() {
        const dni = document.getElementById("dni").value;

        if (dni.length === 8) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "/buscar-dni", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById("nombre").value = response.nombre;
                        document.getElementById("dni_error").innerHTML = "";
                    } else {
                        document.getElementById("dni_error").innerHTML = response.error;
                        document.getElementById("nombre").value = "";
                    }
                } else if (xhr.readyState === 4) {
                    document.getElementById("dni_error").innerHTML = "Error en la solicitud.";
                }
            };

            xhr.send("dni=" + dni);
        }
    }

    // Validar el formulario antes de enviarlo
    function validarFormulario() {
        const dni = document.getElementById("ndocu").value;
        const nombre = document.getElementById("nombre").value;

        document.getElementById("dni_error").innerHTML = "";
        document.getElementById("nombre_error").innerHTML = "";

        if (dni === "" || dni.length !== 8) {
            document.getElementById("dni_error").innerHTML = "El DNI debe tener 8 dígitos.";
            return false;
        }

        if (nombre === "") {
            document.getElementById("nombre_error").innerHTML = "El nombre es obligatorio.";
            return false;
        }

        return true;
    }

    // Autocompletar el campo "Lugar" basado en la selección de "nomoficina"
    function autocompletarLugar() {
        const selectOficina = document.getElementById("nomoficina");
        const inputLugar = document.getElementById("lugar");

        // Si no existen los elementos, terminar la ejecución
        if (!selectOficina || !inputLugar) return;

        // Asignar el mismo valor de oficina al campo "Lugar"
        const oficinaSeleccionada = selectOficina.value;
        inputLugar.value = oficinaSeleccionada !== "SELECCIONE" ? oficinaSeleccionada : '';
    }



    // Mantener el botón seleccionado para "Motivo de visita"
    function selectMotivo(button, motivo) {
        document.getElementById("smotivo").value = motivo;

        const buttons = document.querySelectorAll("#motivo-buttons .btn");
        buttons.forEach(btn => btn.classList.remove("active"));
        button.classList.add("active");
    }

    // Registrar visita y actualizar la tabla sin recargar la página
    async function registrarVisita(event) {
        event.preventDefault();
        const formData = new FormData(document.getElementById("frmvisita"));

        try {
            const response = await fetch("{{ route('visitas.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            if (response.ok) {
                const visita = await response.json();
                agregarVisitaATabla(visita);
                document.getElementById("frmvisita").reset();
            } else {
                console.error("Error al registrar la visita:", response.statusText);
            }
        } catch (error) {
            console.error("Error en la solicitud:", error);
        }
    }

    // Registrar la salida sin recargar la página
    async function registrarSalida(id) {
        try {
            const response = await fetch(`/visitas/${id}/salida`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const visita = await response.json();
                const fila = document.getElementById(`fila_${id}`);
                fila.querySelector("td:nth-child(8)").textContent = visita.hora_salida || 'N/A';
                // Oculta la fila para simular eliminación visual
                fila.style.display = 'none';
            } else {
                console.error("Error al registrar la salida:", response.statusText);
            }
        } catch (error) {
            console.error("Error en la solicitud:", error);
        }
    }

    // Agregar una visita a la tabla
    function agregarVisitaATabla(visita) {
        const tbody = document.getElementById("tbody-visitas");
        const row = document.createElement("tr");
        row.setAttribute("id", `fila_${visita.id}`);

        row.innerHTML = `
            <td><button class="btn btn-primary" onclick="registrarSalida(${visita.id})"><i class="material-icons">exit_to_app</i></button></td>
            <td>${visita.id}</td>
            <td>${visita.fecha}</td>
            <td>${visita.nombre}</td>
            <td>${visita.tipopersona}</td>
            <td>${visita.dni}</td>
            <td>${visita.hora_ingreso}</td>
            <td>${visita.hora_salida || 'N/A'}</td>
            <td>${visita.smotivo}</td>
            <td>${visita.lugar}</td>
            <td>
    <button class="btn btn-success" onclick="imprimirTicket(${visita.id})">
        <i class="material-icons">print</i>
    </button>
</td>
        `;

        tbody.prepend(row);
    }

    function noSubmitEnter(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Evita el envío del formulario
            return false;
        }
        return true;
    }
    function toggleChat() {
        var chatWidget = document.getElementById('chat-widget');
        chatWidget.style.display = chatWidget.style.display === 'none' ? 'block' : 'none';
    }
</script>

<script type="text/javascript">
    function imprimirTicket(id) {
        // Crear un iframe oculto para cargar el PDF sin abrir una pestaña visible
        let iframe = document.getElementById('ticketIframe');

        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'ticketIframe';
            iframe.style.display = 'none'; // Oculta el iframe
            document.body.appendChild(iframe);
        }

        // Cargar el PDF en el iframe
        iframe.src = `/visitas/${id}/imprimir-ticket`;

        // Abrir el cuadro de impresión después de cargar el PDF
        iframe.onload = function() {
            iframe.contentWindow.print();
        };
    }
</script>