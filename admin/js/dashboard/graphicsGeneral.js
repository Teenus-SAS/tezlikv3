// Registros por mes
graphicMonth = (allRecords) => {
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const mesActual = meses[new Date().getMonth()];

    const dataDelMes = allRecords.filter(r => r.mes === mesActual);

    const agrupados = {};
    dataDelMes.forEach(r => {
        if (!agrupados[r.company]) {
            agrupados[r.company] = 0;
        }
        agrupados[r.company] += r.total_registros;
    });

    const labels = Object.keys(agrupados);
    const data = Object.values(agrupados);

    // 🔢 Calcular promedio
    const total = data.reduce((sum, val) => sum + val, 0);
    const promedio = (total / data.length).toFixed(2);

    const ctx = document.getElementById('chartMonth').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: `Total registros - ${mesActual}`,
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: `Promedio (${promedio})`,
                    data: new Array(labels.length).fill(promedio),
                    type: 'line',
                    borderColor: 'rgba(255, 99, 132, 0.8)',
                    borderWidth: 1.5,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    tension: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: `Registros por empresa - ${mesActual}`
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total registros'
                    }
                }
            }
        }
    });
};


// Registros por año
graphicYear = (allRecords) => {
    const acumulados = {};
    let totalGeneral = 0;

    allRecords.forEach(r => {
        if (!acumulados[r.company]) {
            acumulados[r.company] = 0;
        }
        acumulados[r.company] += r.total_registros;
        totalGeneral += r.total_registros;
    });

    const labels = Object.keys(acumulados);
    const data = labels.map(company =>
        ((acumulados[company] / totalGeneral) * 100).toFixed(2)
    );

    const ctx = document.getElementById('chartYear').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels, // aún se usan para el tooltip
            datasets: [{
                data: data,
                backgroundColor: labels.map(() =>
                    `hsl(${Math.random() * 360}, 70%, 60%)`
                ),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Participación por empresa - Año actual'
                },
                legend: {
                    display: false // ocultar leyenda (labels fijos)
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${value}%`;
                        }
                    }
                }
            }
        }
    });
};


graphicChartLineEvolution = (allRecords) => {
    const mesesOrdenados = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const empresas = [...new Set(allRecords.map(r => r.company))];

    const select = document.getElementById('selectEmpresa');
    select.innerHTML = '<option value="">-- Selecciona --</option>'; // reset

    empresas.forEach(empresa => {
        const option = document.createElement('option');
        option.value = empresa;
        option.textContent = empresa;
        select.appendChild(option);
    });

    const ctx = document.getElementById('chartLineEvolution').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: mesesOrdenados,
            datasets: []
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución mensual de registros por empresa'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total registros'
                    }
                }
            }
        }
    });

    // Escuchar cambios del <select>
    select.addEventListener('change', function () {
        const empresaSeleccionada = this.value;

        if (!empresaSeleccionada) {
            chart.data.datasets = [];
            chart.update();
            return;
        }

        const registrosEmpresa = allRecords.filter(r => r.company === empresaSeleccionada);
        const dataPorMes = new Array(12).fill(0);
        registrosEmpresa.forEach(r => {
            const indexMes = mesesOrdenados.indexOf(r.mes);
            if (indexMes !== -1) {
                dataPorMes[indexMes] += r.total_registros;
            }
        });

        // Calcular promedio sin contar ceros
        const suma = dataPorMes.reduce((acc, val) => acc + val, 0);
        const cantidadMeses = dataPorMes.filter(val => val > 0).length || 1;
        const promedio = (suma / cantidadMeses).toFixed(2);

        chart.data.datasets = [
            {
                label: empresaSeleccionada,
                data: dataPorMes,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6
            },
            {
                label: `Promedio (${promedio})`,
                data: new Array(12).fill(promedio),
                borderColor: 'rgba(255, 99, 132, 0.8)',
                borderWidth: 1.5,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            }
        ];

        chart.update();
    });
}

// Paso 4: Función para actualizar la línea
function actualizarGrafico(empresaSeleccionada) {
    if (!empresaSeleccionada) {
        chart.data.datasets = [];
        chart.update();
        return;
    }

    const registrosEmpresa = allRecords.filter(r => r.company === empresaSeleccionada);

    const dataPorMes = new Array(12).fill(0);
    registrosEmpresa.forEach(r => {
        const indexMes = mesesOrdenados.indexOf(r.mes);
        if (indexMes !== -1) {
            dataPorMes[indexMes] += r.total_registros;
        }
    });

    chart.data.datasets = [{
        label: empresaSeleccionada,
        data: dataPorMes,
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'transparent',
        borderWidth: 2,
        tension: 0.3,
        pointRadius: 4,
        pointHoverRadius: 6
    }];
    chart.update();
}

function graphicHeatmap(allRecords) {
    // Orden de los meses
    const mesesOrden = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'];

    // Obtener empresas únicas
    const empresas = [...new Set(allRecords.map(item => item.company))];

    // Crear matriz de datos
    const dataMatrix = empresas.map(empresa => {
        return mesesOrden.map(mes => {
            const registro = allRecords.find(r => r.company === empresa && r.mes === mes);
            return registro ? registro.total_registros : 0;
        });
    });

    // Encontrar el valor máximo para la escala de colores
    const maxValue = Math.max(...allRecords.map(r => r.total_registros));

    return {
        labels: mesesOrden,
        empresas: empresas,
        data: dataMatrix,
        maxValue: maxValue
    };
}

// 2. Crear el mapa de calor
function createHeatmapChart(allRecords) {
    const ctx = document.getElementById('heatmapChart').getContext('2d');
    const heatmapData = graphicHeatmap(allRecords);

    // Configurar los datos para Chart.js
    const datasets = heatmapData.empresas.map((empresa, i) => {
        return {
            label: empresa,
            data: heatmapData.data[i],
            backgroundColor: heatmapData.data[i].map(value => {
                // Calcular intensidad del color (rojo) basado en el valor
                const intensity = value / heatmapData.maxValue;
                return `rgba(255, ${Math.floor(100 * (1 - intensity))}, ${Math.floor(100 * (1 - intensity))}, 0.7)`;
            }),
            borderColor: 'rgba(200, 200, 200, 0.2)',
            borderWidth: 1
        };
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: heatmapData.labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Registros por Empresa y Mes',
                    font: {
                        size: 16
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `${context.dataset.label}: ${context.raw} registros`;
                        }
                    }
                },
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Meses'
                    }
                },
                y: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Total de Registros'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

