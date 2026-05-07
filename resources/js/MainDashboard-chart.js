 document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Contributie',
                    data: [44, 55, 41, 67, 22]
                }, {
                    name: 'Leden',
                    data: [13, 23, 20, 8, 13]
                }, {
                    name: 'Betaald',
                    data: [11, 17, 15, 15, 21]
                }, {
                    name: 'Niet betaald',
                    data: [21, 7, 25, 13, 22]
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: false,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 2
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May','june','july','august','september','october','november','december'],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        show: false
                    }
                },
                grid: {
                    show: false,
                },
                fill: {
                    opacity: 1
                },
                colors: ['#1e293b', '#64748b', '#94a3b8', '#cbd5e1'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    markers: {
                        radius: 12,
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
            chart.render();
        });