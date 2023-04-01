$(document).ready(function () {
  /* Ocultar table de ingreso de datos volumen y unidades */
  $('.cardExpensesDistribution').hide();

  /* Abrir ventana para ingresar el volumen dy unidades de ventas para calcular gastos atribuibles al producto */
  $('#btnExpensesDistribution').click(function (e) {
    e.preventDefault();
    $('.selectNameProduct option').removeAttr('selected');
    $('.refProduct option').removeAttr('selected');
    $(`.selectNameProduct option[value='0']`).prop('selected', true);
    $(`.refProduct option[value='0']`).prop('selected', true);

    $('.cardImportExpenses').hide(800);
    $('.cardExpenseRecover').hide(800);
    $('#btnAssignExpenses').html('Asignar');
    $('.cardExpensesDistribution').toggle(800);

    sessionStorage.removeItem('id_expenses_distribution');

    $('#undVendidas').val('');
    $('#volVendidas').val('');
  });

  $('#btnAssignExpenses').click(function (e) {
    e.preventDefault();

    let expensesToDistribution = $('#expensesToDistribution').val();

    if (expensesToDistribution == '$ 0' || !expensesToDistribution) {
      toastr.error('Asigne un gasto primero antes de distribuir');
      return false;
    }

    let idExpensesDistribution = sessionStorage.getItem(
      'id_expenses_distribution'
    );

    if (idExpensesDistribution == '' || idExpensesDistribution == null) {
      checkDataExpenseDistribution(
        '/api/addExpensesDistribution',
        idExpensesDistribution
      );
    } else {
      checkDataExpenseDistribution(
        '/api/updateExpensesDistribution',
        idExpensesDistribution
      );
    }
  });

  /* Actualizar gasto */
  $(document).on('click', '.updateExpenseDistribution', function (e) {
    $('.cardImportExpenses').hide(800);
    $('.cardExpenseRecover').hide(800);
    $('.cardExpensesDistribution').show(800);
    $('#btnAssignExpenses').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblExpensesDistribution.fnGetData(row);

    sessionStorage.setItem(
      'id_expenses_distribution',
      data.id_expenses_distribution
    );

    $(`#EDNameProduct option:contains(${data.product})`).prop('selected', true);
    $(`#EDRefProduct option:contains(${data.reference})`).prop(
      'selected',
      true
    );
    $('#undVendidas').val(data.units_sold.toLocaleString('es-CO'));
    $('#volVendidas').val(data.turnover.toLocaleString('es-CO'));
    $('#expensesToDistribution').val(
      data.assignable_expense.toLocaleString('es-CO')
    );

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision de data gasto */
  checkDataExpenseDistribution = async (url, idExpense) => {
    let refProduct = parseInt($('#EDRefProduct').val());
    let nameProduct = parseInt($('#EDNameProduct').val());
    let unitExp = $('#undVendidas').val();
    let volExp = $('#volVendidas').val();

    unitExp = parseFloat(strReplaceNumber(unitExp));
    volExp = parseFloat(strReplaceNumber(volExp));

    let data = refProduct * nameProduct * unitExp * volExp;

    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataExpense = new FormData(formExpensesDistribution);

    if (idExpense != '' || idExpense != null) {
      dataExpense.append('assignableExpense', $('#assignableExpense').val());
      dataExpense.append('idExpensesDistribution', idExpense);
    }

    let resp = await sendDataPOST(url, dataExpense);

    message(resp, 1);
  };

  /* Eliminar gasto */

  deleteExpenseDistribution = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExpensesDistribution.fnGetData(row);

    let id_expenses_distribution = data.id_expenses_distribution;

    let idProduct = data.id_product;
    let dataExpensesDistribution = {};

    dataExpensesDistribution['idExpensesDistribution'] =
      id_expenses_distribution;
    dataExpensesDistribution['selectNameProduct'] = idProduct;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este gasto? Esta acción no se puede reversar.',
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
          $.post(
            '../../api/deleteExpensesDistribution',
            dataExpensesDistribution,
            function (data, textStatus, jqXHR) {
              message(data, 1);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data, op) => {
    if (data.success == true) {
      $('.cardExpensesDistribution').hide(800);
      $('.cardExpenseRecover').hide(800);
      $('#formExpensesDistribution').trigger('reset');
      $('#formExpenseRecover').trigger('reset');
      updateTable(op);
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable(op) {
    if ($.fn.dataTable.isDataTable('#tblExpenses')) {
      $('#tblExpenses').DataTable().destroy();
      $('#tblExpenses').empty();
    }
    op == 1 ? loadTableExpensesDistribution() : loadTableExpenseRecover();
  }
});
