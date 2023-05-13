$(document).ready(function () {
  $('.cardCreateExpenses').hide();

  $('#btnNewExpense').click(function (e) {
    e.preventDefault();

    $('.cardImportExpensesAssignation').hide(800);
    $('.cardCreateExpenses').toggle(800);
    $('#btnCreateExpense').html('Crear');

    sessionStorage.removeItem('id_expense');

    $('#formCreateExpenses').trigger('reset');
  });

  $('#btnCreateExpense').click(function (e) {
    e.preventDefault();

    let idExpense = sessionStorage.getItem('id_expense');

    if (idExpense == '' || idExpense == null) {
      checkDataExpense('/api/addExpenses', idExpense);
    } else {
      checkDataExpense('/api/updateExpenses', idExpense);
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

    $('#expenseValue').val(data.expense_value.toLocaleString('es-CO'));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data gasto */
  checkDataExpense = async (url, idExpense) => {
    let puc = parseInt($('#idPuc').val());
    let value = $('#expenseValue').val();

    value = parseFloat(strReplaceNumber(value));

    let data = puc * value;

    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let count = $('#idPuc option:selected').text();

    while (count.includes('-')) {
      count = count.slice(0, -1);
    }

    if (count.length < 4) {
      toastr.error('Seleccione una Cuenta');
      return false;
    }

    let dataExpense = new FormData(formCreateExpenses);

    if (idExpense != '' || idExpense != null)
      dataExpense.append('idExpense', idExpense);

    let resp = await sendDataPOST(url, dataExpense);

    message(resp);
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
