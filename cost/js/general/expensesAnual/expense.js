$(document).ready(function () {
  $('.cardCreateExpensesAnual').hide();
  
  $('#btnNewExpenseAnual').click(function (e) {
    e.preventDefault();

    $('.cardImportExpensesAssignationAnual').hide(800);
    $('.cardCreateExpensesAnual').toggle(800);
    $('#btnCreateExpenseAnual').html('Crear');

    sessionStorage.removeItem('id_expense_anual');

    $('#formCreateExpensesAnual').trigger('reset');
    // let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
    // var summarizedExpenses = sumAndGroupExpenses(dataExpenses);
    // summarizedExpenses.sort((a, b) => a.puc.localeCompare(b.puc));

    // loadTblAssExpenses(summarizedExpenses, 1);
  });

  $('#btnCreateExpenseAnual').click(function (e) {
    e.preventDefault();

    let idExpense = sessionStorage.getItem('id_expense_anual');

    if (idExpense == '' || idExpense == null) {
      checkDataExpenseA('/api/addExpensesAnual', idExpense);
    } else {
      checkDataExpenseA('/api/updateExpensesAnual', idExpense);
    }
  });

  $(document).on('click', '.updateExpensesAnual', function (e) {
    $('.cardImportExpensesAssignationAnual').hide(800);
    $('.cardCreateExpensesAnual').show(800);
    $('#btnCreateExpenseAnual').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblAssExpensesAnual.fnGetData(row);
    // let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));

    // production_center == '1' && flag_production_center == '1' ? id = 'id_expense_anual_product_center' : id = 'id_expense_anual';
    // let data = dataExpenses.find(item => item[id] == this.id);
 
    sessionStorage.setItem('id_expense_anual', data.id_expense_anual);
    $(`#idPucAnual option:contains(${data.number_count} - ${data.count})`).prop(
      'selected',
      true
    );

    // if (production_center == '1' && flag_production_center == '1') {
    //   if (data.id_production_center == 0) {
    //     var selectElement = document.getElementById("selectProductionCenterExpenses");
    //     selectElement.selectedIndex = 0;
    //   } else
    //     $(`#selectProductionCenterExpenses option[value=${data.id_production_center}]`).prop("selected", true);
    // }

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

    // if (production_center == '1' && flag_production_center == '1')
    //   selectProductionCenter = parseFloat($('#selectProductionCenterExpenses').val());
    // else
    //   selectProductionCenter = 0;
  
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

    // if (!idExpense && production_center == '1' && flag_production_center == '1') {
    //   let data = JSON.parse(sessionStorage.getItem('dataExpenses'));

    //   let arr = data.find(item => item.id_puc == puc);

    //   if (arr) { 
    //     url = '/api/updateExpenses';

    //     idExpense = arr.id_expense_anual;
    //   }
    // }
    
    dataExpense.append('expenseValue1', value);
    // dataExpense.append('idExpenseProductionCenter', sessionStorage.getItem('id_expense_anual_product_center'));

    dataExpense.append('expenseValue', value);
    // dataExpense.append('production', selectProductionCenter);

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

    // if(op == 2){
    //   id_expense_anual = data.id_expense_anual_product_center;
    //   url = `/api/deleteExpenses/${id_expense_anual}/2`;
    // } else {
    //   id_expense_anual = data.id_expense_anual;
    //   url = `/api/deleteExpenses/${id_expense_anual}/1`;
    // }
    // let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
    // let arr = dataExpenses.filter(item => item.id_expense_anual == id_expense_anual);
    
    // // if (production_center == '1' && flag_production_center == '1') id_expense_anual = data.id_expense_anual_product_center;
    // if (production_center == '1' && flag_production_center == '1' && arr.length > 1 && op == 1) {
    //   var options = ''

    //   for (let i = 0; i < arr.length; i++) {
    //     options += `<option value="${arr[i].id_expense_anual_product_center}">${arr[i].production_center}</option>`;        
    //   }

    //   bootbox.confirm({
    //     title: 'Eliminar',
    //     message: `Está seguro de eliminar este gasto? Esta acción no se puede reversar.<br><br>
    //       Seleccione la unidad de produccion que desea eliminar ese gasto.<br><br>
    //       <select id="selectPCenter" class="form-control">
    //         <option disabled selected> Seleccionar</option>
    //         ${options}
    //       </select>`,
    //     buttons: {
    //       confirm: {
    //         label: 'Si',
    //         className: 'btn-success',
    //       },
    //       cancel: {
    //         label: 'No',
    //         className: 'btn-danger',
    //       },
    //     },
    //     callback: function (result) {
    //       if (result == true) {
    //         let id_expense_anual = $('#selectPCenter').val();

    //         if (!id_expense_anual) {
    //           toastr.error('Seleccione unidad de produccion');
    //           return false;
    //         }

    //         url = `/api/deleteExpenses/${id_expense_anual}/2`;

    //         $.get(url, function (data, textStatus, jqXHR) {
    //             messageExpenseA(data);
    //           }
    //         );
    //       }
    //     },
    //   });
    // }
    // else {
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
            $.get(`/api/deleteExpensesAnual/${id_expense_anual}`, function (data, textStatus, jqXHR) {
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
