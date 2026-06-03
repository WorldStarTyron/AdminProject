// Deze code zorgt voor de inkomsten-grafiek op de rapportagepagina
const ctx = document.getElementById('incomeChart');

// We tekenen de grafiek alleen als de canvas op de pagina bestaat
if (ctx) {
    const canvasContext = ctx.getContext('2d');
    
    // We maken blauwe kleurverloopjes voor 'Overmaking'
    const contribGradient = canvasContext.createLinearGradient(0, 0, 0, 300);
    contribGradient.addColorStop(0, '#2563eb');
    contribGradient.addColorStop(1, '#60a5fa');

    // We maken groene kleurverloopjes voor 'Fysieke inname'
    const activityGradient = canvasContext.createLinearGradient(0, 0, 0, 300);
    activityGradient.addColorStop(0, '#10b981');
    activityGradient.addColorStop(1, '#34d399');

    // We halen de echte cijfers op uit de HTML data-attributes
    const labels = JSON.parse(ctx.dataset.labels || '[]');
    const overmakingData = JSON.parse(ctx.dataset.overmaking || '[]');
    const fysiekData = JSON.parse(ctx.dataset.fysiek || '[]');

    // We maken de Chart.js staafgrafiek met de echte getallen
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels, // Dit zijn de namen van de maanden (bijv. Jan, Feb, Mrt)
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
                        // Zorgt ervoor dat de bedragen in de popup netjes als SRD getoond worden
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
                    suggestedMax: 2000, // Als de getallen klein of 0 zijn, zorgt dit dat de schaal netjes tot 2000 SRD loopt. Wordt het hoger? Dan groeit de schaal automatisch mee!
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false
                    },
                    ticks: { 
                        font: { family: 'Inter', size: 11 },
                        color: '#64748b',
                        // Zorgt ervoor dat er 'Srd' voor de bedragen op de zijkant staat
                        callback: v => 'Srd ' + v.toLocaleString('nl-NL') 
                    } 
                }
            }
        }
    });
}

// Dit stukje zorgt ervoor dat de pagina opnieuw laadt als je de datums aanpast
const from = document.getElementById('date-from');
const to = document.getElementById('date-to');
if (from && to) {
    const update = () => { window.location.href = `?from=${from.value}&to=${to.value}`; };
    from.addEventListener('change', update);
    to.addEventListener('change', update);
}