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

                @foreach ($fields as $field)
                <div class="search-worker mb-3">
                    <label for="{{ $field->name }}" class="form-label">{{ $field->label }}</label>

                    @if ($field->type == 'text')
                    <input type="text" id="{{ $field->name }}" name="{{ $field->name }}" class="form-control" placeholder="{{ $field->label }}" {{ $field->name == 'dniWorker' ? 'maxlength=8 readonly' : '' }}>
                    @elseif ($field->type == 'select')
                    <select id="{{ $field->name }}" name="{{ $field->name }}" class="form-select">
                        @foreach (json_decode($field->options, true) as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    @elseif ($field->type == 'radio')
                    @foreach (json_decode($field->options, true) as $option)
                    <label><input type="radio" name="{{ $field->name }}" value="{{ $option }}"> {{ $option }}</label>
                    @endforeach
                    @endif

                    <div id="{{ $field->name }}_error" class="text-danger" style="font-size: 12px;"></div>
                </div>
                @endforeach

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
                    <td>
                        <span id="contador-{{ $trabajador->id }}" class="contador contador-verde"></span>
                    </td>
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
    let recesosActivos = {};
    let tiemposRestantes = {};

    function registrarReceso() {
        const id = document.getElementById('worker-id').value;
        const nombre = document.getElementById('worker_name').value;
        const dni = document.getElementById('dniWorker').value;
        const duracion = parseInt(document.getElementById('recesoDuration').value, 10); // Duración en minutos

        if (!id || !nombre || !dni || !duracion) {
            alert("Por favor, complete todos los campos antes de iniciar el receso.");
            return;
        }

        fetch(registrarRecesoUrl, {
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

                    // Crear nueva fila en la tabla
                    const newRow = `
            <tr id="fila_${id}">
                <td>${document.querySelectorAll('#tbody-visitas tr').length + 1}</td>
                <td>${nombre}</td>
                <td>${dni}</td>
                <td>${horaReceso}</td>
                <td>N/A</td>
                <td><span id="contador-${id}" class="contador contador-verde">${duracion}:00</span></td>
                <td><button class="btn btn-danger" onclick="finalizarReceso(${id})">
                    <i class="fas fa-stop"></i> Finalizar
                </button></td>
            </tr>`;
                    document.getElementById('tbody-visitas').insertAdjacentHTML('beforeend', newRow);

                    // Guardar la hora de inicio y duración en `localStorage` en minutos
                    const startTime = Date.now();
                    localStorage.setItem(`inicioReceso_${id}`, startTime);
                    localStorage.setItem(`duracion_${id}`, duracion); // Guarda la duración en minutos

                    // Inicia el contador en minutos para mostrar en la interfaz
                    iniciarContador(id, duracion * 60);
                } else {
                    alert(data.message || "Hubo un error al registrar el receso.");
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function iniciarContador(id, tiempoRestante, enTiempoExtra = false) {
        const contadorElemento = document.getElementById(`contador-${id}`);
        if (!contadorElemento) return;

        clearInterval(recesosActivos[id]); // Reinicia el intervalo si ya existe

        // Usar un intervalo que actualice el contador cada segundo en tiempo real
        recesosActivos[id] = setInterval(() => {
            if (tiempoRestante > 0) {
                const minutos = Math.floor(tiempoRestante / 60);
                const segundos = tiempoRestante % 60;
                contadorElemento.textContent = `${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`;
                tiempoRestante--;
            } else {
                if (!enTiempoExtra) {
                    contadorElemento.classList.replace('contador-verde', 'contador-rojo');
                    enTiempoExtra = true;
                }
                // Mostrar tiempo extra en formato negativo MM:SS
                const minutos = Math.floor(Math.abs(tiempoRestante) / 60);
                const segundos = Math.abs(tiempoRestante) % 60;
                contadorElemento.textContent = `-${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`;
                tiempoRestante--; // Continuar en tiempo extra
            }
        }, 1000); // Actualización en tiempo real cada segundo
    }


    function finalizarReceso(id) {
        const contadorElemento = document.getElementById(`contador-${id}`);

        if (!contadorElemento || contadorElemento.textContent === "00:00") {
            alert("El receso ya ha terminado o no está activo.");
            return;
        }

        fetch(finalizarRecesoUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                    if (fila) fila.cells[4].textContent = horaVuelta;

                    clearInterval(recesosActivos[id]);
                    delete recesosActivos[id];
                    contadorElemento.textContent = "00:00";
                    localStorage.removeItem(`receso_${id}`);
                    setTimeout(() => fila.remove(), 1000);
                } else {
                    alert(data.message || "Hubo un error al finalizar el receso.");
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function iniciarContadores() {
        fetch(tiemposRestantesUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    data.trabajadores.forEach(trabajador => {
                        const id = trabajador.id;
                        const duracionTotalMinutos = trabajador.duracionRestante; // Obtener la duración personalizada del backend

                        // Obtener o establecer el tiempo de inicio en `localStorage`
                        let startTime = localStorage.getItem(`inicioReceso_${id}`);
                        if (!startTime) {
                            startTime = Date.now();
                            localStorage.setItem(`inicioReceso_${id}`, startTime);
                            localStorage.setItem(`duracionTotalMinutos_${id}`, duracionTotalMinutos);
                        } else {
                            startTime = parseInt(startTime, 10);
                        }

                        // Calcular el tiempo transcurrido en segundos desde el inicio
                        const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
                        const duracionTotalSegundos = duracionTotalMinutos * 60;
                        let tiempoRestante = duracionTotalSegundos - elapsedSeconds;

                        // Iniciar el contador en tiempo real con actualización cada segundo
                        iniciarContador(id, tiempoRestante);
                    });
                } else {
                    console.error("Error al obtener los tiempos restantes:", data.message);
                }
            })
            .catch(error => console.error('Error en la solicitud de tiempos restantes:', error));
    }





    function actualizarVistaContador(trabajadorId, tiempoRestante, enTiempoExtra) {
        // Lógica para actualizar el elemento del contador en la página
        const contadorElemento = document.getElementById(`contador_${trabajadorId}`);
        if (contadorElemento) {
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            contadorElemento.textContent = `${minutos}:${segundos < 10 ? '0' : ''}${segundos}`;
        }
    }

    function alternarPausaReanudar(id, boton) {
        const icono = boton.querySelector('i');
        const contadorElemento = document.getElementById(`contador-${id}`);
        const pausado = boton.getAttribute('data-pausado') === 'true';

        if (pausado) {
            iniciarContador(id, tiemposRestantes[id]);
            boton.innerHTML = '<i class="fas fa-pause"></i> Pausar';
            boton.classList.replace('btn-success', 'btn-warning');
            boton.setAttribute('data-pausado', 'false');
        } else {
            clearInterval(recesosActivos[id]);
            delete recesosActivos[id];

            const [minutos, segundos] = contadorElemento.textContent.split(':').map(Number);
            tiemposRestantes[id] = minutos * 60 + segundos;

            boton.innerHTML = '<i class="fas fa-play"></i> Reanudar';
            boton.classList.replace('btn-warning', 'btn-success');
            boton.setAttribute('data-pausado', 'true');
        }
    }
    document.addEventListener("DOMContentLoaded", function() {
        iniciarContadores(); // Cargar los contadores cuando se carga la página
    });

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
    document.getElementById('worker_name').value = nombre; // Campo oculto que guarda el nombre del trabajador
    document.getElementById('dniWorker').value = dni;
    
    // Autocompleta el campo de visualización de nombre del trabajador
    document.getElementById('worker-name-display').textContent = nombre;
    document.getElementById('searchWorker').value = nombre; // Autocompleta el campo de búsqueda con el nombre del trabajador
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
    const registrarRecesoUrl = "{{ route('cronometro.registrar') }}";
    const finalizarRecesoUrl = "{{ route('cronometro.finalizar') }}";
    const tiemposRestantesUrl = "{{ route('cronometro.tiemposRestantes') }}";
</script>