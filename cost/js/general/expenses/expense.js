$(document).ready(function () {
  $('.cardCreateExpenses').hide();

  $('.selectNavigation').click(function (e) {
    e.preventDefault();

    if (this.id == 'sExpenses') {
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

      if (production_center == '1' && flag_production_center == '1') {       
        // Obtener el elemento select
        var selectElement = document.getElementById("selectProductionCenterExpenses1");
        // Establecer la primera opción como seleccionada por defecto
        selectElement.selectedIndex = 0;

        let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
        var summarizedExpenses = sumAndGroupExpenses(dataExpenses); 
        summarizedExpenses.sort((a, b) => a.puc.localeCompare(b.puc));

        loadTblAssExpenses(summarizedExpenses, 1);
      } 

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

      if (production_center == '1' && flag_production_center == '1') {       
        // Obtener el elemento select
        var selectElement = document.getElementById("selectProductionCenterED");
        // Establecer la primera opción como seleccionada por defecto
        selectElement.selectedIndex = 0;
      }
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
    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
    var summarizedExpenses = sumAndGroupExpenses(dataExpenses);
    summarizedExpenses.sort((a, b) => a.puc.localeCompare(b.puc));

    loadTblAssExpenses(summarizedExpenses, 1);
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
    // let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));

    // production_center == '1' && flag_production_center == '1' ? id = 'id_expense_product_center' : id = 'id_expense';
    // let data = dataExpenses.find(item => item[id] == this.id);

    sessionStorage.setItem('id_expense_product_center', data.id_expense_product_center);
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
      selectProductionCenter = 0;
  
    isNaN(value) ? value = 0 : value;
    
    if (!puc || puc == ''|| isNaN(selectProductionCenter)) {
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

    if (!idExpense && production_center == '1' && flag_production_center == '1') {
      let data = JSON.parse(sessionStorage.getItem('dataExpenses'));

      let arr = data.find(item => item.id_puc == puc);

      if (arr) { 
        url = '/api/updateExpenses';

        idExpense = arr.id_expense;
      }
    }
    
    dataExpense.append('expenseValue1', value);
    dataExpense.append('idExpenseProductionCenter', sessionStorage.getItem('id_expense_product_center'));

    dataExpense.append('expenseValue', value);
    dataExpense.append('production', selectProductionCenter);

    if (idExpense != '' || idExpense != null)
      dataExpense.append('idExpense', idExpense);

    let resp = await sendDataPOST(url, dataExpense);

    messageExpense(resp);
  };

  deleteFunction = (op) => {
    // let data = dataExpenses.find(item => item.id_expense == id); 
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblAssExpenses.fnGetData(row);
    
    if(op == 2){
      id_expense = data.id_expense_product_center;
      url = `/api/deleteExpenses/${id_expense}/2`;
    } else {
      id_expense = data.id_expense;
      url = `/api/deleteExpenses/${id_expense}/1`;
    }
    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
    let arr = dataExpenses.filter(item => item.id_expense == id_expense);
    
    // if (production_center == '1' && flag_production_center == '1') id_expense = data.id_expense_product_center;
    if (production_center == '1' && flag_production_center == '1' && arr.length > 1 && op == 1) {
      var options = ''

      for (let i = 0; i < arr.length; i++) {
        options += `<option value="${arr[i].id_expense_product_center}">${arr[i].production_center}</option>`;        
      }

      bootbox.confirm({
        title: 'Eliminar',
        message: `Está seguro de eliminar este gasto? Esta acción no se puede reversar.<br><br>
          Seleccione la unidad de produccion que desea eliminar ese gasto.<br><br>
          <select id="selectPCenter" class="form-control">
            <option disabled selected> Seleccionar</option>
            ${options}
          </select>`,
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
            let id_expense = $('#selectPCenter').val();

            if (!id_expense) {
              toastr.error('Seleccione unidad de produccion');
              return false;
            }

            url = `/api/deleteExpenses/${id_expense}/2`;

            $.get(url, function (data, textStatus, jqXHR) {
                messageExpense(data);
              }
            );
          }
        },
      });
    } else {
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
            $.get(url, function (data, textStatus, jqXHR) {
                messageExpense(data);
              }
            );
          }
        },
      });
    }
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
      if (production_center == '1' && flag_production_center == '1') { 
        // Obtener el elemento select
        var selectElement = document.getElementById("selectProductionCenterExpenses");
        // Establecer la primera opción como seleccionada por defecto
        selectElement.selectedIndex = 0;
      }
      loadAllDataExpenses();
      getExpense();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
