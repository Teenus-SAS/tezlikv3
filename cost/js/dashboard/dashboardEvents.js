$(document).on('click', '#saveChanges', function () {

    const data = {};
    const expense = $('#expenseRecoverDisplay');
    const profit = $('#profitDisplay');
    const commision = $('#commissionDisplay');

    if (expense.attr('data-change') === '1')
        data.expense_recover = expense.attr('data-value');

    if (commision.attr('data-change') === '1')
        data.commission = commision.attr('data-value');

    if (profit.attr('data-change') === '1')
        data.profit = profit.attr('data-value');

    if (Object.keys(data).length > 0) {
        $.ajax({
            url: '/api/updateCosts',
            method: 'POST',
            data,
            success: function (response) {
                console.log('Cambios guardados:', response);
                $('#expenseRecoverInput, #commissionInput, #profitInput').attr('data-change', '0');
                $('.saveChanges').fadeOut();
            },
            error: function (err) {
                console.error('Error al guardar cambios:', err);
            }
        });
    }

});