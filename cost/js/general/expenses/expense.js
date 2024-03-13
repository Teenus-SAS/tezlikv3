$(document).ready(function () {
  $('.cardCreateExpenses').hide();

  $('.selectNavigation').click(function (e) {
    e.preventDefault();

    if (this.id == 'expenses') {
      $('.cardExpenses').show();
      $('.cardExpenseDistribution').hide();
      $('.cardAddNewFamily').hide();
      $('.cardAddProductFamily').hide();
      $('.cardExpensesDistribution').hide();
      $('.cardExpensesDistribution').hide();
      $('.cardExpenseRecover').hide();
      $('.cardImportExpenses').hide();
    } else if (this.id == 'distribution') {
      $('.cardExpenseDistribution').show();
      $('.cardExpenses').hide();
      $('.cardCreateExpenses').hide();
      $('.cardImportExpensesAssignation').hide();
    }

    let tables = document.getElementsByClassName(
      'dataTable'
    );

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = '100%';
      attr = tables[i].firstElementChild;
      attr.style.width = '100%';
    }
  });
  
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
    let data = tblAssExpenses.fnGetData(row);

    sessionStorage.setItem('id_expense', data.id_expense);
    $(`#idPuc option:contains(${data.number_count} - ${data.count})`).prop(
      'selected',
      true
    );

    // let decimals = contarDecimales(data.expense_value);
    // let expense_value = formatNumber(data.expense_value, decimals);
    $('#expenseValue').val(data.expense_value);

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
    let value = parseFloat($('#expenseValue').val());

    // value = parseFloat(strReplaceNumber(value));

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

    messageExpense(resp);
  };

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblAssExpenses.fnGetData(row);

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
              messageExpense(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */
  messageExpense = (data) => {
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileExpensesAssignation').val('');
    
    if (data.success == true) {
      $('.cardImportExpensesAssignation').hide(800);
      $('#formImportExpesesAssignation').trigger('reset');
      $('.cardCreateExpenses').hide(800);
      $('#formCreateExpenses').trigger('reset');
      updateTable();
      getExpense();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblAssExpenses').DataTable().clear();
    $('#tblAssExpenses').DataTable().ajax.reload();
  }
});
