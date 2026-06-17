document.addEventListener('DOMContentLoaded', function () {

    const container = document.querySelector('#contributieChart');
    if (!container) return;

    const maandSelect = document.querySelector('#maand');
    const jaarSelect  = document.querySelector('#jaar');

    function loadChart() {
        const maand = maandSelect ? maandSelect.value : '';
        const jaar  = jaarSelect ? jaarSelect.value : '';

        fetch(`/betalingen/chart-data?maand=${maand}&jaar=${jaar}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => {
            if (!res.ok) throw new Error('Netwerkfout: ' + res.status);
            return res.json();
        })
        .then(data => renderChart(data.labels, data.series))
        .catch(err => {
            console.error(err);
            container.innerHTML =
                '<p class="text-slate-400 text-sm text-center pt-8">Grafiek kon niet worden geladen.</p>';
        });
    }

    function renderChart(labels, series) {
        container.innerHTML = ''; // belangrijk: oude chart verwijderen

        var options = {
            series: series,
            chart: {
                type: 'bar',
                height: 250,
                toolbar: { show: false },
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    columnWidth: '60%',
                },
            },
            xaxis: {
                categories: labels,
                labels: {
                    rotate: -45,
                    style: { fontSize: '10px' },
                },
                formatter: function(val, index){
                    return (index +1 ) % 5 === 0 || index === 0 ? val : '';
                },
                tickPlacement: 'on',
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return 'SRD ' + val.toLocaleString('nl-NL');
                    },
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'SRD ' + val.toLocaleString('nl-NL', { minimumFractionDigits: 2 });
                    },
                },
            },
            colors: ['#14b8a6'],
            dataLabels: { enabled: false },
        };

        var chart = new ApexCharts(container, options);
        chart.render();
    }

    // init load
    loadChart();

    // refresh bij wijziging filter
    if (maandSelect) maandSelect.addEventListener('change', loadChart);
    if (jaarSelect) jaarSelect.addEventListener('change', loadChart);

});