// Dit bestand zorgt voor de inkomsten grafiek op de rapportage pagina
// We halen de gegevens uit de HTML en tekenen de grafiek

const ctx = document.getElementById('incomeChart');

// Alleen tekenen als de canvas op de pagina staat
if (ctx) {
    const canvasContext = ctx.getContext('2d');

    // Blauwe kleur voor overmaking
    const contribGradient = canvasContext.createLinearGradient(0, 0, 0, 300);
    contribGradient.addColorStop(0, '#2563eb');
    contribGradient.addColorStop(1, '#60a5fa');

    // Groene kleur voor fysieke inname
    const activityGradient = canvasContext.createLinearGradient(0, 0, 0, 300);
    activityGradient.addColorStop(0, '#10b981');
    activityGradient.addColorStop(1, '#34d399');

    // Gegevens ophalen uit de HTML
    const labels = JSON.parse(ctx.dataset.labels || '[]');
    const overmakingData = JSON.parse(ctx.dataset.overmaking || '[]');
    const fysiekData = JSON.parse(ctx.dataset.fysiek || '[]');

    // Grafiek tekenen
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Overmaking',
                    data: overmakingData,
                    backgroundColor: contribGradient,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Fysieke',
                    data: fysiekData,
                    backgroundColor: activityGradient,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: 'Inter', size: 12, weight: 'semibold' },
                    bodyFont: { family: 'Inter', size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'SRD' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11, weight: 'medium' },
                        color: '#64748b'
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: 2000,
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false
                    },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#64748b',
                        callback: v => 'Srd ' + v.toLocaleString('nl-NL')
                    }
                }
            }
        }
    });
}

// Datum filters: pagina herladen als datums veranderen
const from = document.getElementById('date-from');
const to = document.getElementById('date-to');
if (from && to) {
    const update = () => { window.location.href = `?from=${from.value}&to=${to.value}`; };
    from.addEventListener('change', update);
    to.addEventListener('change', update);
}
