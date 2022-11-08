$(document).ready(function () {
  let selectedFile;

  $('.cardImportExpensesAssignation').hide();

  $('#btnImportNewExpenses').click(function (e) {
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

    file = $('#fileExpensesAssignation').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let expenseToImport = data.map((item) => {
          return {
            numberCount: item.numero_cuenta,
            count: item.cuenta.trim(),
            expenseValue: item.valor,
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
            } else $('#fileExpensesAssignation').val('');
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
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportExpensesAssignation').hide(800);
          $('#formImportExpesesAssignation').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblExpenses').DataTable().clear();
          $('#tblExpenses').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsExpensesAssignation').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Gastos.xlsx';

    link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
