$(document).ready(function () {
  getExpense = async () => {
    let data = await searchData('/api/expenseTotal');

    data.total_expense == undefined || !data.total_expense
      ? (total_expense = 0)
      : (total_expense = data.total_expense);

    /* Carga gasto total */
    $('#expensesToDistribution').val(
      `$ ${total_expense.toLocaleString('es-CO')}`
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
          changeTypeExpense();
        },
      });
    } else {
      option = data.flag_expense;

      setDataExpense();
    }
  };

  getExpense();

  changeTypeExpense = async () => {
    resp = await searchData(`/api/changeTypeExpense/${option}`);
    if (resp.success) toastr.success(resp.message);
    else toastr.error(resp.message);
    setDataExpense();
  };

  setDataExpense = () => {
    if ($.fn.dataTable.isDataTable('#tblExpenses')) {
      $('#tblExpenses').DataTable().destroy();
      $('#tblExpenses').empty();
    }

    if (option == 1) {
      $('.distributionExpenses').html('Distribución de Gastos');
      $('.cardBtnExpensesDistribution').show(800);
      $('.cardBtnImportExpenses').show(800);
      $('#btnImportNewExpenses').html('Importar Distribución');
      $('#lblImportExpense').html('Importar Distribución de Gasto');
      $('#descrExpense').html('Distribución Gastos Generales');
      loadTableExpensesDistribution();
    }
    if (option == 2) {
      $('.cardCheckExpense').show(800);
      // $('.generalExpenses').hide();
      $('.distributionExpenses').html('Recuperación de Gastos');
      $('.cardBtnExpenseRecover').show(800);
      $('.cardBtnImportExpenses').show(800);
      $('#btnImportNewExpenses').html('Importar Recuperar Gastos');
      $('#lblImportExpense').html('Importar Recuperación');
      $('#descrExpense').html('Recuperación Gastos Generales');
      loadTableExpenseRecover();
    }
  };
});
