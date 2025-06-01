$(document).ready(function () {

    //ver lista de productos de ese periodo
    $(document).on('click', '.seeProducts i', function (e) {
        e.preventDefault();

        // Obtener ID de forma más segura
        const id_historic_period = $(this).data('id');

        // Validar el ID
        if (!id_historic_period) {
            console.error('tiempo no disponible');
            toastr.error('No se pudo identificar el tiempo');
            return;
        }

        // Ocultar tabla de resumeny mostrar la de productos cliqueado
        $('.cardHistoricalResume').hide();
        $('.cardHistoricalProducts').show();

        //Cargar productos
        loadTblHistoriProducts(id_historic_period);

    });

    //ver detalle de costos del producto
    $(document).on('click', '.seeDetail i', function (e) {
        e.preventDefault();

        // Obtener ID de forma más segura
        const id_historic = $(this).data('id');

        // Validar el ID
        if (!id_historic) {
            console.error('ID histórico no válido');
            toastr.error('No se pudo identificar el registro histórico');
            return;
        }

        // Guardar en sessionStorage de forma segura
        try {
            localStorage.setItem('idHistoric', id_historic);

            // Redireccionar
            window.location.href = `/cost/details-historical?id=${id_historic}`;
        } catch (error) {
            console.error('Error al guardar en sessionStorage:', error);
            // Fallback: redirección con parámetro GET
            window.location.href = `/cost/details-historical?id=${id_historic}`;
        }
    });

    //ver detalle de costos del producto
    $(document).on('click', '.deleteHistoricProducts i', function (e) {
        e.preventDefault();

        // Obtener ID de forma más segura
        const fila = $(this).closest('tr');
        let data = $('#tblHistoricalResume').DataTable().row(fila).data();
        data = JSON.stringify(data);
        const confirmDialog = bootbox.confirm({
            title: `<i class="fas fa-exclamation-triangle"></i> Eliminar`,
            message:
                `<div class="">
                <h5>¿Esta seguro de eliminar los datos almacenados de este periodo?</h5>
                <p class="mb-0"><small><b>Esta acción no se puede deshacer.</b></small></p>
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
                const loadingDialog = bootbox.dialog({
                    message: '<p class="text-center mb-0"><i class="fas fa-spinner fa-spin"></i> Eliminando datos...</p>',
                    closeButton: false
                });

                $.ajax({
                    method: "POST",
                    url: "/api/deleteHistorical",
                    data: data,
                    success: function (resp) {
                        toastr.success(resp.message);
                        $('#tblHistoricalResume').DataTable().ajax.reload();
                        loadingDialog.modal('hide');
                    }
                });
            }
        });

    });

    $('.btnsProfit').click(function (e) {
        e.preventDefault();
        const data = this.id == 'max'
            ? historical.filter(item => item.min_profitability == maxProfitability)
            : historical.filter(item => item.min_profitability == minProfitability);
        loadTblPrices(data);
    });
});