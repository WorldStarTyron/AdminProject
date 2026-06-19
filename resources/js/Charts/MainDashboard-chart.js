// Dit bestand zorgt voor de grafiek op het dashboard
// We halen de gegevens op en laten de grafiek zien

document.addEventListener('DOMContentLoaded', function () {

// Basis instellingen voor de grafiek
    var options = {
        series: [],
        chart: {
            type: 'bar',
            height: 300,
            stacked: false,
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif',
            animations: {
                enabled: true,
                speed: 600
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4,
                borderRadiusApplication: 'end',
                dataLabels: { position: 'top' }
            }
        },
        dataLabels: { enabled: false },
        stroke: {
            show: true,
            width: 1,
            colors: ['#0ea5e9', '#22c55e', '#f59e0b']
        },
        xaxis: {
            categories: [],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#64748b', fontSize: '11px' }
            }
        },
        yaxis: {
            labels: {
                show: true,
                style: { colors: '#64748b', fontSize: '11px' },
                formatter: function(val) {
                    if (val >= 1000) return (val/1000).toFixed(1) + 'K';
                    return val;
                }
            }
        },
        grid: {
            show: true,
            borderColor: '#e2e8f0',
            strokeDashArray: 4
        },
        colors: ['#0ea5e9', '#22c55e', '#f59e0b'],
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            markers: { radius: 12 },
            itemMargin: { horizontal: 10, vertical: 0 },
            fontSize: '12px'
        },
        noData: {
            text: 'Gegevens laden...',
            style: { color: '#94a3b8', fontSize: '14px' }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toLocaleString('nl-NL');
                }
            }
        }
    };

    // Grafiek maken
    var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
    chart.render();

    // Dropdowns ophalen
    var jaarSelect = document.getElementById('filterJaar');
    var maandSelect = document.getElementById('filterMaand');

    // Gegevens ophalen met filters
    function loadChartData() {
        var params = new URLSearchParams();

        if (jaarSelect && jaarSelect.value) {
            params.append('jaar', jaarSelect.value);
        }
        if (maandSelect && maandSelect.value) {
            params.append('maand', maandSelect.value);
        }

        var url = '/dashboard/chart-data';
        if (params.toString()) {
            url += '?' + params.toString();
        }

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Labels updaten
            chart.updateOptions({
                xaxis: {
                    categories: data.labels
                }
            });

// Gegevens in de grafiek zetten
            chart.updateSeries([
                { name: 'Inkomen (SRD)', data: data.contributie },
                { name: 'Betaald',         data: data.betaald },
                { name: 'Niet betaald',   data: data.niet_betaald }
            ]);

            // Jaar dropdown vullen (alleen eerst keer)
            if (jaarSelect && jaarSelect.options.length <= 1) {
                data.Totaaljaren.forEach(function(jaar) {
                    var opt = document.createElement('option');
                    opt.value = jaar;
                    opt.textContent = jaar;
                    if (jaar === data.huidigJaar) {
                        opt.selected = true;
                    }
                    jaarSelect.appendChild(opt);
                });
            }

            // Maand dropdown vullen
            if (maandSelect) {
                var currentMaandValue = maandSelect.value;
                while (maandSelect.options.length > 1) {
                    maandSelect.remove(1);
                }

                var maandNamen = {
                    1: 'Januari', 2: 'Februari', 3: 'Maart', 4: 'April',
                    5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Augustus',
                    9: 'September', 10: 'Oktober', 11: 'November', 12: 'December'
                };

                data.Totaalmaanden.forEach(function(maand) {
                    var opt = document.createElement('option');
                    opt.value = maand;
                    opt.textContent = maandNamen[maand] || 'Maand ' + maand;
                    if (String(maand) === String(currentMaandValue)) {
                        opt.selected = true;
                    }
                    maandSelect.appendChild(opt);
                });
            }
        })
        .catch(err => {
            console.error('Kon gegevens niet laden:', err);
        });
    }

    // Bij verandering van jaar of maand, opnieuw laden
    if (jaarSelect) {
        jaarSelect.addEventListener('change', function() {
            if (maandSelect) {
                maandSelect.value = '';
            }
            loadChartData();
        });
    }

    if (maandSelect) {
        maandSelect.addEventListener('change', function() {
            loadChartData();
        });
    }

    // Eerste keer laden
    loadChartData();
});
