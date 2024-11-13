<style>
    /* Estilos para centrar y definir el tamaño del gráfico */
    .chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        margin: 20px 0;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    #recesosChart {
        max-width: 600px;
        max-height: 400px;
    }
</style>

<div class="chart-container">
    <canvas id="recesosChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('recesosChart').getContext('2d');
    const recesosChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($data['labels']),
            datasets: [{
                label: 'Recesos por Mes',
                data: @json($data['data']),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',  // Color de fondo más claro
                borderColor: 'rgba(75, 192, 192, 1)',         // Color de borde más oscuro
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#333',
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0,0,0,0.7)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    titleFont: { size: 16 },
                    bodyFont: { size: 14 },
                }
            },
            scales: {
                x: {
                    ticks: { color: '#555' },
                    grid: { display: false },
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#555' },
                    grid: { color: 'rgba(200, 200, 200, 0.2)' }
                }
            }
        }
    });
</script>
