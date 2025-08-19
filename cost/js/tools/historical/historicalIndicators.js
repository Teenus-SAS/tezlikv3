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

    // Limpiar dashboard
    if (window.weeklyChart) {
        window.dashboardCharts.destroy();
        window.weeklyChart = null;
    }
    if (window.productChart) {
        window.productChart.destroy();
        window.productChart = null;
    }
});