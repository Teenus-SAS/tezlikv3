$(document).ready(function () {
  /* Ocultar table de ingreso de datos volumen y unidades */
  $('.cardExpensesDistribution').hide();

  /* Abrir ventana para ingresar el volumen dy unidades de ventas para calcular gastos atribuibles al producto */
  $('#btnExpensesDistribution').click(async function (e) {
    e.preventDefault();

    if (flag_expense_distribution == 1) await loadExpensesDProducts();
    else await loadFamilies(2);

    $('.selectNameProduct option').removeAttr('selected');
    $('.refProduct option').removeAttr('selected');
    $(`.selectNameProduct option[value='0']`).prop('selected', true);
    $(`.refProduct option[value='0']`).prop('selected', true);

    $('.cardImportExpenses').hide(800);
    $('.cardNewProduct').hide(800);
    $('.cardExpenseRecover').hide(800);
    $('.cardAddNewFamily').hide(800);
    $('.cardAddProductFamily').hide(800);
    $('.cardTblFamilies').hide(800);
    $('#btnAssignExpenses').html('Asignar');

    sessionStorage.removeItem('id_expenses_distribution');

    $('#undVendidas').val('');
    $('#volVendidas').val('');

    let tables = document.getElementById('tblExpenses');

    let attr = tables;
    attr.style.width = '100%';
    attr = tables.firstElementChild;
    attr.style.width = '100%';

    $('.cardTblExpensesDistribution').show(800);
    $('.cardExpensesDistribution').toggle(800);
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

    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpensesDistribution'));
    let data = dataExpenses.find(item => item.id_expenses_distribution == this.id);

    // let row = $(this).parent().parent()[0];
    // let data = tblExpensesDistribution.fnGetData(row);

    sessionStorage.setItem('id_expenses_distribution', data.id_expenses_distribution);
    sessionStorage.setItem('newProduct', data.new_product);

    $('#EDRefProduct').empty();
    $('#EDNameProduct').empty();

    $('#EDRefProduct').append(
      `<option value ='${data.id_product}'> ${data.reference} </option>`
    );

    $('#EDNameProduct').append(
      `<option value ='${data.id_product}'> ${data.product} </option>`
    );

    if (flag_expense_distribution == 2) {
      $('#familiesDistribute').empty();
      $('#familiesDistribute').append(
        `<option value=${data.id_family}> ${data.family}</option>`
      );
    }

    $('#undVendidas').val(data.units_sold);
    $('#volVendidas').val(data.turnover);

    
    if (production_center == '1' && flag_production_center == '1') {
      if (data.id_production_center == 0) {
        var selectElement = document.getElementById("selectProductionCenterED"); 
        selectElement.selectedIndex = 0;
      } else
        $(`#selectProductionCenterED option[value=${data.id_production_center}]`).prop("selected", true);

      dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
      data = dataExpenses.filter(item => item.id_production_center == data.id_production_center);
      let totalExpense = 0;

      data.forEach(item => {
        totalExpense += parseFloat(item.expense_value)
      });

      $('#expensesToDistribution').val(`$ ${totalExpense.toLocaleString('es-CO', { maximumFractionDigits: 2 })}`);
    }

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
    let family = parseInt($('#familiesDistribute').val());
    let unitExp = parseFloat($('#undVendidas').val());
    let volExp = parseFloat($('#volVendidas').val());
    // let expense = $('#expensesToDistribution').val();
    // expense = strReplaceNumber($('#expensesToDistribution').val());
    let expense = $('#expensesToDistribution').val();
    expense = parseFloat(strReplaceNumber(expense.replace('$ ', '')));
    let productionCenter = 0;

    let data = refProduct * nameProduct;
    
    if (production_center == '1' && flag_production_center == '1'){
      productionCenter = parseFloat($('#selectProductionCenterED').val());
      data = data * productionCenter;
    }
    
    if (flag_expense_distribution == 2) data = family //* unitExp * volExp;
    
    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }
    
    let dataExpense = new FormData(formExpensesDistribution);
    
    if (idExpense != '' || idExpense != null) {
      dataExpense.append('expense', expense);
      dataExpense.append('idExpensesDistribution', idExpense);
      dataExpense.append('newProduct', sessionStorage.getItem('newProduct'));
    } 
    dataExpense.append('production', productionCenter);
    
    let resp = await sendDataPOST(url, dataExpense);
    let op = 1;
    if (flag_expense_distribution == 2) op = 3;
    messageDistribution(resp, op);
  };

  /* Eliminar gasto */

  deleteExpenseDistribution = (id) => {
    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpensesDistribution'));
    let data = dataExpenses.find(item => item.id_expenses_distribution == id);

    // let row = $(this.activeElement).parent().parent()[0];
    // data = tblExpensesDistribution.fnGetData(row);

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
              messageDistribution(data, 1);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageDistribution = async (data, op) => {
    $('#fileExpenses').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    
    if (data.success == true) {
      $('.cardImportExpenses').hide(800);
      $('#formImportExpenses').trigger('reset');
      $('.cardExpensesDistribution').hide(800);
      $('.cardAddNewFamily').hide(800);
      $('.cardAddProductFamily').hide(800);
      $('.cardExpenseRecover').hide(800);
      $('.cardNewProduct').hide(800);
      $('#formExpensesDistribution').trigger('reset');
      $('#formFamily').trigger('reset');
      $('#formExpenseRecover').trigger('reset');
      $('#modalExpenseDistributionByFamily').modal('hide');
      if (production_center == '1' && flag_production_center == '1') {
        // Obtener el elemento select
        var selectElement = document.getElementById("selectProductionCenterED");
        // Establecer la primera opción como seleccionada por defecto
        selectElement.selectedIndex = 0;
      }
      if (op == 1) {
        await loadExpensesDProducts();
        await loadFamilies(2);
      } else if (op == 2) await loadExpensesRProducts();
      else if (op == 3) await loadFamilies(1);
      else if (op == 4) {
        await loadExpensesDFamiliesProducts();
        // await loadTableProductsFamilies();
      }

      updateTable(op);
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  async function updateTable(op) {
    // if (op == 1) loadTableExpensesDistribution();
    // else if (op == 2) loadTableExpenseRecover();
    // else if (op == 3) {
    //   await loadTableFamilies(); 
    //   await loadTableExpensesDistributionFamilies();
    // }
    if (op == 1)
      loadAllDataDistribution();
    if (op == 2) {
      if ($.fn.dataTable.isDataTable("#tblExpenses")) {
        $('#tblExpenses').DataTable().clear();
        $('#tblExpenses').DataTable().ajax.reload();
      }

      if ($.fn.dataTable.isDataTable("#tblFamilies")) {
        $('#tblFamilies').DataTable().clear();
        $('#tblFamilies').DataTable().ajax.reload();
      }
    }
  }
});
