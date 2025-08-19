let modalId = 0;

let date = new Date();
months1 = {
    1: 'Enero',
    2: 'Febrero',
    3: 'Marzo',
    4: 'Abril',
    5: 'Mayo',
    6: 'Junio',
    7: 'Julio',
    8: 'Agosto',
    9: 'Septiembre',
    10: 'Octubre',
    11: 'Noviembre',
    12: 'Diciembre'
};

$(document).on('click', '#btnNewHistorical', function (e) {
    try {
        e.preventDefault();

        // Limpiar el datepicker
        $('#datepicker').val('');

        // Determinar qué modal mostrar basado en la configuración
        const showWeeklyModal = historicalConfig.companyConfig === '1'; // 1=semanal

        // 3. Mostrar el modal correspondiente
        modalId = showWeeklyModal ? '#modalWeekly' : '#modalHistorical';
        $(modalId).modal('show');

        setTimeout(() => {
            $(`${modalId} form :input:visible:enabled:first`).focus();
        }, 500);

    } catch (error) {
        console.error('Error al mostrar modal histórico:', error);
        toastr.error('Error al preparar el formulario histórico');
    }
});

$('#btnSaveHistorical').click(function (e) {
    e.preventDefault();

    let date = $('#datepicker').val();

    if (!date || date == '') {
        toastr.error('Ingrese la fecha');
        return false;
    }

    date = date.split('-');
    let data = {};
    data['year'] = date[0];
    data['month'] = date[1];

    $(modalId).modal('hide');

    //Buscar el año y la semana en el datatable si se encuentra marcarlo como 1 sino 0
    existsHistorical = existsHistoricalMonth(data.year, data.month);

    period = String(historicalConfig.companyConfig) === String(1) ? 'La Semana' : 'El Mes';

    // Mensajes condicionales
    const primaryMessage = existsHistorical
        ? `¿Desea <b>reescribir</b> los datos para ${period} ${data['month']} ? Los datos existentes se perderán.`
        : '¿Desea guardar los datos?';

    const primaryTitle = existsHistorical ? 'Reemplazar' : 'Guardar';

    const confirmDialog = bootbox.confirm({
        title: `<i class="fas fa-exclamation-triangle"></i> ${primaryTitle}`,
        message: `
            <div class="">
                <h5>${primaryMessage}</h5>
                <p class="mb-0"><small>Esta acción no se puede deshacer.</small></p>
            </div>`,
        buttons: {
            confirm: {
                label: '<i class="fas fa-check"></i> Confirmar',
                className: 'btn-success'
            },
            cancel: {
                label: '<i class="fas fa-times"></i> Cancelar',
                className: 'btn-danger'
            }
        },
        callback: (result) => {
            if (!result) return;
            confirmDialog.modal('hide');

            // Mostrar spinner durante el guardado
            /* const loadingDialog = bootbox.dialog({
                message: '<p class="text-center mb-0"><i class="fas fa-spinner fa-spin"></i> Guardando datos...</p>',
                closeButton: false
            }); */

            $.ajax({
                method: "POST",
                url: "/api/historical/saveHistorical",
                data: data,
                success: function (resp) {
                    if (resp.success) {
                        toastr.success(resp.message);
                        $('#tblHistoricalResume').DataTable().ajax.reload();
                        loadingDialog.modal('hide');
                    }
                    else if (resp.error == true) toastr.error(resp.message);
                }
            });
        }
    });
});


$('#btnCloseHistorical').click(function (e) {
    e.preventDefault();

    $('#modalHistorical').modal('hide');
});

/**
* Verifica si ya existe un registro histórico para el año y mes especificados
* @param {number|string} year - Año a verificar
* @param {number|string} month - Mes a verificar (1-12)
* @returns {boolean} True si ya existe un registro para ese periodo
*/
existsHistoricalMonth = (year, month) => {
    try {
        // Obtener datos de la tabla
        const table = $('#tblHistoricalResume').DataTable();
        const allData = table.rows().data().toArray();

        // Buscar coincidencia
        return allData.some(row => {
            return String(row.year) === String(year) &&
                String(row.month) === String(month);
        });

    } catch (error) {
        console.error('Error en existsHistoricalMonth:', error);
        return false;
    }
};
