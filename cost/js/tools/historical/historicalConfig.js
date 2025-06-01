// Definir la configuración histórica
window.historicalConfig = {
    companyConfig: localStorage.getItem('companyConfigHistory') || 1, // 0=mes, 1=semana
    months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
};

// Función para actualizar la configuración
window.updateHistoricalConfig = function (config) {
    if (config && typeof config.companyConfig !== 'undefined') {
        historicalConfig.companyConfig = config.companyConfig;
        localStorage.setItem('companyConfigHistory', config.companyConfig);
    }
};