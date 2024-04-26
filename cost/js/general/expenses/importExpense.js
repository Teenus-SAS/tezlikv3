$(document).ready(function () {
  let selectedFile;

  $('.cardImportExpensesAssignation').hide();

  $('#btnImportNewAssExpenses').click(function (e) {
    e.preventDefault();
    $('.cardCreateExpenses').hide(800);
    $('.cardImportExpensesAssignation').toggle(800);
  });

  $('#fileExpensesAssignation').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportExpensesAssignation').click(function (e) {
    e.preventDefault();

    let file = $('#fileExpensesAssignation').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formExpenses');

    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
         if (data.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExpensesAssignation').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['numero_cuenta', 'cuenta', 'valor', 'centro_produccion'];

        if (production_center == '0' || flag_production_center == '0')
          expectedHeaders.splice(3, 1);

        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExpensesAssignation').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let expenseToImport = data.map((item) => {
          let expenseValue = '';

          if (item.valor)
            expenseValue = item.valor.toString().replace('.', ',');

          if (production_center == '1' && flag_production_center == '1')
            production = item.centro_produccion;
          else
            production = 0;

          return {
            numberCount: item.numero_cuenta,
            count: item.cuenta,
            expenseValue: expenseValue,
            production: production,
          };
        });
        checkExpense(expenseToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkExpense = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/expenseDataValidation',
      data: { importExpense: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExpensesAssignation').val('');
          return false;
        }

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
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
              saveExpense(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileExpensesAssignation').val('');
            }
          },
        });
      },
    });
  };

  saveExpense = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addExpenses',
      data: { importExpense: data },
      success: function (r) {
        messageExpense(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsExpensesAssignation').click(function (e) {
    e.preventDefault();

    production_center == '1' && flag_production_center == '1' ?
      url = 'assets/formatsXlsx/Gastos(CP).xlsx' :
      url = 'assets/formatsXlsx/Gastos.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
