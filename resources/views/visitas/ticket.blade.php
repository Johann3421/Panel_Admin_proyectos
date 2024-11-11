<!-- resources/views/visitas/ticket.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Ticket de Visita</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Ticket de Visita</h2>
            <p>ID de Visita: {{ $visita->id }}</p>
        </div>

        <div class="info"><strong>Fecha de visita:</strong> {{ $visita->fecha }}</div>
        <div class="info"><strong>Visitante:</strong> {{ $visita->nombre }}</div>
        <div class="info"><strong>Documento del visitante:</strong> {{ $visita->dni }}</div>
        <div class="info"><strong>Hora de Ingreso:</strong> {{ $visita->hora_ingreso }}</div>
        <div class="info"><strong>Hora de Salida:</strong> {{ $visita->hora_salida ?? 'N/A' }}</div>
        <div class="info"><strong>Motivo:</strong> {{ $visita->smotivo }}</div>
        <div class="info"><strong>Lugar Específico:</strong> {{ $visita->lugar }}</div>
    </div>
</body>
</html>
