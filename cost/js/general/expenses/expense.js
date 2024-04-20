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
      $('.cardNewProducts').hide();
      $('.cardProductionCenter').hide();
      $('.cardAddNewProductionCenter').hide();
    } else if (this.id == 'distribution') {
      $('.cardExpenseDistribution').show();
      
      if (flag_expense == '1')
        $('.cardNewProducts').show();
    
      $('.cardExpenses').hide();
      $('.cardCreateExpenses').hide();
      $('.cardImportExpensesAssignation').hide();
      $('.cardNewProduct').hide();
      $('.cardProductionCenter').hide();
      $('.cardAddNewProductionCenter').hide();
    } else {
      $('.cardProductionCenter').show();
      $('.cardAddNewProductionCenter').hide();
      $('.cardNewProduct').hide();
      $('.cardExpenses').hide();
      $('.cardImportExpensesAssignation').hide();
      $('.cardCreateExpenses').hide();
      $('.cardExpenseDistribution').hide();
      $('.cardAddNewFamily').hide();
      $('.cardAddProductFamily').hide();
      $('.cardExpensesDistribution').hide(); 
      $('.cardExpenseRecover').hide();
      $('.cardImportExpenses').hide();
      $('.cardNewProducts').hide();
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

    // let row = $(this).parent().parent()[0];
    // let data = tblAssExpenses.fnGetData(row);
    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
    let data = dataExpenses.find(item => item.id_expense == this.id);

    sessionStorage.setItem('id_expense', data.id_expense);
    $(`#idPuc option:contains(${data.number_count} - ${data.count})`).prop(
      'selected',
      true
    );

    if (production_center == '1' && flag_production_center == '1') {
      if (data.id_production_center == 0) {
        var selectElement = document.getElementById("selectProductionCenterExpenses");
        selectElement.selectedIndex = 0;
      } else
        $(`#selectProductionCenterExpenses option[value=${data.id_production_center}]`).prop("selected", true);
    }

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

    if (production_center == '1' && flag_production_center == '1')
      selectProductionCenter = parseFloat($('#selectProductionCenterExpenses').val());
    else
      selectProductionCenter = 1;
  
    isNaN(value) ? value = 0 : value;
    
    if (!puc || puc == ''||selectProductionCenter <= 0 || isNaN(selectProductionCenter)) {
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
    dataExpense.append('expenseValue', value);
    dataExpense.append('production', selectProductionCenter);

    if (idExpense != '' || idExpense != null)
      dataExpense.append('idExpense', idExpense);

    let resp = await sendDataPOST(url, dataExpense);

    messageExpense(resp);
  };

  deleteFunction = (id) => {
    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
    let data = dataExpenses.find(item => item.id_expense == id); 

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
      // Obtener el elemento select
      var selectElement = document.getElementById("selectProductionCenterExpenses");
      // Establecer la primera opción como seleccionada por defecto
      selectElement.selectedIndex = 0;

      loadAllDataExpenses();
      getExpense();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
