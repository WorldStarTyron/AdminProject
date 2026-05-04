<div class="chart-container" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: 100%;">
    <h3 style="margin-top: 0; font-size: 1.1rem; color: #1e3a8a; font-weight: 700;">Leden Groei</h3>
    <div style="height: 200px;">
        <canvas id="joinChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('joinChart').getContext('2d');
    
    // Monthly data from PHP
    const labels = {!! json_encode($labels) !!};
    const values = {!! json_encode($values) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Joined',
                data: values,
                backgroundColor: '#1e3a8a',
                borderRadius: 4,
                barThickness: 15,
                hoverBackgroundColor: '#1e40af',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        display: false
                    },
                    border: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10
                        },
                        color: '#a0aec0'
                    }
                }
            }
        }
    });
});
</script>
