$(document).ready(function () {
  let selectedFile;

  $('.cardImportDistributionExpenses').hide();

  $('#btnImportNewExpensesDistribution').click(function (e) {
    e.preventDefault();
    $('.cardExpensesDistribution').hide(800);
    $('.cardImportDistributionExpenses').toggle(800);
  });

  $('#fileDistributionExpenses').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportDistributionExpenses').click(function (e) {
    e.preventDefault();

    file = $('#fileDistributionExpenses').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let expenseDistributionToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            unitsSold: item.unidades_vendidas,
            turnover: item.volumen_ventas,
          };
        });
        checkExpenseDistribution(expenseDistributionToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkExpenseDistribution = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/expenseDistributionDataValidation',
      data: { importExpenseDistribution: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          $('#fileDistributionExpenses').val('');
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
              saveExpenseDistribution(data);
            } else $('#fileDistributionExpenses').val('');
          },
        });
      },
    });
  };

  saveExpenseDistribution = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addExpensesDistribution',
      data: { importExpenseDistribution: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportDistributionExpenses').hide(800);
          $('#formImportDistributionExpenses')[0].reset();
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblExpensesDistribution').DataTable().clear();
          $('#tblExpensesDistribution').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsDistributionExpenses').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Distribucion_Gastos.xlsx';

    link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
