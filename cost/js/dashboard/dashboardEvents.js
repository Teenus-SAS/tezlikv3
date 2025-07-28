$(document).on('click', '#saveChanges', function () {
    const data = {};
    const expense = $('#expenseRecoverDisplay');
    const profit = $('#profitDisplay');
    const commision = $('#commissionDisplay');

    const salesPriceText = $('#salesPrice').text();
    const salesPrice = parseFloat(salesPriceText.replace(/[^\d,]/g, '').replace(',', '.'));

    const idProduct = sessionStorage.getItem('idProduct');

    data.salesPrice = salesPrice;
    data.idProduct = idProduct;

    if (expense.attr('data-change') === '1')
        data.percentage = expense.attr('data-value');

    if (commision.attr('data-change') === '1')
        data.commission = commision.attr('data-value');

    if (profit.attr('data-change') === '1')
        data.profit = profit.attr('data-value');

    if (Object.keys(data).length > 0) {

        // Mostrar spinner y ocultar texto
        $('#spinnerSave').removeClass('d-none');
        $('#saveText').addClass('d-none');

        $.ajax({
            url: '/api/updateCosts',
            method: 'POST',
            data,
            success: function (response) {
                toastr.success('Actualización realizada correctamente');
                $('#expenseRecoverInput, #commissionInput, #profitInput').attr('data-change', '0');
                $('.saveChanges').fadeOut();
            },
            error: function (err) {
                console.error('Error al guardar cambios:', err);
                toastr.error('Error al guardar cambios');
            },
            complete: function () {
                // Ocultar spinner y mostrar texto nuevamente
                $('#spinnerSave').addClass('d-none');
                $('#saveText').removeClass('d-none');
            }
        });
    }

});