// TotalLeden-Chart.js
// Chart initialization for the ledenpagina.
// Reads $labels and $values from data attributes on the #joinChart canvas element,
// which are set in the blade template via @json().

document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('joinChart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels);
    const values = JSON.parse(canvas.dataset.values);

    const ctx = canvas.getContext('2d');

    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(203, 213, 225, 0.8)');
    gradient.addColorStop(1, 'rgba(226, 232, 240, 0.3)');

    // Highlight gradient for the last bar with data
    const highlightGradient = ctx.createLinearGradient(0, 0, 0, 300);
    highlightGradient.addColorStop(0, 'rgba(191, 219, 254, 1)');
    highlightGradient.addColorStop(1, 'rgba(219, 234, 254, 0.6)');

    // Determine which bar to highlight (last non-zero or last)
    let highlightIndex = values.length - 1;
    for (let i = values.length - 1; i >= 0; i--) {
        if (values[i] > 0) {
            highlightIndex = i;
            break;
        }
    }

    const backgroundColors = values.map((_, i) =>
        i === highlightIndex ? highlightGradient : gradient
    );

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.map(l => l.toUpperCase()),
            datasets: [{
                data: values,
                backgroundColor: backgroundColors,
                borderRadius: 6,
                borderSkipped: false,
                barPercentage: 0.5,
                categoryPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: 'Inter', size: 12 },
                    bodyFont: { family: 'Inter', size: 12 },
                    cornerRadius: 8,
                    padding: 10,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' leden';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'Inter', size: 11, weight: '500' },
                    },
                    border: { display: false }
                },
                y: {
                    display: false,
                    beginAtZero: true,
                }
            }
        }
    });
});