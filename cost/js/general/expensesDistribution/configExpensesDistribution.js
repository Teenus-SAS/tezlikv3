$(document).ready(function () {
  getExpense = async () => {
    data = await searchData('/api/expenseTotal');
    /* Carga gasto total */
    $('#expensesToDistribution').val(
      `$ ${data.total_expense.toLocaleString('es-CO')}`
    );
    $('#expensesToDistribution').prop('disabled', true);

    data = await searchData('/api/checkTypeExpense');

    if (data.flag_expense == 0) {
      /* Seleccionar tipo de gasto */
      bootbox.confirm({
        closeButton: false,
        title: 'Tipo de Gasto',
        message: 'Seleccione a cual tipo de gasto desea ingresar.',
        buttons: {
          confirm: {
            label: 'Distribución',
            className: 'btn-success',
          },
          cancel: {
            label: 'Recuperación',
            className: 'btn-info',
          },
        },
        callback: function (result) {
          result == true ? (option = 1) : (option = 2);
          setDataExpense();
        },
      });
    }
  };

  getExpense();

  setDataExpense = async () => {
    resp = await searchData(`/api/changeTypeExpense/${option}`);

    if (resp.success) {
      toastr.success(resp.message);

      if ($.fn.dataTable.isDataTable('#tblExpenses')) {
        $('#tblExpenses').DataTable().destroy();
        $('#tblExpenses').empty();
      }

      if (option == 1) {
        $('#btnExpensesDistribution').show(800);
        $('#btnImportNewExpenses').show(800);
        $('#btnImportNewExpenses').html('Importar Distribuir Gastos');
        $('#btnImportNewExpenses').val('Importar Distribución');
        $('#descrExpense').html('Distribución Gastos Generales');
        loadTableExpensesDistribution();
      }
      if (option == 2) {
        $('#btnNewExpenseRecover').show(800);
        $('#btnImportNewExpenses').show(800);
        $('#btnImportNewExpenses').html('Importar Recuperar Gastos');
        $('#btnImportNewExpenses').val('Importar Recuperación');
        $('#descrExpense').html('Recuperación Gastos Generales');
        loadTableExpenseRecover();
      }
    } else toastr.error(resp.message);
  };
});
