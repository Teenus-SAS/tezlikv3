
$('.cardCreateExpenses').hide();

$('.selectNavigation').click(function (e) {
  e.preventDefault();

  $('.cardsGeneral').hide();

  let option = this.id;

  switch (option) {
    case 'sExpenses':
      $('.cardExpenses').show();
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
      break;
    case 'sExpensesA':
      $('.cardExpensesAnual').show();
      break;
    case 'distribution':
      $('.cardExpenseDistribution').show();

      if (flag_expense == '1')
        $('.cardNewProducts').show();

      if (production_center == '1' && flag_production_center == '1') {
        // Obtener el elemento select
        var selectElement = document.getElementById("selectProductionCenterED");
        // Establecer la primera opción como seleccionada por defecto
        selectElement.selectedIndex = 0;
      }
      break;
    case 'sDistributionA':
      // document.getElementsByClassName('nav-link').className = 'nav-link selectNavigation';

      $('.cardExpenseDistributionAnual').show();
      break;
    case 'sProductionCenter':
      $('.cardProductionCenter').show();
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

$('#btnNewExpense').click(function (e) {
  e.preventDefault();

  $('.cardImportExpensesAssignation').hide(800);
  $('.cardCreateExpenses').toggle(800);
  $('#btnCreateExpense').html('Crear');

  sessionStorage.removeItem('id_expense');

  $('#formCreateExpenses').trigger('reset');
  let dataExpenses = JSON.parse(sessionStorage.getItem('dataExpenses'));
  loadPUC(dataExpenses);

  /* var summarizedExpenses = sumAndGroupExpenses(dataExpenses);
  summarizedExpenses.sort((a, b) => a.puc.localeCompare(b.puc)); */

  //loadTblAssExpenses(summarizedExpenses, 1);
});

//Actualizar Gasto General
$('#btnCreateExpense').click(function (e) {
  e.preventDefault();

  const idExpense = sessionStorage.getItem('id_expense');
  const endpoint = (!idExpense)
    ? '/api/addExpenses'
    : '/api/updateExpenses';

  checkDataExpense(endpoint, idExpense || '');
});

$(document).on('click', '.updateExpenses', function (e) {
  $('.cardImportExpensesAssignation').hide(800);
  $('.cardCreateExpenses').show(800);
  $('#btnCreateExpense').html('Actualizar');

  //Obtener data
  let row = $(this).parent().parent()[0];
  let data = tblAssExpenses.fnGetData(row);

  sessionStorage.setItem('id_expense_product_center', data.id_expense_product_center);
  sessionStorage.setItem('id_expense', data.id_expense);

  //Cargar select
  setSelectedPUCOption({ id_puc: data.id_puc, number_count: data.number_count, count: data.count });


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

$(document).on('click', '.deleteExpenses', function (e) {
  let row = $(this).closest('tr')[0];
  let data = tblAssExpenses.fnGetData(row);
  const op = $(this).data('op');

  if (op == 2) {
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
});

/* Mensaje de exito */
messageExpense = async (data) => {
  if (data.reload) {
    location.reload();
  }

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

