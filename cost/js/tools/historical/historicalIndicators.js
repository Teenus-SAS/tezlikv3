/**
 * Dashboard de Ganancias Semanales
 * Archivo: dashboard-charts.js
 * 
 * Este archivo maneja toda la lógica de gráficos y análisis de ganancias por semana
 */

// Variables globales
let dashboardData = null;
let weeklyChart = null;
let productChart = null;
let distributionChart = null;
let currentWeek = 'all';
let currentProduct = 'all';

/**
 * Función principal para inicializar el dashboard
 */
async function initializeProfitsDashboard() {
    try {
        console.log('🚀 Inicializando dashboard de ganancias...');

        // Mostrar indicador de carga en el select
        showLoadingInSelect('weekSelector', 'Cargando semanas...');

        // Obtener datos del servidor
        const response = await fetch('/api/dataHistorical');

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        console.log('📊 Datos recibidos del servidor:', data);

        // Procesar datos
        dashboardData = processWeeklyData(data);

        if (!dashboardData || !dashboardData.weeks.length) {
            throw new Error('No se encontraron datos válidos para procesar');
        }

        console.log('✅ Datos procesados correctamente:', dashboardData);

        // Llenar selectores
        populateWeekSelector();
        populateProductSelector();

        // Mostrar vista inicial (todas las semanas)
        showAllWeeksView();

        console.log('🎉 Dashboard inicializado exitosamente');

    } catch (error) {
        console.error('❌ Error al inicializar dashboard:', error);
        showErrorInDashboard(error.message);
    }
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
 */
document.addEventListener('DOMContentLoaded', function () {

    // Listener para cambio de semana
    document.getElementById('weekSelector').addEventListener('change', function () {
        const selectedWeek = this.value;
        console.log(`🔄 Cambiando a semana: ${selectedWeek}`);

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

        this.disabled = true;
        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Reinicializar dashboard
        initializeProfitsDashboard().finally(() => {
            this.innerHTML = originalHTML;
            this.disabled = false;
        });
    });

    // Listener para botón de exportar
    document.getElementById('btnExportData').addEventListener('click', function () {
        console.log('📤 Exportando datos...');
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