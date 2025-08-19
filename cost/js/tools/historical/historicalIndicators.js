// Event listener para botón de gráficos
document.getElementById('btnGraphic').addEventListener('click', function () {
    console.log('🎯 Botón Gráficos clickeado');

    // Mostrar dashboard y controles
    document.querySelector('.cardHistoricalResume').style.display = 'none';
    document.querySelector('.cardHistoricalProducts').style.display = 'none';
    document.querySelector('.cardDashboard').style.display = 'block';

    // Mostrar controles
    const controls = document.getElementById('analysisControls');
    controls.style.display = 'block';
    controls.classList.add('slide-in');

    // CARGAR DASHBOARD AUTOMÁTICAMENTE
    loadDashboardData();
});

// Event listener para botón de lista
document.getElementById('btnList').addEventListener('click', function () {
    console.log('📋 Botón Lista clickeado');

    // Ocultar dashboard y controles
    document.querySelector('.cardDashboard').style.display = 'none';
    document.getElementById('analysisControls').style.display = 'none';

    // Mostrar lista
    document.querySelector('.cardHistoricalResume').style.display = 'block';

    // CORRECCIÓN: Limpiar dashboard de forma segura
    try {
        // Método 1: Usar la función global si existe
        if (window.dashboardCharts && typeof window.dashboardCharts.destroy === 'function') {
            console.log('🧹 Limpiando gráficos con dashboardCharts.destroy()');
            window.dashboardCharts.destroy();
        }
        // Método 2: Limpiar individualmente si las variables globales existen
        else {
            console.log('🧹 Limpiando gráficos individualmente');

            if (window.weeklyChart && typeof window.weeklyChart.destroy === 'function') {
                window.weeklyChart.destroy();
                window.weeklyChart = null;
            }

            if (window.productChart && typeof window.productChart.destroy === 'function') {
                window.productChart.destroy();
                window.productChart = null;
            }
        }

        // Método 3: Limpiar canvas manualmente como respaldo
        const weeklyCanvas = document.getElementById('weeklyChart');
        if (weeklyCanvas) {
            const ctx = weeklyCanvas.getContext('2d');
            ctx.clearRect(0, 0, weeklyCanvas.width, weeklyCanvas.height);
        }

        const productCanvas = document.getElementById('productChart');
        if (productCanvas) {
            const ctx = productCanvas.getContext('2d');
            ctx.clearRect(0, 0, productCanvas.width, productCanvas.height);
        }

        const evolutionCanvas = document.getElementById('productEvolutionChart');
        if (evolutionCanvas) {
            const ctx = evolutionCanvas.getContext('2d');
            ctx.clearRect(0, 0, evolutionCanvas.width, evolutionCanvas.height);
        }

        console.log('✅ Gráficos limpiados correctamente');

    } catch (error) {
        console.warn('⚠️ Error al limpiar gráficos:', error);
        // No hacer nada más, continuar con la ejecución normal
    }
});