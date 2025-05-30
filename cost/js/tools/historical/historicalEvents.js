$(document).ready(function () {
    $(document).on('click', '.seeDetail', function (e) {
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
            sessionStorage.setItem('idHistoric', JSON.stringify({
                id: id_historic,
                timestamp: new Date().getTime()
            }));

            // Redireccionar
            window.location.href = `/cost/details-historical?id=${id_historic}`;
        } catch (error) {
            console.error('Error al guardar en sessionStorage:', error);
            // Fallback: redirección con parámetro GET
            window.location.href = `/cost/details-historical?id=${id_historic}`;
        }
    });

    $('.btnsProfit').click(function (e) {
        e.preventDefault();
        const data = this.id == 'max'
            ? historical.filter(item => item.min_profitability == maxProfitability)
            : historical.filter(item => item.min_profitability == minProfitability);
        loadTblPrices(data);
    });
});