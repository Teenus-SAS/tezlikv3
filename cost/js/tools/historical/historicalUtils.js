$(document).ready(function () {

    /* Cargue tabla de Precios */
    window.loadAllData = async () => {
        try {
            historical = await searchData('/api/historical');

            // Procesar fechas según configuración
            historical = historical.map(item => {
                return {
                    ...item,
                    formattedPeriod: formatPeriod(item.date || new Date(item.year, item.month - 1))
                };
            });

            const periodosInvertidos = {};
            const yearsInvertidos = {};

            historical.forEach(item => {
                periodosInvertidos[historicalConfig.companyConfig === 1 ? item.month : getWeekNumber(item.date)] =
                    historicalConfig.companyConfig === 1 ? historicalConfig.months[item.month - 1] : `Semana ${getWeekNumber(item.date)}`;
                yearsInvertidos[item.year] = item.year;
            });

            historicalIndicatiors(historical);

            // Cargar select de periodos (mes/semana)
            let $select = $(`#period`);
            $select.empty();
            $select.append(`<option disabled selected>Seleccionar</option>`);
            $select.append('<option value="0">Todo</option>');
            $.each(periodosInvertidos, function (i, value) {
                $select.append(
                    `<option value="${i}">${value}</option>`
                );
            });

            // Cargar select de años
            let $selectYear = $('#year');
            $selectYear.empty();
            $selectYear.append('<option disabled selected>Seleccionar</option>');
            $selectYear.append('<option value="0">Todo</option>');

            $.each(yearsInvertidos, function (i, value) {
                $selectYear.append(
                    `<option value="${i}">${value}</option>`
                );
            });

            loadTblPrices(historical);
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }

    // Función para obtener el número de semana según ISO (comienza lunes)
    window.getWeekNumber = (date) => {
        const d = new Date(date);
        d.setHours(0, 0, 0, 0);
        d.setDate(d.getDate() + 3 - (d.getDay() + 6) % 7);
        const week1 = new Date(d.getFullYear(), 0, 4);
        return 1 + Math.round(((d - week1) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
    }

    // Función para formatear el periodo según configuración
    window.formatPeriod = (dateStr) => {
        const date = new Date(dateStr);
        if (historicalConfig.companyConfig === 1) {
            // Formato por mes
            return `${date.getFullYear()} / ${historicalConfig.months[date.getMonth()]}`;
        } else {
            // Formato por semana (Semana X, Mes Año)
            const weekNumber = getWeekNumber(date);
            return `Semana ${weekNumber}, ${historicalConfig.months[date.getMonth()]} ${date.getFullYear()}`;
        }
    }

});