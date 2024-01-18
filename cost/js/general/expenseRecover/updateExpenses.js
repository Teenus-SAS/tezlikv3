$(document).ready(function () {
  let expensesRecover = [];

  /* Modificar porcentaje masivo */
  $(document).on('click', '.checkExpense', function () {
    let id = this.id;

    if (id.includes('all')) {
      expensesRecover = [];
      if ($(`#${id}`).is(':checked')) {
        let data = sessionStorage.getItem('dataExpensesRecover');
        data = JSON.parse(data);

        for (let i = 0; i < data.length; i++) {
          expensesRecover.push({
            idExpenseRecover: data[i].id_expense_recover,
            idProduct: data[i].id_product,
          });
        }
        $('.checkExpense').prop('checked', true);
      } else {
        $('.checkExpense').prop('checked', false);
        $('.cardBtnUpdateExpenses').hide(800);
      }
    } else {
      let idExpenseRecover = id.slice(6, id.length);
      if ($(`#${id}`).is(':checked')) {
        let data = sessionStorage.getItem('dataExpensesRecover');
        data = JSON.parse(data);
        data = setDataRowRecover(data, idExpenseRecover);

        let expense = {
          idExpenseRecover: idExpenseRecover,
          idProduct: data.id_product,
        };

        expensesRecover.push(expense);
      } else {
        for (i = 0; i < expensesRecover.length; i++)
          if (expensesRecover[i].idExpenseRecover == idExpenseRecover)
            expensesRecover.splice(i, 1);
      }
    }

    if (expensesRecover.length >= 2) $('.cardBtnUpdateExpenses').show(800);
  });

  $('#btnUpdateExpenses').click(function (e) {
    e.preventDefault();

    $('#percentageRecover').val('');

    $('#modifyExpensesRecover').modal('show');
  });

  /* Ocultar modal */
  $('#btnCloseExpensesRecover').click(function (e) {
    e.preventDefault();
    $('.checkExpense').prop('checked', false);
    expensesRecover = [];
    $('.cardBtnUpdateExpenses').hide(800);
    $('#modifyExpensesRecover').modal('hide');
  });

  $('#btnUpdateExpensesRecover').click(function (e) {
    e.preventDefault();

    let percentage = $('#percentageRecover').val();

    percentage = parseFloat(percentage.replace(',', '.'));

    if (!percentage || percentage == '') {
      toastr.error('Campo vacio');
      return false;
    }

    if (percentage > 100) {
      toastr.error('El porcentaje de recuperación debe ser menor al 100%');
      return false;
    }

    $('#modifyExpensesRecover').modal('hide');

    bootbox.confirm({
      title: 'Modificar Gastos',
      message: 'Esta seguro de realizar este cambio?',
      buttons: {
        confirm: {
          label: 'Si',
          className: 'btn-success',
        },
        cancel: {
          label: 'No',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          expensesRecover.push(percentage);

          $.ajax({
            type: 'POST',
            url: '/api/updateExpenseRecover',
            data: { data: expensesRecover },
            success: function (resp) {
              $('.checkExpense').prop('checked', false);
              $('.cardBtnUpdateExpenses').hide(800);
              expensesRecover = [];

              messageDistribution(resp, 2);
            },
          });
        } else {
          $('.checkExpense').prop('checked', false);
          expensesRecover = [];
          $('.cardBtnUpdateExpenses').hide(800);
        }
      },
    });
  });
});
