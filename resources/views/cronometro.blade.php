@extends('layouts.app')

@section('title', 'Control de Receso de Trabajadores')

@section('content')
@vite(['resources/css/style.css'])

<section class="content">
    <div class="row">
        <div class="left-section">
            <div class="search-worker mb-3">
                <label for="searchWorker" class="form-label">Buscar Trabajador:</label>
                <input type="text" id="searchWorker" class="form-control" placeholder="Ingrese el nombre del trabajador" onkeyup="buscarTrabajador()">
                <div id="searchResult" class="mt-2"></div>
            </div>

            <input type="hidden" id="worker-id">
            <input type="hidden" id="worker-name">
            <input type="hidden" id="worker-dni">

            <div id="main-worker" class="worker-box">
                <h4 id="worker-name-display">Nombre del Trabajador</h4>
                <div class="search-worker mb-3">
                    <label for="dniWorker" class="form-label">DNI:</label>
                    <input type="text" id="dniWorker" class="form-control" placeholder="Ingrese el DNI" maxlength="8" readonly>
                </div>
                <div class="mb-3">
                    <label for="recesoDuration" class="form-label">Duración del Receso:</label>
                    <select id="recesoDuration" class="form-select">
                        <option value="15">15 minutos</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button class="btn btn-success" onclick="registrarReceso()">Generar Ticket</button>
                </div>
            </div>
        </div>

        <div class="right-section">
            <div class="clock-container">
                <div class="digital-clock">
                    <div class="time">
                        <span class="hour">00</span> :
                        <span class="minute">00</span> :
                        <span class="second">00</span>
                        <span class="ampm">AM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="abajo">
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
            <tbody id="tbody-visitas" onclick="manejarClickBoton(event)">
                @forelse ($trabajadores as $index => $trabajador)
                <tr id="fila_{{ $trabajador->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trabajador->nombre }}</td>
                    <td>{{ $trabajador->dni }}</td>
                    <td>{{ $trabajador->hora_receso }}</td>
                    <td>{{ $trabajador->hora_vuelta ?? 'N/A' }}</td>
                    <td><span id="contador-{{ $trabajador->id }}" class="contador contador-verde"></span></td>
                    <td>
                        <button class="btn btn-danger" onclick="finalizarReceso({{ $trabajador->id }})">
                            <i class="fas fa-stop"></i> Finalizar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No hay datos disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

<script>
    // resources/js/script.js

    let recesosActivos = {};
    let tiemposRestantes = {};

    function registrarReceso() {
        const id = document.getElementById('worker-id').value;
        const nombre = document.getElementById('worker-name').value;
        const dni = document.getElementById('dniWorker').value;
        const duracion = document.getElementById('recesoDuration').value;

        if (!id || !nombre || !dni || !duracion) {
            alert("Por favor, complete todos los campos antes de iniciar el receso.");
            return;
        }

        fetch(route('cronometro.registrar'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new URLSearchParams({
                    id,
                    duracion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const horaReceso = data.hora_receso;
                    const newRow = `
                <tr id="fila_${id}">
                    <td>${document.querySelectorAll('#tbody-visitas tr').length + 1}</td>
                    <td>${nombre}</td>
                    <td>${dni}</td>
                    <td>${horaReceso}</td>
                    <td>N/A</td>
                    <td><span id="contador-${id}" class="contador contador-verde">${duracion}:00</span></td>
                    <td><button class="btn btn-danger" onclick="finalizarReceso(${id})"><i class="fas fa-stop"></i> Finalizar</button></td>
                </tr>`;
                    document.getElementById('tbody-visitas').insertAdjacentHTML('beforeend', newRow);
                    iniciarContador(id, duracion * 60);
                } else {
                    alert(data.message || "Hubo un error al registrar el receso.");
                }
            })
            .catch(error => console.error('Error:', error));
    }


    function finalizarReceso(id) {
        const contadorElemento = document.getElementById(`contador-${id}`);

        if (!contadorElemento || contadorElemento.textContent === "00:00") {
            alert("El receso ya ha terminado o no está activo.");
            return;
        }

        fetch("{{ route('cronometro.finalizar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const horaVuelta = data.hora_vuelta;
                    const fila = document.getElementById(`fila_${id}`);
                    if (fila) {
                        fila.cells[4].textContent = horaVuelta;
                    }
                    clearInterval(recesosActivos[id]);
                    delete recesosActivos[id];
                    contadorElemento.textContent = "00:00";
                } else {
                    alert(data.message || "Hubo un error al finalizar el receso.");
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function iniciarContadores() {
        fetch("{{ route('cronometro.tiemposRestantes') }}")
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    data.trabajadores.forEach(trabajador => {
                        iniciarContador(trabajador.id, trabajador.tiempo_restante, trabajador.en_tiempo_extra);
                    });
                } else {
                    console.error("Error al obtener los tiempos restantes:", data.message);
                }
            })
            .catch(error => console.error('Error en la solicitud de tiempos restantes:', error));
    }

    function buscarTrabajador() {
        const query = document.getElementById('searchWorker').value;

        if (query.length > 2) {
            fetch(`{{ route('cronometro.buscarTrabajador') }}?busqueda=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const searchResultDiv = document.getElementById('searchResult');
                    searchResultDiv.innerHTML = ''; // Limpiar resultados previos

                    if (data.length > 0) {
                        data.forEach(trabajador => {
                            const trabajadorDiv = document.createElement('div');
                            trabajadorDiv.textContent = `${trabajador.nombre} (${trabajador.dni})`;
                            trabajadorDiv.onclick = () => seleccionarTrabajador(trabajador.id, trabajador.nombre, trabajador.dni);
                            searchResultDiv.appendChild(trabajadorDiv);
                        });
                    } else {
                        searchResultDiv.innerHTML = 'No se encontraron resultados.';
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            document.getElementById('searchResult').innerHTML = '';
        }
    }

    function seleccionarTrabajador(id, nombre, dni) {
        document.getElementById('worker-id').value = id;
        document.getElementById('worker-name').value = nombre;
        document.getElementById('dniWorker').value = dni;
        document.getElementById('worker-name-display').textContent = nombre;
        document.getElementById('searchWorker').value = nombre;
        document.getElementById('searchResult').innerHTML = ''; // Limpiar resultados de búsqueda
    }

    function actualizarRelojDigital() {
        const reloj = document.querySelector('.digital-clock .time');
        const ahora = new Date();
        reloj.querySelector('.hour').textContent = String(ahora.getHours()).padStart(2, '0');
        reloj.querySelector('.minute').textContent = String(ahora.getMinutes()).padStart(2, '0');
        reloj.querySelector('.second').textContent = String(ahora.getSeconds()).padStart(2, '0');
        reloj.querySelector('.ampm').textContent = ahora.getHours() >= 12 ? 'PM' : 'AM';
    }

    setInterval(actualizarRelojDigital, 1000);
</script>