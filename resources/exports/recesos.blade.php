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
