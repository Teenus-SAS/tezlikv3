/* Revision Data gasto */
handleCheckDataExpenseRecover = async (url, idExpenseRecover) => {
    let idProduct = parseInt($('#idReference').val());
    let percentage = parseFloat($('#percentage').val());

    let data = idProduct * percentage;

    if (isNaN(data) || data <= 0) {
        toastr.error('Ingrese todos los campos');
        return false;
    }

    if (percentage > 100) {
        toastr.error('El porcentaje de recuperación debe ser menor al 100%');
        return false;
    }

    $(`#ERRefProduct`).prop('disabled', false);
    let dataExpenseRecover = new FormData(formExpenseRecover);

    if (idExpenseRecover != null)
        dataExpenseRecover.append('idExpenseRecover', idExpenseRecover);

    // Mostrar el spinner
    $(".db-spinner-overlay").show();

    let resp = await sendDataPOST(url, dataExpenseRecover);

    // Ocultar el spinner
    $(".db-spinner-overlay").hide();



    messageDistribution(resp, 2);
};

//manejador de actualizacion del porcentaje de recuperacion
handleUpdateExpenseRecovery = (data) => {
    // Cambiar botón a modo edición
    $('#btnExpenseRecover')
        .html("Actualizar")
        .data('edit-mode', true)
        .data('expense-id', data.id_expense_recover);

    // Limpiar selects y valores anteriores
    $('#idReference').val(null).trigger('change');
    $('#idNameProduct').val(null).trigger('change');
    $('#percentage').val('');

    // Volver a cargar datos en los selects y campo porcentaje
    $('#idReference').append(new Option(data.reference, data.reference, true, true)).trigger('change');
    $('#idNameProduct').append(new Option(data.product, data.product, true, true)).trigger('change');
    $('#percentage').val(data.expense_recover);

    // Scroll hacia arriba
    $('html, body').animate({ scrollTop: 0 }, 1000);
}