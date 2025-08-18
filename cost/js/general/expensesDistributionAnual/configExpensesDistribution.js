$(document).ready(function () {
  getExpenseAnual = async () => {
    let data = await searchData('/api/expensesAnual/totalExpenseAnual');

    data.expenses_value == undefined || !data.expenses_value
      ? (expenses_value = 0)
      : (expenses_value = data.expenses_value);

    /* Carga gasto total */
    $('#expensesToDistributionAnual').val(
      `$ ${expenses_value.toLocaleString('es-CO')}`
    );
    $('#expensesToDistributionAnual').prop('disabled', true);

  };

  getExpenseAnual();


});
