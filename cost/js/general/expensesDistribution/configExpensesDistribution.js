$(document).ready(function () {
  /* Carga gasto total */
  $.ajax({
    type: 'GET',
    url: `/api/expenseTotal`,
    success: function (r) {
      $('#expensesToDistribution').val(
        `$ ${r.total_expense.toLocaleString('es-CO')}`
      );
      $('#expensesToDistribution').prop('disabled', true);
    },
  });
});
