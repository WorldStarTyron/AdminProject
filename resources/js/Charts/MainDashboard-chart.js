document.addEventListener('DOMContentLoaded', function () {

    // Chart opties
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
                borderRadius: 2
            }
        },
        dataLabels: { enabled: false },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: [],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#94a3b8', fontSize: '12px' }
            }
        },
        yaxis: {
            labels: { show: false }
        },
        grid: { show: true },
        fill: { opacity: 1 },
        colors: ['#1e293b', '#64748b', '#94a3b8', '#cbd5e1'],
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            markers: { radius: 12 },
            itemMargin: { horizontal: 10, vertical: 0 }
        },
        noData: {
            text: 'Data laden...',
            style: { color: '#94a3b8', fontSize: '14px' }
        }
    };

    var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
    chart.render();

    // Referenties naar de dropdowns
    var jaarSelect = document.getElementById('filterJaar');
    var maandSelect = document.getElementById('filterMaand');

    // Functie om chart data op te halen met filters
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
            // Update x-as labels
            chart.updateOptions({
                xaxis: {
                    categories: data.labels
                }
            });

            // Update chart series (alleen de 4 relevante datasets)
            chart.updateSeries([
                { name: 'Contributie (Srd)', data: data.contributie },
                { name: 'Leden',             data: data.leden },
                { name: 'Betaald',           data: data.betaald },
                { name: 'Niet betaald',      data: data.niet_betaald }
            ]);

            // Vul de Jaar dropdown (alleen bij eerste keer laden)
            if (jaarSelect && jaarSelect.options.length <= 1) {
                data.Totaaljaren.forEach(function(jaar) {
                    var opt = document.createElement('option');
                    opt.value = jaar;
                    opt.textContent = jaar;
                    // Selecteer het huidige jaar
                    if (jaar === data.huidigJaar) {
                        opt.selected = true;
                    }
                    jaarSelect.appendChild(opt);
                });
            }

            // Vul de Maand dropdown opnieuw (maanden kunnen per jaar anders zijn)
            if (maandSelect) {
                var currentMaandValue = maandSelect.value;
                // Bewaar alleen de eerste "Alle maanden" optie
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
            console.error('Chart data kon niet geladen worden:', err);
        });
    }

    // Event listeners voor de dropdowns
    if (jaarSelect) {
        jaarSelect.addEventListener('change', function() {
            // Reset maand selectie wanneer jaar verandert
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