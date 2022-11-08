$(document).ready(function () {
  /* Ocultar table de ingreso de datos volumen y unidades */

  $('.cardExpensesDistribution').hide();

  /* Abrir ventana para ingresar el volumen dy unidades de ventas para calcular gastos atribuibles al producto */

  $('#btnExpensesDistribution').click(function (e) {
    e.preventDefault();

    $('.cardImportDistributionExpenses').hide(800);
    $('.cardExpensesDistribution').toggle(800);
    $('#btnAssignExpenses').html('Asignar');

    sessionStorage.removeItem('id_expenses_distribution');

    $('#refProduct option:contains(Seleccionar)').prop('selected', true);
    $('#selectNameProduct option:contains(Seleccionar)').prop('selected', true);
    $('#undVendidas').val('');
    $('#volVendidas').val('');
  });

  $('#btnAssignExpenses').click(function (e) {
    e.preventDefault();
    let idExpensesDistribution = sessionStorage.getItem(
      'id_expenses_distribution'
    );

    if (idExpensesDistribution == '' || idExpensesDistribution == null) {
      refProduct = parseInt($('#refProduct').val());
      nameProduct = parseInt($('#selectNameProduct').val());
      unitExp = parseInt($('#undVendidas').val());
      volExp = parseInt($('#volVendidas').val());

      data = refProduct * nameProduct;
      exp = unitExp * volExp;

      if (!data || exp == null) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      expensesDistribution = $('#formExpensesDistribution').serialize();

      $.post(
        '../../api/addExpensesDistribution',
        expensesDistribution,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateExpensesDistribution();
    }
  });

  /* Actualizar gasto */

  $(document).on('click', '.updateExpenseDistribution', function (e) {
    $('.cardImportDistributionExpenses').hide(800);
    $('.cardExpensesDistribution').show(800);
    $('#btnAssignExpenses').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblExpensesDistribution.fnGetData(row);

    sessionStorage.setItem(
      'id_expenses_distribution',
      data.id_expenses_distribution
    );

    $(`#selectNameProduct option:contains(${data.product})`).prop(
      'selected',
      true
    );
    $(`#refProduct option:contains(${data.reference})`).prop('selected', true);
    $('#undVendidas').val(data.units_sold.toLocaleString());
    $('#volVendidas').val(data.turnover.toLocaleString());
    $('#expensesToDistribution').val(data.assignable_expense.toLocaleString());

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateExpensesDistribution = () => {
    let data = $('#formExpensesDistribution').serialize();
    assignableExpense = $('#assignableExpense').val();
    idExpensesDistribution = sessionStorage.getItem('id_expenses_distribution');
    data =
      data +
      '&assignableExpense=' +
      assignableExpense +
      '&idExpensesDistribution=' +
      idExpensesDistribution;

    $.post(
      '../../api/updateExpensesDistribution',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar gasto */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExpensesDistribution.fnGetData(row);

    let id_expenses_distribution = data.id_expenses_distribution;

    let idProduct = data.id_product;

    dataExpensesDistribution = {};
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
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardExpensesDistribution').hide(800);
      $('#formExpensesDistribution').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblExpensesDistribution').DataTable().clear();
    $('#tblExpensesDistribution').DataTable().ajax.reload();
  }
});
