 // Eenvoudige grafiek (staafdiagram)
    const ctx = document.getElementById('incomeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                datasets: [
                    { label: 'Contributie', data: [4000, 4500, 5200, 4800], backgroundColor: '#3b82f6', borderRadius: 4 },
                    { label: 'Activiteiten', data: [1200, 1600, 2100, 1450], backgroundColor: '#10b981', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '€' + v } }
                }
            }
        });
    }

    // Periodefilter: herlaad bij aanpassing
    const from = document.getElementById('date-from');
    const to = document.getElementById('date-to');
    if (from && to) {
        const update = () => { window.location.href = `?from=${from.value}&to=${to.value}`; };
        from.addEventListener('change', update);
        to.addEventListener('change', update);
    }