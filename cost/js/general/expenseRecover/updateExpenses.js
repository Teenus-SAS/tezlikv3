$(document).ready(function () {
  let expensesRecover = [];

  /* Modificar porcentaje masivo */
  $(document).on('click', '.checkExpense', function () {
    let id = this.id;

    if (id.includes('all')) {
      expensesRecover = [];
      if ($(`#${id}`).is(':checked')) {
        let data = tblExpenseRecover.rows().data();

        for (let i = 0; i < data.length; i++) {
          expensesRecover.push({
            idExpenseRecover: data[i].id_expense_recover,
            idProduct: data[i].id_product,
          });
        }
        $('.checkExpense').prop('checked', true);
      } else {
        $('.checkExpense').prop('checked', false);
        $('#btnUpdateExpenses').hide(800);
      }
    } else {
      let idExpenseRecover = id.slice(6, id.length);
      if ($(`#${id}`).is(':checked')) {
        let row = $(this).parent().parent()[0];
        let data = tblExpenseRecover.rows(row).data()[0];

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

    if (expensesRecover.length >= 2) $('#btnUpdateExpenses').show(800);
  });

  $('#btnUpdateExpenses').click(function (e) {
    e.preventDefault();

    bootbox.confirm({
      title: 'Modificar Gastos',
      message: `<div class="row">
                    <div class="col-12">
                      <label for="">Porcentaje de recuperación</label>
                      <input type="number" class="form-control" id="percentageRecover">
                    </div>
                  </div>`,
      buttons: {
        confirm: {
          label: 'Ok',
          className: 'btn-success',
        },
        cancel: {
          label: 'Cancel',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          let percentage = $('#percentageRecover').val();

          percentage = parseFloat(percentage.replace(',', '.'));

          if (!percentage || percentage == '') {
            toastr.error('Campo vacio');
            return false;
          }

          if (percentage > 100) {
            toastr.error(
              'El porcentaje de recuperación debe ser menor al 100%'
            );
            return false;
          }

          expensesRecover.push(percentage);

          $.ajax({
            type: 'POST',
            url: '/api/updateExpenseRecover',
            data: { data: expensesRecover },
            success: function (resp) {
              $('.checkExpense').prop('checked', false);
              $('#btnUpdateExpenses').hide(800);
              expensesRecover = [];

              message(resp, 2);
            },
          });
        } else {
          $('.checkExpense').prop('checked', false);
          expensesRecover = [];
          $('#btnUpdateExpenses').hide(800);
        }
      },
    });
  });
});
