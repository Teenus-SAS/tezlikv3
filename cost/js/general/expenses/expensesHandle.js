/* Revision data gasto */
checkDataExpense = async (url, idExpense) => {
    let puc = parseInt($('#idPuc').val());
    let value = parseFloat($('#expenseValue').val());

    if (production_center == '1' && flag_production_center == '1')
        selectProductionCenter = parseFloat($('#selectProductionCenterExpenses').val());
    else
        selectProductionCenter = 0;

    isNaN(value) ? value = 0 : value;

    if (!puc || puc == '' || isNaN(selectProductionCenter)) {
        toastr.error('Ingrese todos los campos');
        return false;
    }

    let count = $('#idPuc option:selected').text();

    while (count.includes('-'))
        count = count.slice(0, -1);

    if (count.length < 4) {
        toastr.error('Seleccione una Cuenta');
        return false;
    }

    let dataExpense = new FormData(formCreateExpenses);

    if (!idExpense && production_center == '1' && flag_production_center == '1') {
        let data = JSON.parse(sessionStorage.getItem('dataExpenses'));

        let arr = data.find(item => item.id_puc == puc);

        if (arr) {
            url = '/api/updateExpenses';

            idExpense = arr.id_expense;
        }
    }

    dataExpense.append('expenseValue1', value);
    dataExpense.append('idExpenseProductionCenter', sessionStorage.getItem('id_expense_product_center'));

    dataExpense.append('expenseValue', value);
    dataExpense.append('production', selectProductionCenter);

    if (idExpense != '' || idExpense != null)
        dataExpense.append('idExpense', idExpense);

    // Mostrar el spinner
    $(".db-spinner-overlay").show();

    let resp = await sendDataPOST(url, dataExpense);

    // Ocultar el spinner
    $(".db-spinner-overlay").hide();

    messageExpense(resp);
};