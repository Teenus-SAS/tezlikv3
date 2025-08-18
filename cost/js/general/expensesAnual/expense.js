$(document).ready(function () {
  $('.cardCreateExpensesAnual').hide();

  $('.selectExpenses').change(function (e) {
    e.preventDefault();

    $('.cardsGeneral').hide();
    $('.navExpenseMonth').hide();
    $('.navExpenseAnual').hide();
    let elements = document.getElementsByClassName('selectNavigation');

    for (let i = 0; i < elements.length; i++) {
      elements[i].className = 'nav-link selectNavigation';
    }

    document.getElementById('sExpenses').className = 'nav-link active selectNavigation';
    document.getElementById('sExpensesA').className = 'nav-link active selectNavigation';

    let option = this.value;
    $('.selectExpenses').val(option);

    switch (option) {
      case '1':// Mensual
        $('.navExpenseMonth').show();
        $('.cardExpenses').show();
        break;
      case '2':// Mensual
        $('.navExpenseAnual').show();
        $('.cardExpensesAnual').show();
        break;
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

  $('#btnNewExpenseAnual').click(function (e) {
    e.preventDefault();

    $('.cardImportExpensesAssignationAnual').hide(800);
    $('.cardCreateExpensesAnual').toggle(800);
    $('#btnCreateExpenseAnual').html('Crear');

    sessionStorage.removeItem('id_expense_anual');

    $('#formCreateExpensesAnual').trigger('reset');
  });

  $('#btnCreateExpenseAnual').click(function (e) {
    e.preventDefault();

    let idExpense = sessionStorage.getItem('id_expense_anual');

    if (idExpense == '' || idExpense == null) {
      checkDataExpenseA('/api/expensesAnual/addExpensesAnual', idExpense);
    } else {
      checkDataExpenseA('/api/expensesAnual/updateExpensesAnual', idExpense);
    }
  });

  $(document).on('click', '.updateExpensesAnual', function (e) {
    $('.cardImportExpensesAssignationAnual').hide(800);
    $('.cardCreateExpensesAnual').show(800);
    $('#btnCreateExpenseAnual').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblAssExpensesAnual.fnGetData(row);

    sessionStorage.setItem('id_expense_anual', data.id_expense_anual);
    $(`#idPucAnual option:contains(${data.number_count} - ${data.count})`).prop(
      'selected',
      true
    );

    $('#expenseValueAnual').val(data.expense_value);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data gasto */
  const checkDataExpenseA = async (url, idExpense) => {
    let puc = parseInt($('#idPucAnual').val());
    let value = parseFloat($('#expenseValueAnual').val());


    isNaN(value) ? value = 0 : value;

    if (!puc || puc == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let count = $('#idPucAnual option:selected').text();

    while (count.includes('-')) {
      count = count.slice(0, -1);
    }

    if (count.length < 4) {
      toastr.error('Seleccione una Cuenta');
      return false;
    }

    let dataExpense = new FormData(formCreateExpensesAnual);

    dataExpense.append('expenseValue1', value);
    dataExpense.append('expenseValue', value);

    if (idExpense != '' || idExpense != null)
      dataExpense.append('idExpense', idExpense);

    let resp = await sendDataPOST(url, dataExpense);

    messageExpenseA(resp);
  };

  deleteExpenseDA = () => {
    // let data = dataExpenses.find(item => item.id_expense_anual == id); 
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblAssExpensesAnual.fnGetData(row);
    let id_expense_anual = data.id_expense_anual;


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
          $.get(`/api/expensesAnual/deleteExpensesAnual/${id_expense_anual}`, function (data, textStatus, jqXHR) {
            messageExpenseA(data);
          }
          );
        }
      },
    });
    // }
  };

  /* Mensaje de exito */
  messageExpenseA = (data) => {
    if (data.reload) {
      location.reload();
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileExpensesAssignation').val('');

    if (data.success == true) {
      $('.cardImportExpensesAssignationAnual').hide(800);
      $('#formImportExpesesAssignationAnual').trigger('reset');
      $('.cardCreateExpensesAnual').hide(800);
      $('#formCreateExpensesAnual').trigger('reset');
      loadAllDataExpensesAnual();
      getExpenseAnual();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
