 // ApexCharts - Stacked Bar Chart

    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{
                name: 'Cash',
                data: [4200, 3800, 4500, 3200, 4800, 5100, 4600]
            }, {
                name: 'Overmaking',
                data: [3100, 2900, 3400, 2800, 3600, 3900, 3200]
            }, {
                name: 'Mobiel',
                data: [1800, 2200, 1900, 2100, 2400, 2600, 2300]
            }],
            chart: {
                type: 'bar',
                height: 200,
                stacked: true,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '45%',
                }
            },
            colors: ['#1e293b', '#64748b', '#cbd5e1'],
            xaxis: {
                categories: ['Jan', 'Feb', 'Maart', 'April', 'Mei', 'Juni', 'Juli'],
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '11px',
                        fontWeight: 500,
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                show: false,
            },
            grid: {
                show: false,
            },
            legend: {
                show: false,
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function(val) {
                        return 'Srd ' + val.toLocaleString();
                    }
                }
            },
        };

        var chart = new ApexCharts(document.querySelector("#contributieChart"), options);
        chart.render();
    });
