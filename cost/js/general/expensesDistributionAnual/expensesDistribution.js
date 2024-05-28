$(document).ready(function () {
  /* Ocultar table de ingreso de datos volumen y unidades */
  $('.cardExpensesDistributionAnual').hide();

  /* Abrir ventana para ingresar el volumen dy unidades de ventas para calcular gastos atribuibles al producto */
  $('#btnExpensesDistributionAnual').click(async function (e) {
    e.preventDefault();

    // if (flag_expense_distribution == 1) await loadExpensesDAProducts();
    // else await loadFamilies(2);
    await loadExpensesDAProducts();

    $('.selectNameProduct option').removeAttr('selected');
    $('.refProduct option').removeAttr('selected');
    $(`.selectNameProduct option[value='0']`).prop('selected', true);
    $(`.refProduct option[value='0']`).prop('selected', true);

    $('.cardImportExpensesAnual').hide(800);
    // $('.cardNewProduct').hide(800);
    // $('.cardExpenseRecover').hide(800);
    // $('.cardAddNewFamily').hide(800);
    // $('.cardAddProductFamily').hide(800);
    // $('.cardTblFamilies').hide(800);
    $('#btnAssignExpensesAnual').html('Asignar');

    sessionStorage.removeItem('id_expense_distribution_anual');

    $('#undAVendidas').val('');
    $('#volAVendidas').val('');

    let tables = document.getElementById('tblExpensesDistributionAnual');

    let attr = tables;
    attr.style.width = '100%';
    attr = tables.firstElementChild;
    attr.style.width = '100%';

    // $('.cardTblExpensesDistribution').show(800);
    $('.cardExpensesDistributionAnual').toggle(800);
  });

  $('#btnAssignExpensesAnual').click(function (e) {
    e.preventDefault();

    let expensesToDistributionAnual = $('#expensesToDistributionAnual').val();

    if (expensesToDistributionAnual == '$ 0' || !expensesToDistributionAnual) {
      toastr.error('Asigne un gasto primero antes de distribuir');
      return false;
    }

    let idExpensesDistribution = sessionStorage.getItem(
      'id_expense_distribution_anual'
    );

    if (idExpensesDistribution == '' || idExpensesDistribution == null) {
      checkDataExpenseDistributionA(
        '/api/addExpensesDistributionAnual',
        idExpensesDistribution
      );
    } else {
      checkDataExpenseDistributionA(
        '/api/updateExpensesDistributionAnual',
        idExpensesDistribution
      );
    }
  });

  /* Actualizar gasto */
  $(document).on('click', '.updateExpenseDistributionAnual', function (e) {
    $('.cardImportExpensesAnual').hide(800);
    // $('.cardExpenseRecover').hide(800);
    $('.cardExpensesDistributionAnual').show(800);
    $('#btnAssignExpensesAnual').html('Actualizar');

    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpensesDistributionA'));
    let data = dataExpenses.find(item => item.id_expense_distribution_anual == this.id);

    // let row = $(this).parent().parent()[0];
    // let data = tblExpensesDistribution.fnGetData(row);

    sessionStorage.setItem('id_expense_distribution_anual', data.id_expense_distribution_anual);
    sessionStorage.setItem('newProduct', data.new_product);

    $('#EDARefProduct').empty();
    $('#EDANameProduct').empty();

    $('#EDARefProduct').append(
      `<option value ='${data.id_product}'> ${data.reference} </option>`
    );

    $('#EDANameProduct').append(
      `<option value ='${data.id_product}'> ${data.product} </option>`
    );

    // if (flag_expense_distribution == 2) {
    //   $('#familiesDistribute').empty();
    //   $('#familiesDistribute').append(
    //     `<option value=${data.id_family}> ${data.family}</option>`
    //   );
    // }

    $('#undAVendidas').val(data.units_sold);
    $('#volAVendidas').val(data.turnover);

    // if (production_center == '1' && flag_production_center == '1') {
    //   if (data.id_production_center == 0) {
    //     var selectElement = document.getElementById("selectProductionCenterED"); 
    //     selectElement.selectedIndex = 0;
    //   } else
    //     $(`#selectProductionCenterED option[value=${data.id_production_center}]`).prop("selected", true);

    //   dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
    //   data = dataExpenses.filter(item => item.id_production_center == data.id_production_center);
    //   let totalExpense = 0;

    //   data.forEach(item => {
    //     totalExpense += parseFloat(item.expense_value)
    //   });

    //   $('#expensesToDistributionAnual').val(`$ ${totalExpense.toLocaleString('es-CO', { maximumFractionDigits: 2 })}`);
    // }

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision de data gasto */
  const checkDataExpenseDistributionA = async (url, idExpense) => {
    let refProduct = parseInt($('#EDARefProduct').val());
    let nameProduct = parseInt($('#EDANameProduct').val());
    // let family = parseInt($('#familiesDistribute').val());
    let unitExp = parseFloat($('#undAVendidas').val());
    let volExp = parseFloat($('#volAVendidas').val());
    // let expense = $('#expensesToDistributionAnual').val();
    // expense = strReplaceNumber($('#expensesToDistributionAnual').val());
    let expense = $('#expensesToDistributionAnual').val();
    expense = parseFloat(strReplaceNumber(expense.replace('$ ', '')));
    // let productionCenter = 0;

    let data = refProduct * nameProduct;
    
    // if (production_center == '1' && flag_production_center == '1'){
    //   productionCenter = parseFloat($('#selectProductionCenterED').val());
    //   data = data * productionCenter;
    // }
    
    // if (flag_expense_distribution == 2) data = family //* unitExp * volExp;
    
    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }
    
    let dataExpense = new FormData(formExpensesDistributionAnual);
    
    if (idExpense != '' || idExpense != null) {
      dataExpense.append('expense', expense);
      dataExpense.append('idExpensesDistribution', idExpense);
      // dataExpense.append('newProduct', sessionStorage.getItem('newProduct'));
    } 
    // dataExpense.append('production', productionCenter);
    
    let resp = await sendDataPOST(url, dataExpense);
    // let op = 1;
    // if (flag_expense_distribution == 2) op = 3;
    messageDistributionA(resp);
  };

  /* Eliminar gasto */
  deleteExpenseDistributionA = (id) => {
    let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpensesDistributionA'));
    let data = dataExpenses.find(item => item.id_expense_distribution_anual == id);

    // let row = $(this.activeElement).parent().parent()[0];
    // data = tblExpensesDistribution.fnGetData(row);

    let id_expense_distribution_anual = data.id_expense_distribution_anual;

    let idProduct = data.id_product;
    let dataExpensesDistribution = {};

    dataExpensesDistribution['idExpensesDistribution'] =
      id_expense_distribution_anual;
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
            '../../api/deleteExpensesDistributionAnual',
            dataExpensesDistribution,
            function (data, textStatus, jqXHR) {
              messageDistributionA(data, 1);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageDistributionA = async (data) => {
    $('#fileExpenses').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    
    if (data.success == true) {
      $('.cardImportExpensesAnual').hide(800);
      $('#formImportExpensesAnual').trigger('reset');
      $('.cardExpensesDistributionAnual').hide(800);
      // $('.cardAddNewFamily').hide(800);
      // $('.cardAddProductFamily').hide(800);
      // $('.cardExpenseRecover').hide(800);
      // $('.cardNewProduct').hide(800);
      $('#formExpensesDistributionAnual').trigger('reset');
      // $('#formFamily').trigger('reset');
      // $('#formExpenseRecover').trigger('reset');
      // $('#modalExpenseDistributionByFamily').modal('hide');
      // if (production_center == '1' && flag_production_center == '1') {
      //   // Obtener el elemento select
      //   var selectElement = document.getElementById("selectProductionCenterED");
      //   // Establecer la primera opción como seleccionada por defecto
      //   selectElement.selectedIndex = 0;
      // }
      // if (op == 1) {
      $('.loading').show(800);
      document.body.style.overflow = 'hidden';

      await loadExpensesDAProducts();
        
      setTimeout(() => {
        $('.loading').hide(800);
        document.body.style.overflow = '';
      }, 3500);
      // } else if (op == 2) await loadExpensesRProducts();
      // else if (op == 3) await loadFamilies(1);
      // else if (op == 4) {
      //   await loadExpensesDFamiliesProducts();
      //   // await loadTableProductsFamilies();
      // }

      await updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  async function updateTable() {
    // if (op == 1) loadTableExpensesDistribution();
    // else if (op == 2) loadTableExpenseRecover();
    // else if (op == 3) {
    //   await loadTableFamilies(); 
    //   await loadTableExpensesDistributionFamilies();
    // }
    // if (op == 1)
    loadAllDataDistributionA();
    // if (op == 2) {
    //   // if ($.fn.dataTable.isDataTable("#tblExpenses")) {
    //   //   $('#tblExpenses').DataTable().clear();
    //   //   $('#tblExpenses').DataTable().ajax.reload();
    //   // }
    //   loadTableExpenseRecover();

    //   if ($.fn.dataTable.isDataTable("#tblFamilies")) {
    //     $('#tblFamilies').DataTable().clear();
    //     $('#tblFamilies').DataTable().ajax.reload();
    //   }
    // }
  }
});
