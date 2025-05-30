const historicalConfig = {
    companyConfig: 1, // 1=mes, 2=semana (debería venir del backend)
    months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
};

$(document).ready(function () {
    // Configuración compartida para todos los módulos históricos

    // Función para actualizar la configuración desde el backend
    window.initializeHistoricalConfig = async () => {
        try {
            const config = await $.get('/api/historical_config');
            historicalConfig.companyConfig = config.historicalPeriod || historicalConfig.companyConfig;
        } catch (error) {
            console.warn('No se pudo obtener configuración, usando valores por defecto', error);
        }
        return historicalConfig;
    };
});