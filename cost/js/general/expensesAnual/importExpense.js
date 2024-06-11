$(document).ready(function () {
  let selectedFile;

  $('.cardImportExpensesAssignationAnual').hide();

  $('#btnImportNewAssExpensesAnual').click(function (e) {
    e.preventDefault();
    $('.cardCreateExpensesAnual').hide(800);
    $('.cardImportExpensesAssignationAnual').toggle(800);
  });

  $('#fileExpensesAssignationAnual').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportExpensesAssignationAnual').click(function (e) {
    e.preventDefault();

    let file = $('#fileExpensesAssignationAnual').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formExpensesAnual');

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
        let arr = data.rowObject;

         if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExpensesAssignationAnual').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['numero_cuenta', 'cuenta', 'valor'];

        // if (production_center == '0' || flag_production_center == '0')
        //   expectedHeaders.splice(3, 1);

        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExpensesAssignationAnual').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let expenseToImport = arr.map((item) => {
          let expenseValue = '';

          if (item.valor)
            expenseValue = item.valor.toString().replace('.', ',');

          // if (production_center == '1' && flag_production_center == '1')
          //   production = item.centro_produccion;
          // else
          //   production = 0;

          return {
            numberCount: item.numero_cuenta,
            count: item.cuenta,
            expenseValue: expenseValue,
            // expenseValue1: expenseValue,
            // production: production,
          };
        });
        checkExpenseA(expenseToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkExpenseA = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/expenseAnualDataValidation',
      data: { importExpense: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExpensesAssignationAnual').val('');
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
              saveExpenseA(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileExpensesAssignationAnual').val('');
            }
          },
        });
      },
    });
  };

  const saveExpenseA = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addExpensesAnual',
      data: { importExpense: data },
      success: function (r) {
        messageExpenseA(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsExpensesAssignationAnual').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Gastos.xlsx';
    let newFileName = 'Gastos_Anual.xlsx';

    fetch(url)
      .then(response => response.blob())
      .then(blob => {
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = newFileName;

        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        URL.revokeObjectURL(link.href); // liberar memoria
      })
      .catch(console.error);
  });
});
