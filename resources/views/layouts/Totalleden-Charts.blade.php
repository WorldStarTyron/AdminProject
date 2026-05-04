<div class="chart-card">
    <div class="chart-card-header">
        <h3 class="chart-card-title">Ledenactiviteit</h3>
        <span class="chart-card-year">{{ date('Y') }}</span>
    </div>
    <div class="chart-canvas-wrapper">
        <canvas id="joinChart"></canvas>
    </div>
</div>

<style>
.chart-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 
        0 1px 3px rgba(0, 0, 0, 0.04),
        0 4px 12px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(226, 232, 240, 0.6);
    height: 100%;
    display: flex;
    flex-direction: column;
    animation: fadeSlideUp 0.5s ease-out 0.1s both;
}

.chart-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.chart-card-title {
    margin: 0;
    font-size: 0.9375rem;
    font-weight: 700;
    color: #1e293b;
    letter-spacing: -0.01em;
}

.chart-card-year {
    font-size: 0.75rem;
    font-weight: 600;
    color: #94a3b8;
    background: #f1f5f9;
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
}

.chart-canvas-wrapper {
    flex: 1;
    min-height: 180px;
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('joinChart');
    if (!ctx) return;

    // Monthly data from PHP
    const labels = {!! json_encode($labels) !!};
    const values = {!! json_encode($values) !!};

    // Create gradient
    const chartCtx = ctx.getContext('2d');
    const gradient = chartCtx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(30, 58, 138, 0.85)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.6)');

    // make the chart
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nieuwe leden',
                data: values,
                backgroundColor: gradient,
                borderRadius: 6,
                barThickness: 18,
                hoverBackgroundColor: '#1e40af',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 12, weight: '600' },
                    bodyFont: { size: 11 },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' leden';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(226, 232, 240, 0.5)',
                        drawBorder: false,
                    },
                    ticks: {
                        font: { size: 10, weight: '500' },
                        color: '#94a3b8',
                        stepSize: 1,
                        padding: 8,
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 10, weight: '500' },
                        color: '#94a3b8',
                        padding: 4,
                    }
                }
            }
        }
    });
});
</script>
