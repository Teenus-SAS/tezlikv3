$(document).ready(function () {
  $('.cardExpenseRecover').hide();

  $('#btnNewExpenseRecover').click(function (e) {
    e.preventDefault();
    $('.selectNameProduct option').removeAttr('selected');
    $('.refProduct option').removeAttr('selected');
    $(`.selectNameProduct option[value='0']`).prop('selected', true);
    $(`.refProduct option[value='0']`).prop('selected', true);

    $('.cardImportDistributionExpenses').hide(800);
    $('.cardExpensesDistribution').hide(800);
    $('.cardExpenseRecover').toggle(800);
    $('#btnAssignExpenses').html('Guardar');

    sessionStorage.removeItem('id_expense_recover');

    $('#percentage').val('');
  });

  $('#btnExpenseRecover').click(function (e) {
    e.preventDefault();

    let id_expense_recover = sessionStorage.getItem('id_expense_recover');

    if (id_expense_recover == '' || !id_expense_recover) {
      let idProduct = parseInt($('#ERNameProduct').val());
      let percentage = parseInt($('#percentage').val());

      let data = idProduct * percentage;

      if (!data || data == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      let expensesDistribution = $('#formExpenseRecover').serialize();

      $.post(
        '../api/addExpenseRecover',
        expensesDistribution,
        function (data, textStatus, jqXHR) {
          message(data, 2);
        }
      );
    } else updateExpenseRecover();
  });

  /* Actualizar recuperacion gasto */
  $(document).on('click', '.updateExpenseRecover', function () {
    $('.cardImportDistributionExpenses').hide(800);
    $('.cardExpensesDistribution').hide(800);
    $('.cardExpenseRecover').show(800);
    $('#btnExpenseRecover').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblExpenseRecover.fnGetData(row);

    sessionStorage.setItem('id_expense_recover', data.id_expense_recover);

    $(`#ERNameProduct option:contains(${data.product})`).prop('selected', true);
    $(`#ERRefProduct option:contains(${data.reference})`).prop(
      'selected',
      true
    );
    $('#percentage').val(data.expense_recover);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateExpenseRecover = () => {
    let data = $('#formExpenseRecover').serialize();
    let id_expense_recover = sessionStorage.getItem('id_expense_recover');

    data += `&idExpenseRecover=${id_expense_recover}`;

    $.post(
      '../api/updateExpenseRecover',
      data,
      function (data, textStatus, jqXHR) {
        message(data, 2);
      }
    );
  };

  /* Eliminar recuperacion de gasto */
  deleteExpenseRecover = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExpenseRecover.fnGetData(row);

    let id_expense_recover = data.id_expense_recover;

    let idProduct = data.id_product;
    let dataExpenseRecover = {};

    dataExpenseRecover['idExpenseRecover'] = id_expense_recover;
    dataExpenseRecover['idProduct'] = idProduct;

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
            '../../api/deleteExpenseRecover',
            dataExpenseRecover,
            function (data, textStatus, jqXHR) {
              message(data, 2);
            }
          );
        }
      },
    });
  };
});
