document.addEventListener('DOMContentLoaded', function () {

    // Lege chart alvast renderen met loading state
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
            categories: ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#94a3b8', fontSize: '12px' }
            }
        },
        yaxis: {
            labels: { show: false }
        },
        grid: { show: false },
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

    // Data ophalen van de controller
    fetch('/dashboard/chart-data', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        chart.updateSeries([
            { name: 'Contributie (Srd)', data: data.contributie },
            { name: 'Leden',             data: data.leden },
            { name: 'Betaald',           data: data.betaald },
            { name: 'Niet betaald',      data: data.niet_betaald }
        ]);
    })
    .catch(err => {
        console.error('Chart data kon niet geladen worden:', err);
    });

});
       