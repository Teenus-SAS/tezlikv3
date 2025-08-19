/**
 * ARCHIVO: dashboard-charts.js
 * PROPÓSITO: Dashboard de ganancias semanales - ARCHIVO INDEPENDIENTE
 * INCLUIR EN HTML: <script src="dashboard-charts.js"></script>
 */

// Variables globales
let dashboardData = null;
let weeklyChart = null;
let productChart = null;
let currentWeek = 'all';
let currentProduct = 'all';

/**
 * Función principal: Cargar datos del dashboard
 */
async function loadDashboardData() {
    try {
        console.log('🔄 Cargando datos del dashboard...');

        // Mostrar loading en select
        const weekSelector = document.getElementById('weekSelector');
        weekSelector.innerHTML = '<option value="">⏳ Cargando semanas...</option>';
        weekSelector.disabled = true;

        // Obtener datos del servidor
        const response = await fetch('/api/dataHistorical');
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        console.log('✅ Datos recibidos:', data);

        // Procesar datos
        dashboardData = processWeeklyData(data);

        // Llenar selectores
        fillWeekSelector();
        fillProductSelector();

        // Mostrar gráfico inicial
        showAllWeeksChart();

        // Habilitar select
        weekSelector.disabled = false;

        console.log('🎉 Dashboard cargado exitosamente');

    } catch (error) {
        console.error('❌ Error cargando dashboard:', error);
        document.getElementById('weekSelector').innerHTML = '<option value="">❌ Error cargando datos</option>';
    }
}

/**
 * Procesar datos semanales
 */
function processWeeklyData(data) {
    if (!data || data.length !== 3) {
        throw new Error('Datos inválidos');
    }

    const [expenses, distributions, historical] = data;
    const weeklyData = {};
    const productTotals = {};

    // Procesar cada registro
    historical.forEach(item => {
        const week = item.month;
        const productId = item.id_product;
        const productName = item.product_name || item.product || `Producto ${productId}`;

        // Calcular costos TOTALES (costos unitarios * unidades vendidas)
        const units = parseInt(item.units_sold) || 0;
        const unitCosts = (parseFloat(item.cost_material) || 0) +
            (parseFloat(item.cost_workforce) || 0) +
            (parseFloat(item.cost_indirect) || 0) +
            (parseFloat(item.assignable_expense) || 0);

        const costs = unitCosts * units;

        const revenue = parseFloat(item.turnover) || 0;
        const profit = revenue - costs;

        // Agrupar por semana
        if (!weeklyData[week]) {
            weeklyData[week] = {
                week: week,
                totalProfit: 0,
                totalRevenue: 0,
                totalCosts: 0,
                totalUnits: 0,
                products: {}
            };
        }

        weeklyData[week].totalProfit += profit;
        weeklyData[week].totalRevenue += revenue;
        weeklyData[week].totalCosts += costs;
        weeklyData[week].totalUnits += units;

        // Productos por semana
        if (!weeklyData[week].products[productId]) {
            weeklyData[week].products[productId] = {
                id: productId,
                name: productName,
                profit: 0,
                revenue: 0,
                units: 0
            };
        }

        weeklyData[week].products[productId].profit += profit;
        weeklyData[week].products[productId].revenue += revenue;
        weeklyData[week].products[productId].units += units;

        // Totales por producto
        if (!productTotals[productId]) {
            productTotals[productId] = {
                id: productId,
                name: productName,
                totalProfit: 0
            };
        }
        productTotals[productId].totalProfit += profit;
    });

    // Convertir a arrays
    const weeks = Object.values(weeklyData).sort((a, b) => a.week.localeCompare(b.week));

    // CAMBIO PRINCIPAL: Remover .slice(0, 10) para cargar TODOS los productos
    const topProducts = Object.values(productTotals)
        .sort((a, b) => b.totalProfit - a.totalProfit);
    // .slice(0, 10); ← ESTA LÍNEA SE ELIMINA

    console.log(`✅ Procesados ${weeks.length} semanas y ${topProducts.length} productos (sin límite)`);

    return { weeks, topProducts, weeklyData };
}

/**
 * Llenar selector de semanas
 */
function fillWeekSelector() {
    const select = document.getElementById('weekSelector');
    select.innerHTML = '<option value="all">📊 Todas las Semanas</option>';

    dashboardData.weeks.forEach(week => {
        const option = document.createElement('option');
        option.value = week.week;
        option.textContent = `📅 ${week.week} - ${formatCurrency(week.totalProfit)}`;
        select.appendChild(option);
    });

    console.log(`📝 ${dashboardData.weeks.length} semanas agregadas al selector`);
}

/**
 * Llenar selector de productos
 */
function fillProductSelector() {
    const select = document.getElementById('productSelector');
    select.innerHTML = '<option value="all">📦 Todos los Productos</option>';

    console.log(`📝 Cargando ${dashboardData.topProducts.length} productos en el selector...`);

    dashboardData.topProducts.forEach((product, index) => {
        const option = document.createElement('option');
        option.value = product.id;

        // Mostrar posición, nombre y ganancia
        const position = (index + 1).toString().padStart(2, '0');
        option.textContent = `${position}. ${product.name} - ${formatCurrency(product.totalProfit)}`;

        select.appendChild(option);
    });

    console.log(`✅ ${dashboardData.topProducts.length} productos cargados en el selector`);
}
/**
 * Mostrar vista de todas las semanas
 */
function showAllWeeksChart() {
    const container = document.querySelector('.cardDashboard .container-fluid');
    container.innerHTML = '';

    container.innerHTML = `
        <!-- CARDS DE INDICADORES EN LA PARTE SUPERIOR -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success" id="totalSales">$0</h3>
                        <p class="mb-0">💰 Total Ventas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-danger" id="totalCosts">$0</h3>
                        <p class="mb-0">📊 Total Costos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary" id="totalProfits">$0</h3>
                        <p class="mb-0">⭐ Total Ganancias</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info" id="totalMargin">0%</h3>
                        <p class="mb-0">📈 Margen Promedio</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- GRÁFICO PRINCIPAL DEBAJO -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📈 Análisis Financiero por Semana</h5>
                        <small class="text-muted">Evolución de Ventas, Costos y Ganancias</small>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 450px; width: 100%;">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    createTripleMetricsChart();
    updateSummaryMetrics();
}

/**
 * Mostrar vista de semana específica
 */
function showWeekChart(weekId) {
    const weekData = dashboardData.weeklyData[weekId];
    if (!weekData) return;

    const container = document.querySelector('.cardDashboard .container-fluid');
    container.innerHTML = '';

    container.innerHTML = `
        <!-- CARDS DE INDICADORES DE LA SEMANA EN LA PARTE SUPERIOR -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success">${formatCurrency(weekData.totalRevenue)}</h4>
                        <p class="mb-0">💰 Ventas ${weekId}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-danger">${formatCurrency(weekData.totalCosts)}</h4>
                        <p class="mb-0">📊 Costos ${weekId}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary">${formatCurrency(weekData.totalProfit)}</h4>
                        <p class="mb-0">⭐ Ganancias ${weekId}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info">${((weekData.totalProfit / weekData.totalRevenue) * 100).toFixed(1)}%</h4>
                        <p class="mb-0">📈 Margen ${weekId}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- GRÁFICO Y LISTA DEBAJO -->
        <div class="row mb-4">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📊 Productos de la ${weekId}</h5>
                        <small class="text-muted">Ventas, Costos y Ganancias por Producto</small>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 400px; width: 100%;">
                            <canvas id="productChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Top Productos - ${weekId}</h5>
                    </div>
                    <div class="card-body">
                        <div id="weekProductsList" style="max-height: 380px; overflow-y: auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    createProductsMetricsChart(weekData);
    fillWeekProductsList(weekData);
}

/**
 * NUEVO: Mostrar análisis de un producto específico a través de las semanas
 */
function showProductAnalysis(productId) {
    console.log(`🔍 Analizando producto: ${productId}`);

    // Obtener datos del producto a través de todas las semanas
    const productData = getProductDataAcrossWeeks(productId);
    if (!productData) {
        console.error('❌ No se encontraron datos para el producto');
        return;
    }

    const container = document.querySelector('.cardDashboard .container-fluid');
    container.innerHTML = '';

    container.innerHTML = `
        <!-- CARDS DE INDICADORES DEL PRODUCTO EN LA PARTE SUPERIOR -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success">${formatCurrency(productData.totalRevenue)}</h4>
                        <p class="mb-0">💰 Ventas Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-danger">${formatCurrency(productData.totalCosts)}</h4>
                        <p class="mb-0">📊 Costos Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary">${formatCurrency(productData.totalProfit)}</h4>
                        <p class="mb-0">⭐ Ganancias Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info">${productData.averageMargin}%</h4>
                        <p class="mb-0">📈 Margen Promedio</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- GRÁFICO Y RESUMEN DEBAJO -->
        <div class="row mb-4">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📈 Evolución del ${productData.name}</h5>
                        <small class="text-muted">Análisis temporal de ventas, costos y ganancias</small>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 400px; width: 100%;">
                            <canvas id="productEvolutionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📊 Resumen del Producto</h5>
                    </div>
                    <div class="card-body">
                        <div id="productSummary">
                            <!-- Resumen se llena dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Crear gráfico de evolución del producto
    createProductEvolutionChart(productData);

    // Llenar resumen del producto
    fillProductSummary(productData);
}

/**
 * NUEVO: Obtener datos de un producto a través de todas las semanas
 */
function getProductDataAcrossWeeks(productId) {
    const productWeeks = [];
    let totalRevenue = 0;
    let totalCosts = 0;
    let totalProfit = 0;
    let totalUnits = 0;
    let productName = '';

    // Buscar el producto en todas las semanas
    dashboardData.weeks.forEach(week => {
        if (week.products[productId]) {
            const product = week.products[productId];
            const costs = product.revenue - product.profit;

            productWeeks.push({
                week: week.week,
                revenue: product.revenue,
                costs: costs,
                profit: product.profit,
                units: product.units,
                margin: product.revenue > 0 ? ((product.profit / product.revenue) * 100).toFixed(1) : 0
            });

            totalRevenue += product.revenue;
            totalCosts += costs;
            totalProfit += product.profit;
            totalUnits += product.units;
            productName = product.name;
        }
    });

    if (productWeeks.length === 0) {
        return null;
    }

    // Calcular estadísticas
    const averageMargin = totalRevenue > 0 ? ((totalProfit / totalRevenue) * 100).toFixed(1) : 0;
    const bestWeek = productWeeks.reduce((max, week) => week.profit > max.profit ? week : max, productWeeks[0]);
    const worstWeek = productWeeks.reduce((min, week) => week.profit < min.profit ? week : min, productWeeks[0]);

    return {
        id: productId,
        name: productName,
        weeks: productWeeks.sort((a, b) => a.week.localeCompare(b.week)),
        totalRevenue,
        totalCosts,
        totalProfit,
        totalUnits,
        averageMargin,
        bestWeek,
        worstWeek,
        weeksActive: productWeeks.length
    };
}

/**
 * NUEVO: Crear gráfico de evolución del producto
 */
function createProductEvolutionChart(productData) {
    // IMPORTANTE: Destruir gráfico anterior
    if (productChart) {
        productChart.destroy();
        productChart = null;
    }

    const canvas = document.getElementById('productEvolutionChart');
    if (!canvas) return;

    // Establecer tamaño fijo del canvas
    canvas.width = canvas.offsetWidth;
    canvas.height = 400;
    canvas.style.width = '100%';
    canvas.style.height = '400px';

    const chartType = getSelectedChartType();

    productChart = new Chart(canvas, {
        type: chartType,
        data: {
            labels: productData.weeks.map(w => w.week),
            datasets: [
                {
                    label: '💰 Ventas',
                    data: productData.weeks.map(w => w.revenue),
                    borderColor: '#10b981',
                    backgroundColor: chartType === 'line' ? 'rgba(16, 185, 129, 0.1)' : '#10b981',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4
                },
                {
                    label: '📊 Costos',
                    data: productData.weeks.map(w => w.costs),
                    borderColor: '#ef4444',
                    backgroundColor: chartType === 'line' ? 'rgba(239, 68, 68, 0.1)' : '#ef4444',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4
                },
                {
                    label: '⭐ Ganancias',
                    data: productData.weeks.map(w => w.profit),
                    borderColor: '#3b82f6',
                    backgroundColor: chartType === 'line' ? 'rgba(59, 130, 246, 0.1)' : '#3b82f6',
                    borderWidth: 4,
                    fill: chartType === 'area',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: `Evolución Temporal - ${productData.name}`
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            return `${productData.name} - ${context[0].label}`;
                        },
                        label: function (context) {
                            return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value);
                        }
                    }
                }
            }
        }
    });

    console.log(`📈 Gráfico de evolución creado para ${productData.name}`);
}

/**
 * NUEVO: Llenar resumen del producto
 */
function fillProductSummary(productData) {
    const container = document.getElementById('productSummary');
    if (!container) return;

    container.innerHTML = `
        <div class="mb-4">
            <h6 class="text-primary mb-3">📋 Información General</h6>
            <div class="row text-center">
                <div class="col-6">
                    <div class="border rounded p-2 mb-2">
                        <div class="font-weight-bold text-success">${productData.weeksActive}</div>
                        <small class="text-muted">Semanas Activas</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-2 mb-2">
                        <div class="font-weight-bold text-info">${productData.totalUnits}</div>
                        <small class="text-muted">Unidades Totales</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <h6 class="text-success mb-3">🏆 Mejor Semana</h6>
            <div class="bg-light rounded p-3">
                <div class="d-flex justify-content-between">
                    <strong>${productData.bestWeek.week}</strong>
                    <span class="badge badge-success">${productData.bestWeek.margin}%</span>
                </div>
                <div class="small mt-2">
                    <div>💰 Ventas: ${formatCurrency(productData.bestWeek.revenue)}</div>
                    <div>⭐ Ganancia: ${formatCurrency(productData.bestWeek.profit)}</div>
                    <div>📦 Unidades: ${productData.bestWeek.units}</div>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <h6 class="text-danger mb-3">📉 Peor Semana</h6>
            <div class="bg-light rounded p-3">
                <div class="d-flex justify-content-between">
                    <strong>${productData.worstWeek.week}</strong>
                    <span class="badge badge-warning">${productData.worstWeek.margin}%</span>
                </div>
                <div class="small mt-2">
                    <div>💰 Ventas: ${formatCurrency(productData.worstWeek.revenue)}</div>
                    <div>⭐ Ganancia: ${formatCurrency(productData.worstWeek.profit)}</div>
                    <div>📦 Unidades: ${productData.worstWeek.units}</div>
                </div>
            </div>
        </div>
        
        <div>
            <h6 class="text-info mb-3">📊 Promedios</h6>
            <div class="bg-light rounded p-3">
                <div class="small">
                    <div>💰 Venta promedio: ${formatCurrency(productData.totalRevenue / productData.weeksActive)}</div>
                    <div>📊 Costo promedio: ${formatCurrency(productData.totalCosts / productData.weeksActive)}</div>
                    <div>⭐ Ganancia promedio: ${formatCurrency(productData.totalProfit / productData.weeksActive)}</div>
                    <div>📦 Unidades promedio: ${Math.round(productData.totalUnits / productData.weeksActive)}</div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Crear gráfico principal con 3 métricas
 */
function createTripleMetricsChart() {
    if (weeklyChart) {
        weeklyChart.destroy();
        weeklyChart = null;
    }

    const canvas = document.getElementById('weeklyChart');
    if (!canvas) return;

    canvas.width = canvas.offsetWidth;
    canvas.height = 450;
    canvas.style.width = '100%';
    canvas.style.height = '450px';

    const chartType = getSelectedChartType();

    weeklyChart = new Chart(canvas, {
        type: chartType,
        data: {
            labels: dashboardData.weeks.map(w => w.week),
            datasets: [
                {
                    label: '💰 Ventas',
                    data: dashboardData.weeks.map(w => w.totalRevenue),
                    borderColor: '#10b981',
                    backgroundColor: chartType === 'line' ? 'rgba(16, 185, 129, 0.1)' : '#10b981',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4
                },
                {
                    label: '📊 Costos',
                    data: dashboardData.weeks.map(w => w.totalCosts),
                    borderColor: '#ef4444',
                    backgroundColor: chartType === 'line' ? 'rgba(239, 68, 68, 0.1)' : '#ef4444',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4
                },
                {
                    label: '⭐ Ganancias',
                    data: dashboardData.weeks.map(w => w.totalProfit),
                    borderColor: '#3b82f6',
                    backgroundColor: chartType === 'line' ? 'rgba(59, 130, 246, 0.1)' : '#3b82f6',
                    borderWidth: 4,
                    fill: chartType === 'area',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Análisis Financiero Completo por Semana'
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value);
                        }
                    }
                }
            }
        }
    });
}

/**
 * Crear gráfico de productos por semana
 */
function createProductsMetricsChart(weekData) {
    if (productChart) {
        productChart.destroy();
        productChart = null;
    }

    const canvas = document.getElementById('productChart');
    if (!canvas) return;

    canvas.width = canvas.offsetWidth;
    canvas.height = 400;
    canvas.style.width = '100%';
    canvas.style.height = '400px';

    const products = Object.values(weekData.products)
        .sort((a, b) => b.profit - a.profit)
        .slice(0, 8);

    productChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: products.map(p => p.name.length > 12 ? p.name.substring(0, 12) + '...' : p.name),
            datasets: [
                {
                    label: '💰 Ventas',
                    data: products.map(p => p.revenue),
                    backgroundColor: '#10b981',
                    maxBarThickness: 40
                },
                {
                    label: '📊 Costos',
                    data: products.map(p => p.revenue - p.profit),
                    backgroundColor: '#ef4444',
                    maxBarThickness: 40
                },
                {
                    label: '⭐ Ganancias',
                    data: products.map(p => p.profit),
                    backgroundColor: '#3b82f6',
                    maxBarThickness: 40
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: `Análisis por Producto - ${weekData.week}`
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            return products[context[0].dataIndex].name;
                        },
                        label: function (context) {
                            return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value);
                        }
                    }
                }
            }
        }
    });
}

/**
 * Llenar lista de productos de la semana
 */
function fillWeekProductsList(weekData) {
    const container = document.getElementById('weekProductsList');
    if (!container) return;

    const products = Object.values(weekData.products)
        .sort((a, b) => b.profit - a.profit)
        .slice(0, 10);

    container.innerHTML = products.map((product, index) => {
        const costs = product.revenue - product.profit;
        const margin = product.revenue > 0 ? ((product.profit / product.revenue) * 100).toFixed(1) : 0;
        const color = getProductColor(index);

        return `
            <div class="mb-3 p-3 border rounded" style="border-left: 4px solid ${color};">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong class="text-truncate" style="max-width: 150px;" title="${product.name}">
                        ${product.name}
                    </strong>
                    <span class="badge badge-primary">${margin}%</span>
                </div>
                <div class="small">
                    <div class="d-flex justify-content-between">
                        <span class="text-success">💰 Ventas:</span>
                        <strong>${formatCurrency(product.revenue)}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-danger">📊 Costos:</span>
                        <strong>${formatCurrency(costs)}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-primary">⭐ Ganancia:</span>
                        <strong>${formatCurrency(product.profit)}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">📦 Unidades:</span>
                        <span>${product.units}</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Actualizar métricas de resumen
 */
function updateSummaryMetrics() {
    const totalSales = dashboardData.weeks.reduce((sum, week) => sum + week.totalRevenue, 0);
    const totalCosts = dashboardData.weeks.reduce((sum, week) => sum + week.totalCosts, 0);
    const totalProfits = dashboardData.weeks.reduce((sum, week) => sum + week.totalProfit, 0);
    const totalMargin = totalSales > 0 ? ((totalProfits / totalSales) * 100).toFixed(1) : 0;

    document.getElementById('totalSales').textContent = formatCurrency(totalSales);
    document.getElementById('totalCosts').textContent = formatCurrency(totalCosts);
    document.getElementById('totalProfits').textContent = formatCurrency(totalProfits);
    document.getElementById('totalMargin').textContent = totalMargin + '%';
}

/**
 * Funciones auxiliares
 */
function getSelectedChartType() {
    const activeBtn = document.querySelector('.btn-chart-type.active');
    return activeBtn ? activeBtn.dataset.chart : 'line';
}

function getProductColor(index) {
    const colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
    return colors[index % colors.length];
}

function formatCurrency(value) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(value || 0);
}

/**
 * Event listeners del dashboard
 */
document.addEventListener('DOMContentLoaded', function () {

    // Cambio de semana
    document.getElementById('weekSelector').addEventListener('change', function () {
        if (!dashboardData) return;

        const selectedWeek = this.value;
        console.log(`🔄 Cambiando a: ${selectedWeek}`);

        // Resetear selector de productos
        document.getElementById('productSelector').value = 'all';

        if (selectedWeek === 'all') {
            showAllWeeksChart();
            document.getElementById('productSelectorGroup').style.display = 'none';
        } else {
            showWeekChart(selectedWeek);
            document.getElementById('productSelectorGroup').style.display = 'block';
        }

        currentWeek = selectedWeek;
        currentProduct = 'all';
    });

    // NUEVO: Cambio de producto (CORREGIDO)
    document.getElementById('productSelector').addEventListener('change', function () {
        if (!dashboardData) return;

        const selectedProduct = this.value;
        console.log(`🔄 Producto seleccionado: ${selectedProduct}`);

        currentProduct = selectedProduct;

        if (selectedProduct === 'all') {
            // Si estamos en una semana específica, mostrar productos de esa semana
            if (currentWeek !== 'all') {
                showWeekChart(currentWeek);
            } else {
                // Si estamos en vista general, mostrar vista general
                showAllWeeksChart();
            }
        } else {
            // Mostrar análisis del producto específico
            showProductAnalysis(selectedProduct);
        }
    });

    // Cambio de tipo de gráfico (CORREGIDO)
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!dashboardData) return;

            console.log(`🎨 Cambiando a: ${this.dataset.chart}`);

            // Recrear gráfico según la vista actual
            if (currentProduct !== 'all') {
                // Vista de producto específico
                showProductAnalysis(currentProduct);
            } else if (currentWeek === 'all') {
                // Vista general de semanas
                showAllWeeksChart();
            } else {
                // Vista de semana específica
                showWeekChart(currentWeek);
            }
        });
    });

    // Botón actualizar
    document.getElementById('btnRefreshData').addEventListener('click', function () {
        console.log('🔄 Actualizando...');

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        if (weeklyChart) weeklyChart.destroy();
        if (productChart) productChart.destroy();
        weeklyChart = null;
        productChart = null;

        loadDashboardData().finally(() => {
            this.innerHTML = '<i class="fas fa-sync"></i>';
            this.disabled = false;
        });
    });
});

// Funciones globales
window.loadDashboardData = loadDashboardData;