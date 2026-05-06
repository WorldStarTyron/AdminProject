// TotalLeden-Chart.js
// Chart initialization for the Totalleden-Charts component.
// Reads $labels and $values from data attributes on the #joinChart canvas element,
// which are set in the Totalleden-Charts.blade.php template via @json().

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('joinChart');
    if (!ctx) return;

    // Monthly data from PHP (passed via data attributes on the canvas element)
    const labels = JSON.parse(ctx.dataset.labels);
    const values = JSON.parse(ctx.dataset.values);

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