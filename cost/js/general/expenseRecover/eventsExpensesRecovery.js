$(document).on('click', '.manual_recovery', async function () {
    const id_expense_recover = this.id;
    const id_product = $(this).data('product');

    try {

        // Mostrar el spinner
        $(".db-spinner-overlay").show();

        const response = await fetch(`/api/changeManualRecovery/${id_expense_recover}/${id_product}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' },
        });

        // Ocultar el spinner
        $(".db-spinner-overlay").hide();

        const result = await response.json();

        if (response.ok)
            tblExpenses.api().ajax.reload(null, false);
        else
            console.error('Error en la respuesta:', result.message || result);
    } catch (error) {
        console.error('Error en el envío:', error);
    }

});