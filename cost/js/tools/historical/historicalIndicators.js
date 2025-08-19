/**
 * Dashboard de Ganancias Semanales
 * Archivo: dashboard-charts.js
 * 
 * SOLUCIÓN ESPECÍFICA PARA:
 * 1. Cargar datos automáticamente al hacer clic en btnGraphic
 * 2. Controlar tamaño de gráficos para evitar crecimiento infinito
 */

// Variables globales
let dashboardData = null;
let weeklyChart = null;
let productChart = null;
let currentWeek = 'all';
let currentProduct = 'all';

/**
 * SOLUCIÓN 1: Función que se ejecuta AUTOMÁTICAMENTE al hacer clic en btnGraphic
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

        // Calcular costos y ganancia
        const costs = (parseFloat(item.cost_material) || 0) +
            (parseFloat(item.cost_workforce) || 0) +
            (parseFloat(item.cost_indirect) || 0) +
            (parseFloat(item.assignable_expense) || 0);

        const revenue = parseFloat(item.turnover) || 0;
        const profit = revenue - costs;
        const units = parseInt(item.units_sold) || 0;

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
    const topProducts = Object.values(productTotals)
        .sort((a, b) => b.totalProfit - a.totalProfit)
        .slice(0, 10);

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

    dashboardData.topProducts.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = `${product.name} - ${formatCurrency(product.totalProfit)}`;
        select.appendChild(option);
    });
}

/**
 * SOLUCIÓN 2: Crear gráfico con tamaño FIJO y controlado
 * ACTUALIZADO: Mostrar COSTOS, VENTAS y GANANCIAS
 */
function showAllWeeksChart() {
    const container = document.querySelector('.cardDashboard .container-fluid');

    // Limpiar contenedor
    container.innerHTML = '';

    // Crear estructura con tamaño FIJO
    container.innerHTML = `
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
        
        <!-- Resumen de métricas -->
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
    `;

    // Crear gráfico con las tres métricas
    createTripleMetricsChart();

    // Actualizar métricas de resumen
    updateSummaryMetrics();
}

/**
 * Crear gráfico de semana específica con métricas completas
 */
function showWeekChart(weekId) {
    const weekData = dashboardData.weeklyData[weekId];
    if (!weekData) return;

    const container = document.querySelector('.cardDashboard .container-fluid');

    // Limpiar contenedor
    container.innerHTML = '';

    // Crear estructura para productos de la semana con métricas
    container.innerHTML = `
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
                            <!-- Lista se llena dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Métricas de la semana -->
        <div class="row">
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
    `;

    // Crear gráfico de productos con métricas
    createProductsMetricsChart(weekData);

    // Llenar lista de productos
    fillWeekProductsList(weekData);
}

/**
 * NUEVO: Gráfico con VENTAS, COSTOS y GANANCIAS
 */
function createTripleMetricsChart() {
    // IMPORTANTE: Destruir gráfico anterior
    if (weeklyChart) {
        weeklyChart.destroy();
        weeklyChart = null;
    }

    const canvas = document.getElementById('weeklyChart');
    if (!canvas) return;

    // SOLUCIÓN: Establecer tamaño fijo del canvas
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
                    borderColor: '#10b981', // Verde
                    backgroundColor: chartType === 'line' ? 'rgba(16, 185, 129, 0.1)' : '#10b981',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                },
                {
                    label: '📊 Costos',
                    data: dashboardData.weeks.map(w => w.totalCosts),
                    borderColor: '#ef4444', // Rojo
                    backgroundColor: chartType === 'line' ? 'rgba(239, 68, 68, 0.1)' : '#ef4444',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                },
                {
                    label: '⭐ Ganancias',
                    data: dashboardData.weeks.map(w => w.totalProfit),
                    borderColor: '#3b82f6', // Azul
                    backgroundColor: chartType === 'line' ? 'rgba(59, 130, 246, 0.1)' : '#3b82f6',
                    borderWidth: 4, // Línea más gruesa para destacar
                    fill: chartType === 'area',
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Análisis Financiero Completo por Semana',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            return `Semana: ${context[0].label}`;
                        },
                        label: function (context) {
                            const week = dashboardData.weeks[context.dataIndex];
                            const dataset = context.dataset.label;
                            const value = formatCurrency(context.parsed.y);

                            if (dataset.includes('Ventas')) {
                                return `💰 Ventas: ${value}`;
                            } else if (dataset.includes('Costos')) {
                                return `📊 Costos: ${value}`;
                            } else if (dataset.includes('Ganancias')) {
                                const margin = ((week.totalProfit / week.totalRevenue) * 100).toFixed(1);
                                return [`⭐ Ganancias: ${value}`, `📈 Margen: ${margin}%`];
                            }
                        },
                        footer: function (context) {
                            const week = dashboardData.weeks[context[0].dataIndex];
                            return `🏭 Productos: ${Object.keys(week.products).length}`;
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
                    },
                    title: {
                        display: true,
                        text: 'Valor ($)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Semanas'
                    }
                }
            }
        }
    });

    console.log('📈 Gráfico financiero completo creado (Ventas, Costos, Ganancias)');
}

/**
 * NUEVO: Gráfico de productos con métricas completas
 */
function createProductsMetricsChart(weekData) {
    // IMPORTANTE: Destruir gráfico anterior
    if (productChart) {
        productChart.destroy();
        productChart = null;
    }

    const canvas = document.getElementById('productChart');
    if (!canvas) return;

    // SOLUCIÓN: Establecer tamaño fijo del canvas
    canvas.width = canvas.offsetWidth;
    canvas.height = 400;
    canvas.style.width = '100%';
    canvas.style.height = '400px';

    // Top productos de la semana
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
                    borderColor: '#059669',
                    borderWidth: 1,
                    maxBarThickness: 40
                },
                {
                    label: '📊 Costos',
                    data: products.map(p => p.revenue - p.profit), // Costos = Ventas - Ganancias
                    backgroundColor: '#ef4444',
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    maxBarThickness: 40
                },
                {
                    label: '⭐ Ganancias',
                    data: products.map(p => p.profit),
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    maxBarThickness: 40
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                title: {
                    display: true,
                    text: `Análisis por Producto - ${weekData.week}`,
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
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
                            const product = products[context.dataIndex];
                            const costs = product.revenue - product.profit;
                            const margin = product.revenue > 0 ? ((product.profit / product.revenue) * 100).toFixed(1) : 0;

                            if (context.dataset.label.includes('Ventas')) {
                                return `💰 Ventas: ${formatCurrency(product.revenue)}`;
                            } else if (context.dataset.label.includes('Costos')) {
                                return `📊 Costos: ${formatCurrency(costs)}`;
                            } else if (context.dataset.label.includes('Ganancias')) {
                                return [`⭐ Ganancias: ${formatCurrency(product.profit)}`, `📈 Margen: ${margin}%`];
                            }
                        },
                        footer: function (context) {
                            const product = products[context[0].dataIndex];
                            return `📦 Unidades: ${product.units}`;
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
                    },
                    title: {
                        display: true,
                        text: 'Valor ($)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            }
        }
    });

    console.log('📊 Gráfico de productos con métricas completas creado');
}

/**
 * NUEVO: Actualizar métricas de resumen
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
 * NUEVO: Llenar lista de productos de la semana
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
 * SOLUCIÓN 1: Event listeners con carga automática
 */
document.addEventListener('DOMContentLoaded', function () {

    // SOLUCIÓN: Cargar datos automáticamente al hacer clic en Gráficos
    document.getElementById('btnGraphic').addEventListener('click', function () {
        console.log('🎯 Botón Gráficos - Cargando dashboard...');

        // Mostrar dashboard
        document.querySelector('.cardHistoricalResume').style.display = 'none';
        document.querySelector('.cardHistoricalProducts').style.display = 'none';
        document.querySelector('.cardDashboard').style.display = 'block';

        // Mostrar controles
        const controls = document.getElementById('analysisControls');
        controls.style.display = 'block';
        controls.classList.add('slide-in');

        // AUTOMÁTICO: Cargar datos inmediatamente
        loadDashboardData();
    });

    // Botón Lista - Limpiar todo
    document.getElementById('btnList').addEventListener('click', function () {
        console.log('📋 Volviendo a lista - Limpiando dashboard');

        // Destruir gráficos
        if (weeklyChart) {
            weeklyChart.destroy();
            weeklyChart = null;
        }
        if (productChart) {
            productChart.destroy();
            productChart = null;
        }

        // Limpiar datos
        dashboardData = null;

        // Mostrar lista
        document.querySelector('.cardDashboard').style.display = 'none';
        document.getElementById('analysisControls').style.display = 'none';
        document.querySelector('.cardHistoricalResume').style.display = 'block';
    });

    // Cambio de semana
    document.getElementById('weekSelector').addEventListener('change', function () {
        if (!dashboardData) return;

        const selectedWeek = this.value;
        console.log(`🔄 Cambiando a: ${selectedWeek}`);

        if (selectedWeek === 'all') {
            showAllWeeksChart();
            document.getElementById('productSelectorGroup').style.display = 'none';
        } else {
            showWeekChart(selectedWeek);
            document.getElementById('productSelectorGroup').style.display = 'block';
        }

        currentWeek = selectedWeek;
    });

    // Cambio de tipo de gráfico
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!dashboardData) return;

            console.log(`🎨 Cambiando a: ${this.dataset.chart}`);

            if (currentWeek === 'all') {
                showAllWeeksChart();
            } else {
                showWeekChart(currentWeek);
            }
        });
    });

    // Botón actualizar
    document.getElementById('btnRefreshData').addEventListener('click', function () {
        console.log('🔄 Actualizando...');

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Limpiar gráficos
        if (weeklyChart) weeklyChart.destroy();
        if (productChart) productChart.destroy();
        weeklyChart = null;
        productChart = null;

        // Recargar datos
        loadDashboardData().finally(() => {
            this.innerHTML = '<i class="fas fa-sync"></i>';
            this.disabled = false;
        });
    });
});

/**
 * Función global para acceso externo
 */
window.loadDashboardData = loadDashboardData;

/**
 * Procesar datos del servidor para análisis semanal
 */
function processWeeklyData(data) {
    console.log('⚙️ Procesando datos semanales...');

    if (!data || data.length !== 3) {
        throw new Error('Formato de datos inválido - se esperan 3 arrays');
    }

    const [expenses, distributions, historical] = data;

    if (!historical || !Array.isArray(historical)) {
        throw new Error('Datos históricos no válidos');
    }

    const weeklyData = {};
    const productsByWeek = {};
    const productTotals = {};

    // Procesar cada registro histórico
    historical.forEach(item => {
        const week = item.month; // W01, W02, etc.
        const productId = item.id_product;

        // Obtener nombre del producto
        const productName = item.product_name ||
            item.product ||
            item.name ||
            `Producto ${productId}`;

        // Calcular costos totales
        const costs = (parseFloat(item.cost_material) || 0) +
            (parseFloat(item.cost_workforce) || 0) +
            (parseFloat(item.cost_indirect) || 0) +
            (parseFloat(item.assignable_expense) || 0) +
            (parseFloat(item.external_services) || 0);

        // Calcular ingresos y ganancia
        const revenue = parseFloat(item.turnover) || 0;
        const profit = revenue - costs;
        const units = parseInt(item.units_sold) || 0;

        // Agrupar por semana
        if (!weeklyData[week]) {
            weeklyData[week] = {
                week: week,
                totalProfit: 0,
                totalRevenue: 0,
                totalCosts: 0,
                totalUnits: 0,
                productCount: 0,
                products: {}
            };
        }

        // Acumular totales de la semana
        weeklyData[week].totalProfit += profit;
        weeklyData[week].totalRevenue += revenue;
        weeklyData[week].totalCosts += costs;
        weeklyData[week].totalUnits += units;

        // Agregar producto a la semana
        if (!weeklyData[week].products[productId]) {
            weeklyData[week].products[productId] = {
                id: productId,
                name: productName,
                profit: 0,
                revenue: 0,
                costs: 0,
                units: 0
            };
            weeklyData[week].productCount++;
        }

        weeklyData[week].products[productId].profit += profit;
        weeklyData[week].products[productId].revenue += revenue;
        weeklyData[week].products[productId].costs += costs;
        weeklyData[week].products[productId].units += units;

        // Totales por producto (para ranking global)
        if (!productTotals[productId]) {
            productTotals[productId] = {
                id: productId,
                name: productName,
                totalProfit: 0,
                totalRevenue: 0,
                weeks: []
            };
        }

        productTotals[productId].totalProfit += profit;
        productTotals[productId].totalRevenue += revenue;

        if (!productTotals[productId].weeks.includes(week)) {
            productTotals[productId].weeks.push(week);
        }
    });

    // Convertir a arrays ordenados
    const weeks = Object.values(weeklyData)
        .sort((a, b) => a.week.localeCompare(b.week))
        .map(week => ({
            ...week,
            margin: week.totalRevenue > 0 ?
                ((week.totalProfit / week.totalRevenue) * 100).toFixed(2) : 0
        }));

    const topProducts = Object.values(productTotals)
        .sort((a, b) => b.totalProfit - a.totalProfit)
        .slice(0, 15); // Top 15 productos

    const stats = {
        totalWeeks: weeks.length,
        totalProducts: Object.keys(productTotals).length,
        totalProfit: weeks.reduce((sum, week) => sum + week.totalProfit, 0),
        totalRevenue: weeks.reduce((sum, week) => sum + week.totalRevenue, 0),
        bestWeek: weeks.reduce((max, week) =>
            week.totalProfit > max.totalProfit ? week : max, weeks[0]),
        averageWeeklyProfit: 0
    };

    stats.averageWeeklyProfit = stats.totalProfit / stats.totalWeeks;

    console.log(`📈 Procesados: ${weeks.length} semanas, ${stats.totalProducts} productos`);

    return {
        weeks,
        topProducts,
        stats,
        weeklyData
    };
}

/**
 * Llenar el selector de semanas
 */
function populateWeekSelector() {
    const select = document.getElementById('weekSelector');

    // Limpiar opciones existentes
    select.innerHTML = '<option value="all">📊 Todas las Semanas</option>';
    select.classList.remove('select-loading');

    // Agregar cada semana con su ganancia
    dashboardData.weeks.forEach(week => {
        const option = document.createElement('option');
        option.value = week.week;
        option.textContent = `📅 ${week.week} - ${formatCurrency(week.totalProfit)} (${week.productCount} productos)`;
        select.appendChild(option);
    });

    console.log(`📝 Selector poblado con ${dashboardData.weeks.length} semanas`);
}

/**
 * Llenar el selector de productos
 */
function populateProductSelector() {
    const select = document.getElementById('productSelector');

    // Limpiar opciones existentes
    select.innerHTML = '<option value="all">📦 Todos los Productos</option>';

    // Agregar top productos
    dashboardData.topProducts.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = `${product.name} - ${formatCurrency(product.totalProfit)}`;
        select.appendChild(option);
    });

    console.log(`📝 Selector poblado con ${dashboardData.topProducts.length} productos`);
}

/**
 * Mostrar vista de todas las semanas
 */
function showAllWeeksView() {
    console.log('📊 Mostrando vista de todas las semanas');

    currentWeek = 'all';

    // Crear gráfico principal de semanas
    createWeeklyChart();

    // Mostrar estadísticas generales
    updateDashboardStats(dashboardData.stats);

    // Ocultar selector de productos
    document.getElementById('productSelectorGroup').style.display = 'none';
}

/**
 * Mostrar vista de una semana específica
 */
function showWeekView(weekId) {
    console.log(`📅 Mostrando vista de la semana: ${weekId}`);

    currentWeek = weekId;
    const weekData = dashboardData.weeklyData[weekId];

    if (!weekData) {
        console.error('❌ Datos de semana no encontrados');
        return;
    }

    // Crear gráfico de productos de la semana
    createProductChart(weekData);

    // Mostrar estadísticas de la semana
    const weekStats = {
        totalWeeks: 1,
        totalProducts: weekData.productCount,
        totalProfit: weekData.totalProfit,
        totalRevenue: weekData.totalRevenue,
        averageWeeklyProfit: weekData.totalProfit
    };

    updateDashboardStats(weekStats);

    // Mostrar selector de productos
    document.getElementById('productSelectorGroup').style.display = 'block';
}

/**
 * Crear gráfico de evolución semanal
 * FIJO: Tamaño controlado para evitar crecimiento infinito
 */
function createWeeklyChart() {
    const canvas = document.getElementById('weeklyChart');
    if (!canvas) {
        console.error('❌ Canvas weeklyChart no encontrado');
        return;
    }

    // FIJO: Destruir gráfico anterior completamente
    if (weeklyChart) {
        weeklyChart.destroy();
        weeklyChart = null;
    }

    // FIJO: Resetear tamaño del canvas
    canvas.style.width = '100%';
    canvas.style.height = '400px';
    canvas.width = canvas.offsetWidth;
    canvas.height = 400;

    const chartData = {
        labels: dashboardData.weeks.map(w => w.week),
        datasets: [{
            label: 'Ganancia Semanal',
            data: dashboardData.weeks.map(w => w.totalProfit),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6
        }]
    };

    const config = {
        type: getSelectedChartType(),
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false, // FIJO: Mantener aspecto fijo
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución de Ganancias por Semana',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const week = dashboardData.weeks[context.dataIndex];
                            return [
                                `Ganancia: ${formatCurrency(context.parsed.y)}`,
                                `Productos: ${week.productCount}`,
                                `Margen: ${week.margin}%`
                            ];
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
                    },
                    title: {
                        display: true,
                        text: 'Ganancia ($)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Semanas'
                    }
                }
            },
            // FIJO: Controlar tamaño específicamente
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            }
        }
    };

    weeklyChart = new Chart(canvas, config);
    console.log('📈 Gráfico semanal creado con tamaño controlado');
}

/**
 * Crear gráfico de productos por semana
 * FIJO: Tamaño controlado para evitar crecimiento infinito
 */
function createProductChart(weekData) {
    const canvas = document.getElementById('productChart');
    if (!canvas) {
        console.error('❌ Canvas productChart no encontrado');
        return;
    }

    // FIJO: Destruir gráfico anterior completamente
    if (productChart) {
        productChart.destroy();
        productChart = null;
    }

    // FIJO: Resetear tamaño del canvas
    canvas.style.width = '100%';
    canvas.style.height = '300px';
    canvas.width = canvas.offsetWidth;
    canvas.height = 300;

    // Obtener top 10 productos de la semana
    const products = Object.values(weekData.products)
        .sort((a, b) => b.profit - a.profit)
        .slice(0, 10);

    const chartData = {
        labels: products.map(p => p.name.length > 20 ? p.name.substring(0, 20) + '...' : p.name),
        datasets: [{
            label: 'Ganancia por Producto',
            data: products.map(p => p.profit),
            backgroundColor: products.map((_, i) => getProductColor(i)),
            borderColor: products.map((_, i) => getProductColor(i)),
            borderWidth: 2,
            maxBarThickness: 50 // FIJO: Limitar grosor de barras
        }]
    };

    const config = {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false, // FIJO: Mantener aspecto fijo
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                title: {
                    display: true,
                    text: `Productos de la ${weekData.week}`,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            return products[context[0].dataIndex].name;
                        },
                        label: function (context) {
                            const product = products[context.dataIndex];
                            const margin = product.revenue > 0 ?
                                ((product.profit / product.revenue) * 100).toFixed(2) : 0;
                            return [
                                `Ganancia: ${formatCurrency(product.profit)}`,
                                `Ingresos: ${formatCurrency(product.revenue)}`,
                                `Unidades: ${product.units}`,
                                `Margen: ${margin}%`
                            ];
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
                    },
                    title: {
                        display: true,
                        text: 'Ganancia ($)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            // FIJO: Controlar tamaño específicamente
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            }
        }
    };

    productChart = new Chart(canvas, config);
    console.log(`📊 Gráfico de productos creado para ${weekData.week} con tamaño controlado`);
}

/**
 * Procesar datos del servidor para análisis semanal
 */
function processWeeklyData(data) {
    console.log('⚙️ Procesando datos semanales...');

    if (!data || data.length !== 3) {
        throw new Error('Formato de datos inválido - se esperan 3 arrays');
    }

    const [expenses, distributions, historical] = data;

    if (!historical || !Array.isArray(historical)) {
        throw new Error('Datos históricos no válidos');
    }

    const weeklyData = {};
    const productsByWeek = {};
    const productTotals = {};

    // Procesar cada registro histórico
    historical.forEach(item => {
        const week = item.month; // W01, W02, etc.
        const productId = item.id_product;

        // Obtener nombre del producto
        const productName = item.product_name ||
            item.product ||
            item.name ||
            `Producto ${productId}`;

        // Calcular costos totales
        const costs = (parseFloat(item.cost_material) || 0) +
            (parseFloat(item.cost_workforce) || 0) +
            (parseFloat(item.cost_indirect) || 0) +
            (parseFloat(item.assignable_expense) || 0) +
            (parseFloat(item.external_services) || 0);

        // Calcular ingresos y ganancia
        const revenue = parseFloat(item.turnover) || 0;
        const profit = revenue - costs;
        const units = parseInt(item.units_sold) || 0;

        // Agrupar por semana
        if (!weeklyData[week]) {
            weeklyData[week] = {
                week: week,
                totalProfit: 0,
                totalRevenue: 0,
                totalCosts: 0,
                totalUnits: 0,
                productCount: 0,
                products: {}
            };
        }

        // Acumular totales de la semana
        weeklyData[week].totalProfit += profit;
        weeklyData[week].totalRevenue += revenue;
        weeklyData[week].totalCosts += costs;
        weeklyData[week].totalUnits += units;

        // Agregar producto a la semana
        if (!weeklyData[week].products[productId]) {
            weeklyData[week].products[productId] = {
                id: productId,
                name: productName,
                profit: 0,
                revenue: 0,
                costs: 0,
                units: 0
            };
            weeklyData[week].productCount++;
        }

        weeklyData[week].products[productId].profit += profit;
        weeklyData[week].products[productId].revenue += revenue;
        weeklyData[week].products[productId].costs += costs;
        weeklyData[week].products[productId].units += units;

        // Totales por producto (para ranking global)
        if (!productTotals[productId]) {
            productTotals[productId] = {
                id: productId,
                name: productName,
                totalProfit: 0,
                totalRevenue: 0,
                weeks: []
            };
        }

        productTotals[productId].totalProfit += profit;
        productTotals[productId].totalRevenue += revenue;

        if (!productTotals[productId].weeks.includes(week)) {
            productTotals[productId].weeks.push(week);
        }
    });

    // Convertir a arrays ordenados
    const weeks = Object.values(weeklyData)
        .sort((a, b) => a.week.localeCompare(b.week))
        .map(week => ({
            ...week,
            margin: week.totalRevenue > 0 ?
                ((week.totalProfit / week.totalRevenue) * 100).toFixed(2) : 0
        }));

    const topProducts = Object.values(productTotals)
        .sort((a, b) => b.totalProfit - a.totalProfit)
        .slice(0, 15); // Top 15 productos

    const stats = {
        totalWeeks: weeks.length,
        totalProducts: Object.keys(productTotals).length,
        totalProfit: weeks.reduce((sum, week) => sum + week.totalProfit, 0),
        totalRevenue: weeks.reduce((sum, week) => sum + week.totalRevenue, 0),
        bestWeek: weeks.reduce((max, week) =>
            week.totalProfit > max.totalProfit ? week : max, weeks[0]),
        averageWeeklyProfit: 0
    };

    stats.averageWeeklyProfit = stats.totalProfit / stats.totalWeeks;

    console.log(`📈 Procesados: ${weeks.length} semanas, ${stats.totalProducts} productos`);

    return {
        weeks,
        topProducts,
        stats,
        weeklyData
    };
}

/**
 * Llenar el selector de semanas
 */
function populateWeekSelector() {
    const select = document.getElementById('weekSelector');

    // Limpiar opciones existentes
    select.innerHTML = '<option value="all">📊 Todas las Semanas</option>';

    // Agregar cada semana con su ganancia
    dashboardData.weeks.forEach(week => {
        const option = document.createElement('option');
        option.value = week.week;
        option.textContent = `📅 ${week.week} - ${formatCurrency(week.totalProfit)} (${week.productCount} productos)`;
        select.appendChild(option);
    });

    console.log(`📝 Selector poblado con ${dashboardData.weeks.length} semanas`);
}

/**
 * Llenar el selector de productos
 */
function populateProductSelector() {
    const select = document.getElementById('productSelector');

    // Limpiar opciones existentes
    select.innerHTML = '<option value="all">📦 Todos los Productos</option>';

    // Agregar top productos
    dashboardData.topProducts.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = `${product.name} - ${formatCurrency(product.totalProfit)}`;
        select.appendChild(option);
    });

    console.log(`📝 Selector poblado con ${dashboardData.topProducts.length} productos`);
}

/**
 * Mostrar vista de todas las semanas
 */
function showAllWeeksView() {
    console.log('📊 Mostrando vista de todas las semanas');

    currentWeek = 'all';

    // Crear gráfico principal de semanas
    createWeeklyChart();

    // Mostrar estadísticas generales
    updateDashboardStats(dashboardData.stats);

    // Ocultar selector de productos
    document.getElementById('productSelectorGroup').style.display = 'none';
}

/**
 * Mostrar vista de una semana específica
 */
function showWeekView(weekId) {
    console.log(`📅 Mostrando vista de la semana: ${weekId}`);

    currentWeek = weekId;
    const weekData = dashboardData.weeklyData[weekId];

    if (!weekData) {
        console.error('❌ Datos de semana no encontrados');
        return;
    }

    // Crear gráfico de productos de la semana
    createProductChart(weekData);

    // Mostrar estadísticas de la semana
    const weekStats = {
        totalWeeks: 1,
        totalProducts: weekData.productCount,
        totalProfit: weekData.totalProfit,
        totalRevenue: weekData.totalRevenue,
        averageWeeklyProfit: weekData.totalProfit
    };

    updateDashboardStats(weekStats);

    // Mostrar selector de productos
    document.getElementById('productSelectorGroup').style.display = 'block';
}

/**
 * Crear gráfico de evolución semanal
 */
function createWeeklyChart() {
    const ctx = document.getElementById('weeklyChart');
    if (!ctx) {
        console.error('❌ Canvas weeklyChart no encontrado');
        return;
    }

    // Destruir gráfico anterior si existe
    if (weeklyChart) {
        weeklyChart.destroy();
    }

    const chartData = {
        labels: dashboardData.weeks.map(w => w.week),
        datasets: [{
            label: 'Ganancia Semanal',
            data: dashboardData.weeks.map(w => w.totalProfit),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6
        }]
    };

    const config = {
        type: getSelectedChartType(),
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución de Ganancias por Semana',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const week = dashboardData.weeks[context.dataIndex];
                            return [
                                `Ganancia: ${formatCurrency(context.parsed.y)}`,
                                `Productos: ${week.productCount}`,
                                `Margen: ${week.margin}%`
                            ];
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
                    },
                    title: {
                        display: true,
                        text: 'Ganancia ($)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Semanas'
                    }
                }
            }
        }
    };

    weeklyChart = new Chart(ctx, config);
    console.log('📈 Gráfico semanal creado');
}

/**
 * Crear gráfico de productos por semana
 */
function createProductChart(weekData) {
    const ctx = document.getElementById('productChart');
    if (!ctx) {
        console.error('❌ Canvas productChart no encontrado');
        return;
    }

    // Destruir gráfico anterior si existe
    if (productChart) {
        productChart.destroy();
    }

    // Obtener top 10 productos de la semana
    const products = Object.values(weekData.products)
        .sort((a, b) => b.profit - a.profit)
        .slice(0, 10);

    const chartData = {
        labels: products.map(p => p.name.length > 20 ? p.name.substring(0, 20) + '...' : p.name),
        datasets: [{
            label: 'Ganancia por Producto',
            data: products.map(p => p.profit),
            backgroundColor: products.map((_, i) => getProductColor(i)),
            borderColor: products.map((_, i) => getProductColor(i)),
            borderWidth: 2
        }]
    };

    const config = {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: `Productos de la ${weekData.week}`,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            return products[context[0].dataIndex].name;
                        },
                        label: function (context) {
                            const product = products[context.dataIndex];
                            const margin = product.revenue > 0 ?
                                ((product.profit / product.revenue) * 100).toFixed(2) : 0;
                            return [
                                `Ganancia: ${formatCurrency(product.profit)}`,
                                `Ingresos: ${formatCurrency(product.revenue)}`,
                                `Unidades: ${product.units}`,
                                `Margen: ${margin}%`
                            ];
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
                    },
                    title: {
                        display: true,
                        text: 'Ganancia ($)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    };

    productChart = new Chart(ctx, config);
    console.log(`📊 Gráfico de productos creado para ${weekData.week}`);
}

/**
 * Actualizar estadísticas del dashboard
 */
function updateDashboardStats(stats) {
    // Actualizar KPIs si existen los elementos
    const elements = {
        'totalProfitValue': formatCurrency(stats.totalProfit),
        'weekCountValue': stats.totalWeeks,
        'productCountValue': stats.totalProducts,
        'avgWeeklyValue': formatCurrency(stats.averageWeeklyProfit)
    };

    Object.entries(elements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    });

    console.log('📊 Estadísticas actualizadas');
}

/**
 * Obtener tipo de gráfico seleccionado
 */
function getSelectedChartType() {
    const activeButton = document.querySelector('.btn-chart-type.active');
    return activeButton ? activeButton.dataset.chart : 'line';
}

/**
 * Obtener color para productos
 */
function getProductColor(index) {
    const colors = [
        '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
        '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
        '#14b8a6', '#f43f5e', '#8b5cf6', '#06b6d4', '#84cc16'
    ];
    return colors[index % colors.length];
}

/**
 * Formatear moneda en pesos colombianos
 */
function formatCurrency(value) {
    if (isNaN(value)) return '$0';

    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

/**
 * Mostrar indicador de carga en select
 */
function showLoadingInSelect(selectId, message) {
    const select = document.getElementById(selectId);
    if (select) {
        select.innerHTML = `<option value="">${message}</option>`;
        select.classList.add('select-loading');
    }
}

/**
 * Mostrar error en el dashboard
 */
function showErrorInDashboard(message) {
    const dashboardContainer = document.querySelector('.cardDashboard .container-fluid');
    if (dashboardContainer) {
        dashboardContainer.innerHTML = `
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        <h4><i class="fas fa-exclamation-triangle"></i> Error al cargar el dashboard</h4>
                        <p>${message}</p>
                        <button class="btn btn-primary" onclick="initializeProfitsDashboard()">
                            <i class="fas fa-redo"></i> Intentar nuevamente
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
}

/**
 * Event Listeners para los controles del dashboard
 * FIJO: Inicialización automática al hacer clic en btnGraphic
 */
document.addEventListener('DOMContentLoaded', function () {

    // FIJO: Listener para el botón de gráficos (inicializa automáticamente)
    document.getElementById('btnGraphic').addEventListener('click', function () {
        console.log('🎯 Botón Gráficos clickeado - Inicializando dashboard...');

        // Mostrar dashboard y controles
        document.querySelector('.cardHistoricalResume').style.display = 'none';
        document.querySelector('.cardHistoricalProducts').style.display = 'none';
        document.querySelector('.cardDashboard').style.display = 'block';

        // Mostrar controles con animación
        const controls = document.getElementById('analysisControls');
        controls.style.display = 'block';
        controls.classList.add('slide-in');

        // FIJO: Inicializar dashboard automáticamente
        initializeProfitsDashboard();
    });

    // Listener para el botón de lista
    document.getElementById('btnList').addEventListener('click', function () {
        console.log('📋 Botón Lista clickeado');

        // Ocultar dashboard y controles
        document.querySelector('.cardDashboard').style.display = 'none';
        document.querySelector('.cardHistoricalProducts').style.display = 'none';
        document.getElementById('analysisControls').style.display = 'none';

        // Mostrar lista
        document.querySelector('.cardHistoricalResume').style.display = 'block';

        // FIJO: Limpiar gráficos al salir del dashboard
        if (weeklyChart) {
            weeklyChart.destroy();
            weeklyChart = null;
        }
        if (productChart) {
            productChart.destroy();
            productChart = null;
        }

        // Resetear estado
        isInitialized = false;
        dashboardData = null;

        console.log('🧹 Dashboard limpiado');
    });

    // Listener para cambio de semana
    document.getElementById('weekSelector').addEventListener('change', function () {
        const selectedWeek = this.value;
        console.log(`🔄 Cambiando a semana: ${selectedWeek}`);

        if (!dashboardData) {
            console.warn('⚠️ Dashboard no inicializado');
            return;
        }

        if (selectedWeek === 'all') {
            showAllWeeksView();
        } else {
            showWeekView(selectedWeek);
        }
    });

    // Listener para cambio de producto
    document.getElementById('productSelector').addEventListener('change', function () {
        const selectedProduct = this.value;
        console.log(`🔄 Producto seleccionado: ${selectedProduct}`);

        // Aquí se puede agregar lógica para filtrar por producto específico
        currentProduct = selectedProduct;
    });

    // Listeners para botones de tipo de gráfico
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        btn.addEventListener('click', function () {
            console.log(`🎨 Cambiando tipo de gráfico a: ${this.dataset.chart}`);

            if (!dashboardData) {
                console.warn('⚠️ Dashboard no inicializado');
                return;
            }

            // Recrear gráfico con nuevo tipo
            if (currentWeek === 'all') {
                createWeeklyChart();
            } else {
                const weekData = dashboardData.weeklyData[currentWeek];
                if (weekData) {
                    createProductChart(weekData);
                }
            }
        });
    });

    // Listener para botón de actualizar
    document.getElementById('btnRefreshData').addEventListener('click', function () {
        console.log('🔄 Actualizando datos del dashboard...');

        if (!isInitialized) {
            console.warn('⚠️ Dashboard no inicializado, iniciando...');
            initializeProfitsDashboard();
            return;
        }

        this.disabled = true;
        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // FIJO: Limpiar gráficos antes de reinicializar
        if (weeklyChart) {
            weeklyChart.destroy();
            weeklyChart = null;
        }
        if (productChart) {
            productChart.destroy();
            productChart = null;
        }

        // Reinicializar dashboard
        initializeProfitsDashboard().finally(() => {
            this.innerHTML = originalHTML;
            this.disabled = false;
        });
    });

    // Listener para botón de exportar
    document.getElementById('btnExportData').addEventListener('click', function () {
        console.log('📤 Exportando datos...');

        if (!dashboardData) {
            alert('No hay datos para exportar. Inicializa el dashboard primero.');
            return;
        }

        exportDashboardData();
    });
});

/**
 * Función para exportar datos a CSV
 */
function exportDashboardData() {
    if (!dashboardData) {
        alert('No hay datos para exportar');
        return;
    }

    let csvData;
    let filename;

    if (currentWeek === 'all') {
        // Exportar datos de todas las semanas
        csvData = dashboardData.weeks.map(week => ({
            Semana: week.week,
            Ganancia: week.totalProfit,
            Ingresos: week.totalRevenue,
            Costos: week.totalCosts,
            Unidades: week.totalUnits,
            Productos: week.productCount,
            Margen: week.margin + '%'
        }));
        filename = 'ganancias_semanales.csv';
    } else {
        // Exportar datos de la semana seleccionada
        const weekData = dashboardData.weeklyData[currentWeek];
        csvData = Object.values(weekData.products).map(product => ({
            Semana: currentWeek,
            Producto: product.name,
            Ganancia: product.profit,
            Ingresos: product.revenue,
            Costos: product.costs,
            Unidades: product.units
        }));
        filename = `productos_${currentWeek}.csv`;
    }

    downloadCSV(csvData, filename);
}

/**
 * Función para descargar CSV
 */
function downloadCSV(data, filename) {
    const csvContent = convertToCSV(data);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');

    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        console.log(`📁 Archivo ${filename} descargado`);
    }
}

/**
 * Convertir datos a formato CSV
 */
function convertToCSV(data) {
    if (!data.length) return '';

    const headers = Object.keys(data[0]);
    const csvHeaders = headers.join(',');
    const csvRows = data.map(row =>
        headers.map(header => `"${row[header]}"`).join(',')
    );

    return csvHeaders + '\n' + csvRows.join('\n');
}

/**
 * Función global para inicializar desde el HTML
 */
window.initializeProfitsDashboard = initializeProfitsDashboard;