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

        // Calcular costos TOTALES por tipo (costos unitarios * unidades vendidas)
        const units = parseInt(item.units_sold) || 0;

        const materialUnitCost = parseFloat(item.cost_material) || 0;
        const workforceUnitCost = parseFloat(item.cost_workforce) || 0;
        const indirectUnitCost = parseFloat(item.cost_indirect) || 0;
        const assignableUnitCost = parseFloat(item.assignable_expense) || 0;

        // Costos totales por tipo
        const materialCosts = materialUnitCost * units;
        const workforceCosts = workforceUnitCost * units;
        const indirectCosts = indirectUnitCost * units;
        const assignableCosts = assignableUnitCost * units;

        const totalCosts = materialCosts + workforceCosts + indirectCosts + assignableCosts;

        const revenue = parseFloat(item.turnover) || 0;
        const profit = revenue - totalCosts;

        // Agrupar por semana
        if (!weeklyData[week]) {
            weeklyData[week] = {
                week: week,
                totalProfit: 0,
                totalRevenue: 0,
                totalCosts: 0,
                totalUnits: 0,
                // Totales por tipo de costo por semana
                totalMaterialCosts: 0,
                totalWorkforceCosts: 0,
                totalIndirectCosts: 0,
                totalAssignableCosts: 0,
                products: {}
            };
        }

        weeklyData[week].totalProfit += profit;
        weeklyData[week].totalRevenue += revenue;
        weeklyData[week].totalCosts += totalCosts;
        weeklyData[week].totalUnits += units;

        // Sumar costos por tipo en la semana
        weeklyData[week].totalMaterialCosts += materialCosts;
        weeklyData[week].totalWorkforceCosts += workforceCosts;
        weeklyData[week].totalIndirectCosts += indirectCosts;
        weeklyData[week].totalAssignableCosts += assignableCosts;

        // Productos por semana
        if (!weeklyData[week].products[productId]) {
            weeklyData[week].products[productId] = {
                id: productId,
                name: productName,
                profit: 0,
                revenue: 0,
                units: 0,
                // NUEVO: Costos por tipo por producto
                materialCosts: 0,
                workforceCosts: 0,
                indirectCosts: 0,
                assignableCosts: 0
            };
        }

        weeklyData[week].products[productId].profit += profit;
        weeklyData[week].products[productId].revenue += revenue;
        weeklyData[week].products[productId].units += units;

        // NUEVO: Acumular costos por tipo por producto
        weeklyData[week].products[productId].materialCosts += materialCosts;
        weeklyData[week].products[productId].workforceCosts += workforceCosts;
        weeklyData[week].products[productId].indirectCosts += indirectCosts;
        weeklyData[week].products[productId].assignableCosts += assignableCosts;

        // Totales por producto (para análisis global de productos)
        if (!productTotals[productId]) {
            productTotals[productId] = {
                id: productId,
                name: productName,
                totalProfit: 0,
                // NUEVO: Totales por tipo de costo por producto
                totalMaterialCosts: 0,
                totalWorkforceCosts: 0,
                totalIndirectCosts: 0,
                totalAssignableCosts: 0
            };
        }
        productTotals[productId].totalProfit += profit;
        productTotals[productId].totalMaterialCosts += materialCosts;
        productTotals[productId].totalWorkforceCosts += workforceCosts;
        productTotals[productId].totalIndirectCosts += indirectCosts;
        productTotals[productId].totalAssignableCosts += assignableCosts;
    });

    // Convertir a arrays
    const weeks = Object.values(weeklyData).sort((a, b) => a.week.localeCompare(b.week));
    const topProducts = Object.values(productTotals)
        .sort((a, b) => b.totalProfit - a.totalProfit);

    console.log(`✅ Procesados ${weeks.length} semanas y ${topProducts.length} productos (con costos por tipo)`);

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

    // Verificar si Select2 está disponible
    if (typeof $.fn.select2 !== 'function') {
        console.warn('⚠️ Select2 no está disponible, usando selector normal');
        fillProductSelectorNormal();
        return;
    }

    // Destruir Select2 existente si existe
    if ($(select).hasClass('select2-hidden-accessible')) {
        $(select).select2('destroy');
    }

    select.innerHTML = '<option value="all">📦 Todos los Productos</option>';

    console.log(`📝 Cargando ${dashboardData.topProducts.length} productos en el selector...`);

    // Ordenar productos alfabéticamente por nombre
    const sortedProducts = [...dashboardData.topProducts].sort((a, b) =>
        a.name.localeCompare(b.name, 'es', { sensitivity: 'base' })
    );

    sortedProducts.forEach((product) => {
        const option = document.createElement('option');
        option.value = product.id;

        // Solo mostrar nombre y ganancia (sin numeración)
        option.textContent = `${product.name} - ${formatCurrency(product.totalProfit)}`;
        option.setAttribute('data-profit', product.totalProfit);

        select.appendChild(option);
    });

    // Inicializar Select2 con configuración personalizada
    $(select).select2({
        placeholder: '🔍 Buscar producto...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () {
                return "No se encontraron productos";
            },
            searching: function () {
                return "Buscando...";
            }
        },
        templateResult: function (data) {
            if (data.loading) {
                return data.text;
            }

            if (data.id === 'all') {
                return $('<span><i class="fas fa-chart-bar"></i> ' + data.text + '</span>');
            }

            const profit = $(data.element).attr('data-profit');
            if (profit) {
                return $(`
                    <div class="select2-product-result">
                        <div class="product-name">${data.text.split(' - ')[0]}</div>
                        <div class="product-profit text-muted small">${formatCurrency(profit)}</div>
                    </div>
                `);
            }

            return data.text;
        },
        templateSelection: function (data) {
            if (data.id === 'all') {
                return '📦 Todos los Productos';
            }
            return data.text.split(' - ')[0];
        }
    });

    // Agregar estilos CSS para Select2
    if (!document.getElementById('select2-custom-styles')) {
        const style = document.createElement('style');
        style.id = 'select2-custom-styles';
        style.textContent = `
            .select2-product-result { padding: 4px 0; }
            .product-name { font-weight: 500; color: #333; }
            .product-profit { color: #28a745 !important; font-size: 0.85em; }
            .select2-container--default .select2-selection--single {
                height: 38px; border: 1px solid #ced4da; border-radius: 0.375rem;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 36px; padding-left: 12px;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }
            .select2-dropdown { border: 1px solid #ced4da; border-radius: 0.375rem; }
            .select2-search--dropdown .select2-search__field {
                border: 1px solid #ced4da; border-radius: 0.375rem;
            }
        `;
        document.head.appendChild(style);
    }

    console.log(`✅ ${sortedProducts.length} productos cargados alfabéticamente con Select2`);
}

/**
 * SOLUCIÓN 2: FUNCIÓN DE RESPALDO SIN SELECT2
 */
function fillProductSelectorNormal() {
    const select = document.getElementById('productSelector');
    select.innerHTML = '<option value="all">📦 Todos los Productos</option>';

    console.log(`📝 Cargando ${dashboardData.topProducts.length} productos en el selector (sin Select2)...`);

    // Ordenar productos alfabéticamente por nombre
    const sortedProducts = [...dashboardData.topProducts].sort((a, b) =>
        a.name.localeCompare(b.name, 'es', { sensitivity: 'base' })
    );

    sortedProducts.forEach((product) => {
        const option = document.createElement('option');
        option.value = product.id;

        // Solo mostrar nombre y ganancia (sin numeración)
        option.textContent = `${product.name} - ${formatCurrency(product.totalProfit)}`;

        select.appendChild(option);
    });

    console.log(`✅ ${sortedProducts.length} productos cargados alfabéticamente`);
}
/**
 * Mostrar vista de todas las semanas
 */
function showAllWeeksChart() {
    const container = document.querySelector('.cardDashboard .container-fluid');
    container.innerHTML = '';

    container.innerHTML = `
        <!-- CARDS PRINCIPALES EN LA PARTE SUPERIOR -->
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

        <!-- CARDS MODERNOS DE COSTOS POR TIPO -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="modern-cost-card material-card">
                    <div class="card-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value" id="totalMaterial">$0</h4>
                        <p class="cost-label">Materia Prima</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card workforce-card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value" id="totalWorkforce">$0</h4>
                        <p class="cost-label">Mano de Obra</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card indirect-card">
                    <div class="card-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value" id="totalIndirect">$0</h4>
                        <p class="cost-label">Costos Indirectos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card assignable-card">
                    <div class="card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value" id="totalAssignable">$0</h4>
                        <p class="cost-label">Gastos Asignables</p>
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

    // Agregar estilos CSS modernos si no existen
    addModernCostCardStyles();

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
        <!-- CARDS PRINCIPALES DE LA SEMANA -->
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

        <!-- CARDS MODERNOS DE COSTOS DE LA SEMANA -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="modern-cost-card material-card week-card">
                    <div class="card-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(weekData.totalMaterialCosts || 0)}</h4>
                        <p class="cost-label">Materia Prima</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card workforce-card week-card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(weekData.totalWorkforceCosts || 0)}</h4>
                        <p class="cost-label">Mano de Obra</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card indirect-card week-card">
                    <div class="card-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(weekData.totalIndirectCosts || 0)}</h4>
                        <p class="cost-label">Costos Indirectos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card assignable-card week-card">
                    <div class="card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(weekData.totalAssignableCosts || 0)}</h4>
                        <p class="cost-label">Gastos Asignables</p>
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

    // Agregar estilos CSS modernos si no existen
    addModernCostCardStyles();

    createProductsMetricsChart(weekData);
    fillWeekProductsList(weekData);
}
/**
 * Mostrar análisis de un producto específico a través de las semanas
 */
function showProductAnalysis(productId) {
    console.log(`🔍 Analizando producto: ${productId}`);

    const productData = getProductDataAcrossWeeks(productId);
    if (!productData) {
        console.error('❌ No se encontraron datos para el producto');
        return;
    }

    const container = document.querySelector('.cardDashboard .container-fluid');
    container.innerHTML = '';

    container.innerHTML = `
        <!-- CARDS PRINCIPALES DEL PRODUCTO -->
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

        <!-- CARDS MODERNOS DE COSTOS DEL PRODUCTO -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="modern-cost-card material-card product-card">
                    <div class="card-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(productData.totalMaterialCosts)}</h4>
                        <p class="cost-label">Materia Prima</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card workforce-card product-card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(productData.totalWorkforceCosts)}</h4>
                        <p class="cost-label">Mano de Obra</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card indirect-card product-card">
                    <div class="card-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(productData.totalIndirectCosts)}</h4>
                        <p class="cost-label">Costos Indirectos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="modern-cost-card assignable-card product-card">
                    <div class="card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="cost-value">${formatCurrency(productData.totalAssignableCosts)}</h4>
                        <p class="cost-label">Gastos Asignables</p>
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

    // Agregar estilos CSS modernos si no existen
    addModernCostCardStyles();

    createProductEvolutionChart(productData);
    fillProductSummary(productData);
}

/**
 * Obtener datos de un producto a través de todas las semanas
 */
function getProductDataAcrossWeeks(productId) {
    const productWeeks = [];
    let totalRevenue = 0;
    let totalCosts = 0;
    let totalProfit = 0;
    let totalUnits = 0;
    let productName = '';

    // NUEVO: Variables para costos por tipo
    let totalMaterialCosts = 0;
    let totalWorkforceCosts = 0;
    let totalIndirectCosts = 0;
    let totalAssignableCosts = 0;

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
                margin: product.revenue > 0 ? ((product.profit / product.revenue) * 100).toFixed(1) : 0,
                // NUEVO: Costos por tipo por semana para este producto
                materialCosts: product.materialCosts || 0,
                workforceCosts: product.workforceCosts || 0,
                indirectCosts: product.indirectCosts || 0,
                assignableCosts: product.assignableCosts || 0
            });

            totalRevenue += product.revenue;
            totalCosts += costs;
            totalProfit += product.profit;
            totalUnits += product.units;
            productName = product.name;

            // NUEVO: Acumular costos por tipo
            totalMaterialCosts += product.materialCosts || 0;
            totalWorkforceCosts += product.workforceCosts || 0;
            totalIndirectCosts += product.indirectCosts || 0;
            totalAssignableCosts += product.assignableCosts || 0;
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
        weeksActive: productWeeks.length,
        // NUEVO: Totales por tipo de costo
        totalMaterialCosts,
        totalWorkforceCosts,
        totalIndirectCosts,
        totalAssignableCosts
    };
}

/**
 * Crear gráfico de evolución del producto
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
 * Llenar resumen del producto
 */
function fillProductSummary(productData) {
    const container = document.getElementById('productSummary');
    if (!container) return;

    // Calcular porcentajes de cada tipo de costo
    const totalCosts = productData.totalCosts;
    const materialPercent = totalCosts > 0 ? ((productData.totalMaterialCosts / totalCosts) * 100).toFixed(1) : 0;
    const workforcePercent = totalCosts > 0 ? ((productData.totalWorkforceCosts / totalCosts) * 100).toFixed(1) : 0;
    const indirectPercent = totalCosts > 0 ? ((productData.totalIndirectCosts / totalCosts) * 100).toFixed(1) : 0;
    const assignablePercent = totalCosts > 0 ? ((productData.totalAssignableCosts / totalCosts) * 100).toFixed(1) : 0;

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

        <!-- NUEVO: Desglose de costos por tipo -->
        <div class="mb-4">
            <h6 class="text-danger mb-3">💰 Desglose de Costos</h6>
            <div class="bg-light rounded p-3">
                <div class="row small">
                    <div class="col-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-warning">🧱 Materia Prima:</span>
                            <span class="font-weight-bold">${materialPercent}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: ${materialPercent}%"></div>
                        </div>
                        <small class="text-muted">${formatCurrency(productData.totalMaterialCosts)}</small>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-info">👷 Mano de Obra:</span>
                            <span class="font-weight-bold">${workforcePercent}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: ${workforcePercent}%"></div>
                        </div>
                        <small class="text-muted">${formatCurrency(productData.totalWorkforceCosts)}</small>
                    </div>
                </div>
                <div class="row small">
                    <div class="col-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">⚙️ Indirectos:</span>
                            <span class="font-weight-bold">${indirectPercent}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-secondary" style="width: ${indirectPercent}%"></div>
                        </div>
                        <small class="text-muted">${formatCurrency(productData.totalIndirectCosts)}</small>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark">📋 Asignables:</span>
                            <span class="font-weight-bold">${assignablePercent}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-dark" style="width: ${assignablePercent}%"></div>
                        </div>
                        <small class="text-muted">${formatCurrency(productData.totalAssignableCosts)}</small>
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

    // Calcular totales por tipo de costo
    const totalMaterial = dashboardData.weeks.reduce((sum, week) => sum + (week.totalMaterialCosts || 0), 0);
    const totalWorkforce = dashboardData.weeks.reduce((sum, week) => sum + (week.totalWorkforceCosts || 0), 0);
    const totalIndirect = dashboardData.weeks.reduce((sum, week) => sum + (week.totalIndirectCosts || 0), 0);
    const totalAssignable = dashboardData.weeks.reduce((sum, week) => sum + (week.totalAssignableCosts || 0), 0);

    // Actualizar cards principales
    const salesElement = document.getElementById('totalSales');
    const costsElement = document.getElementById('totalCosts');
    const profitsElement = document.getElementById('totalProfits');
    const marginElement = document.getElementById('totalMargin');

    if (salesElement) salesElement.textContent = formatCurrency(totalSales);
    if (costsElement) costsElement.textContent = formatCurrency(totalCosts);
    if (profitsElement) profitsElement.textContent = formatCurrency(totalProfits);
    if (marginElement) marginElement.textContent = totalMargin + '%';

    // Actualizar cards de costos por tipo
    const materialElement = document.getElementById('totalMaterial');
    const workforceElement = document.getElementById('totalWorkforce');
    const indirectElement = document.getElementById('totalIndirect');
    const assignableElement = document.getElementById('totalAssignable');

    if (materialElement) materialElement.textContent = formatCurrency(totalMaterial);
    if (workforceElement) workforceElement.textContent = formatCurrency(totalWorkforce);
    if (indirectElement) indirectElement.textContent = formatCurrency(totalIndirect);
    if (assignableElement) assignableElement.textContent = formatCurrency(totalAssignable);

    console.log(`📊 Métricas actualizadas - Material: ${formatCurrency(totalMaterial)}, Mano de obra: ${formatCurrency(totalWorkforce)}, Indirectos: ${formatCurrency(totalIndirect)}, Asignables: ${formatCurrency(totalAssignable)}`);
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
        minimumFractionDigits: 0,
        maximumFractionDigits: 0  // CAMBIO: Agregar esta línea
    }).format(Math.round(value || 0));  // CAMBIO: Agregar Math.round()
}

/**
 * 4. EVENT LISTENERS - CAMBIO PARA SELECT2
 */
// CAMBIO: Usar $('#productSelector').on('change') en lugar de addEventListener
$('#productSelector').on('change', function () {
    if (!dashboardData) return;

    const selectedProduct = this.value;
    console.log(`🔄 Producto seleccionado: ${selectedProduct}`);

    currentProduct = selectedProduct;

    if (selectedProduct === 'all') {
        if (currentWeek !== 'all') {
            showWeekChart(currentWeek);
        } else {
            showAllWeeksChart();
        }
    } else {
        showProductAnalysis(selectedProduct);
    }
});

/**
 * SOLUCIÓN 3: EVENT LISTENERS COMPATIBLES
 */
document.addEventListener('DOMContentLoaded', function () {

    // Cambio de semana
    document.getElementById('weekSelector').addEventListener('change', function () {
        if (!dashboardData) return;

        const selectedWeek = this.value;
        console.log(`🔄 Cambiando a: ${selectedWeek}`);

        // Resetear selector de productos (compatible con y sin Select2)
        const productSelector = document.getElementById('productSelector');
        if (typeof $.fn.select2 === 'function' && $(productSelector).hasClass('select2-hidden-accessible')) {
            $('#productSelector').val('all').trigger('change');
        } else {
            productSelector.value = 'all';
            // Disparar evento manualmente
            productSelector.dispatchEvent(new Event('change'));
        }

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

    // Cambio de producto (compatible con y sin Select2)
    function handleProductChange() {
        if (!dashboardData) return;

        const selectedProduct = document.getElementById('productSelector').value;
        console.log(`🔄 Producto seleccionado: ${selectedProduct}`);

        currentProduct = selectedProduct;

        if (selectedProduct === 'all') {
            if (currentWeek !== 'all') {
                showWeekChart(currentWeek);
            } else {
                showAllWeeksChart();
            }
        } else {
            showProductAnalysis(selectedProduct);
        }
    }

    // Agregar event listener para productos
    if (typeof $.fn.select2 === 'function') {
        // Con Select2
        $('#productSelector').on('change', handleProductChange);
    } else {
        // Sin Select2
        document.getElementById('productSelector').addEventListener('change', handleProductChange);
    }

    // Resto de event listeners...
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!dashboardData) return;

            console.log(`🎨 Cambiando a: ${this.dataset.chart}`);

            if (currentProduct !== 'all') {
                showProductAnalysis(currentProduct);
            } else if (currentWeek === 'all') {
                showAllWeeksChart();
            } else {
                showWeekChart(currentWeek);
            }
        });
    });

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

/**
 * 5. AGREGAR AL FINAL DEL ARCHIVO - VARIABLES GLOBALES
 */
// Exponer variables de charts globalmente para otros archivos
window.dashboardCharts = {
    weeklyChart: null,
    productChart: null,
    destroy: function () {
        if (this.weeklyChart) {
            this.weeklyChart.destroy();
            this.weeklyChart = null;
        }
        if (this.productChart) {
            this.productChart.destroy();
            this.productChart = null;
        }
    }
};

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

/**
 * 4. FUNCIÓN PARA AGREGAR ESTILOS CSS MODERNOS
 */
function addModernCostCardStyles() {
    if (document.getElementById('modern-cost-card-styles')) return;

    const style = document.createElement('style');
    style.id = 'modern-cost-card-styles';
    style.textContent = `
        /* Estilos para cards modernos de costos */
        .modern-cost-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .modern-cost-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .modern-cost-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-gradient));
        }

        /* Gradientes específicos por tipo */
        .material-card {
            --card-gradient: #ff6b6b, #ffa726;
        }
        .material-card::before {
            background: linear-gradient(90deg, #ff6b6b, #ffa726);
        }

        .workforce-card {
            --card-gradient: #4fc3f7, #29b6f6;
        }
        .workforce-card::before {
            background: linear-gradient(90deg, #4fc3f7, #29b6f6);
        }

        .indirect-card {
            --card-gradient: #9575cd, #7e57c2;
        }
        .indirect-card::before {
            background: linear-gradient(90deg, #9575cd, #7e57c2);
        }

        .assignable-card {
            --card-gradient: #66bb6a, #4caf50;
        }
        .assignable-card::before {
            background: linear-gradient(90deg, #66bb6a, #4caf50);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .material-card .card-icon {
            background: linear-gradient(135deg, #ff6b6b20, #ffa72620);
            color: #ff6b6b;
        }

        .workforce-card .card-icon {
            background: linear-gradient(135deg, #4fc3f720, #29b6f620);
            color: #29b6f6;
        }

        .indirect-card .card-icon {
            background: linear-gradient(135deg, #9575cd20, #7e57c220);
            color: #7e57c2;
        }

        .assignable-card .card-icon {
            background: linear-gradient(135deg, #66bb6a20, #4caf5020);
            color: #4caf50;
        }

        .card-icon i {
            font-size: 24px;
        }

        .card-content {
            flex: 1;
            min-width: 0;
        }

        .cost-value {
            margin: 0 0 8px 0;
            color: #2c3e50;
            line-height: 1.2;
        }

        .cost-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #5a6c7d;
            margin: 0 0 12px 0;
            line-height: 1.2;
        }

        .cost-trend {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cost-trend i {
            font-size: 12px;
            opacity: 0.7;
        }

        .trend-text {
            font-size: 0.8rem;
            color: #8892a0;
            font-weight: 500;
        }

        /* Variaciones para diferentes contextos */
        .week-card {
            border-left: 4px solid var(--card-accent);
        }

        .product-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modern-cost-card {
                height: 120px;
                padding: 16px;
            }

            .card-icon {
                width: 48px;
                height: 48px;
                margin-right: 12px;
            }

            .card-icon i {
                font-size: 20px;
            }

            .cost-value {
                font-size: 1.4rem;
            }

            .cost-label {
                font-size: 0.85rem;
            }
        }

        /* Animaciones sutiles */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modern-cost-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .modern-cost-card:nth-child(1) { animation-delay: 0.1s; }
        .modern-cost-card:nth-child(2) { animation-delay: 0.2s; }
        .modern-cost-card:nth-child(3) { animation-delay: 0.3s; }
        .modern-cost-card:nth-child(4) { animation-delay: 0.4s; }
    `;

    document.head.appendChild(style);
}

// Funciones globales
window.loadDashboardData = loadDashboardData;