$(document).ready(function () {
  /* Carga gasto total */
  $.ajax({
    type: 'GET',
    url: `/api/expenseTotal`,
    success: function (r) {
      $('#expensesToDistribution').val(`$ ${r.total_expense.toLocaleString()}`);
      $('#expensesToDistribution').prop('disabled', true);
    },
  });
});
