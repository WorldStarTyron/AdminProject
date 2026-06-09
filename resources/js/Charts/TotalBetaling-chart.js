// ApexCharts - Stacked Bar Chart (live data via /betalingen/chart-data)

document.addEventListener('DOMContentLoaded', function () {

    // ── Skeleton / laadstatus tonen
    const container = document.querySelector('#contributieChart');
    if (!container) return;

    // ── Data ophalen van de backend 
    fetch('/betalingen/chart-data', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then(function (response) {
            if (!response.ok) throw new Error('Netwerkfout: ' + response.status);
            return response.json();
        })
        .then(function (data) {
            renderChart(data.labels, data.series);
        })
        .catch(function (err) {
            console.error('Chart data kon niet worden opgehaald:', err);
            // Toon een vriendelijke foutmelding in de chart-container
            container.innerHTML =
                '<p class="text-slate-400 color-red text-sm text-center pt-8">Grafiek kon niet worden geladen.</p>';
        });

    // ── Chart renderen ───────────────────────────────────────────────────────
    function renderChart(labels, series) {
        var options = {
            series: series,
            chart: {
                type: 'bar',
                height: 200,
                stacked: false,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 600,
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 2,
                    columnWidth: '45%',
                },
            },
            colors: ['#0867ffff'],
            xaxis: {
                categories: labels,
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '11px',
                        fontWeight: 500,
                    },
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                show: true,
                labels: {
                    formatter: function (val) {
                        return 'SRD ' + val.toLocaleString('nl-NL', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    },
                    style: {
                        colors: '#94a3b8',
                        fontSize: '10px',
                        fontWeight: 500,
                    }
                }
            },
            grid: {
                show: true,
            },
            legend: {
                show: true,
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                theme: 'dark',
                y: {
                    // Toon totale bedrag in srd
                    formatter: function (val) {
                        return new Intl.NumberFormat('nl-SR', {
                            style: 'currency',
                            currency: 'SRD',
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2 
                        }).format(val);
                    },
                },
            },
        };

        var chart = new ApexCharts(container, options);
        chart.render();
    }
});