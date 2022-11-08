$(document).ready(function () {
  $('.cardCreateExpenses').hide();

  $('#btnNewExpense').click(function (e) {
    e.preventDefault();

    $('.cardImportExpensesAssignation').hide(800);
    $('.cardCreateExpenses').toggle(800);
    $('#btnCreateExpense').html('Crear');

    sessionStorage.removeItem('id_expense');

    $('#idPuc option:contains(Seleccionar)').prop('selected', true);
    $('#expenseValue').val('');
  });

  $('#btnCreateExpense').click(function (e) {
    e.preventDefault();

    let idExpense = sessionStorage.getItem('id_expense');

    if (idExpense == '' || idExpense == null) {
      puc = parseInt($('#idPuc').val());
      value = parseInt($('#expenseValue').val());

      data = puc * value;

      if (!data) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      expenses = $('#formCreateExpenses').serialize();

      $.post('/api/addExpenses', expenses, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateExpenses();
    }
  });

  $(document).on('click', '.updateExpenses', function (e) {
    $('.cardImportExpensesAssignation').hide(800);
    $('.cardCreateExpenses').show(800);
    $('#btnCreateExpense').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblExpenses.fnGetData(row);

    sessionStorage.setItem('id_expense', data.id_expense);
    $(`#idPuc option:contains(${data.number_count} - ${data.count})`).prop(
      'selected',
      true
    );

    $('#expenseValue').val(data.expense_value.toLocaleString());

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateExpenses = () => {
    let data = $('#formCreateExpenses').serialize();
    idExpense = sessionStorage.getItem('id_expense');
    data = data + '&idExpense=' + idExpense;

    $.post(
      '../../api/updateExpenses',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExpenses.fnGetData(row);

    let id_expense = data.id_expense;

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
          $.get(
            `../../api/deleteExpenses/${id_expense}`,
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
      $('.cardCreateExpenses').hide(800);
      $('#formCreateExpenses').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblExpenses').DataTable().clear();
    $('#tblExpenses').DataTable().ajax.reload();
  }
});
