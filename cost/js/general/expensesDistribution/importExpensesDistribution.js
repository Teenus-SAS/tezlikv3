$(document).ready(function () {
  let selectedFile;

  $('.cardImportExpenses').hide();

  $('#btnImportNewExpenses').click(function (e) {
    e.preventDefault();
    $('.cardExpensesDistribution').hide(800);
    $('.cardExpenseRecover').hide(800);
    $('#lblImprotExpense').html(this.value);
    $('.cardImportExpenses').toggle(800);
  });

  $('#fileExpenses').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportExpenses').click(function (e) {
    e.preventDefault();

    let expensesToDistribution = $('#expensesToDistribution').val();

    if (expensesToDistribution == '$ 0' || !expensesToDistribution) {
      $('#fileExpenses').val('');
      toastr.error('Asigne un gasto primero antes de distribuir');
      return false;
    }

    let file = $('#fileExpenses').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let expenseToImport = data.map((item) => {
          if (option == 1) {
            url = '/api/expenseDistributionDataValidation';
            let unitsSold = '';
            let turnover = '';

            if(item.unidades_vendidas)
              unitsSold = item.unidades_vendidas.toString().replace('.', ',');
            if(item.volumen_ventas)
              turnover = item.volumen_ventas.toString().replace('.', ',');
            
            return {
              referenceProduct: item.referencia_producto,
              product: item.producto,
              unitsSold: unitsSold,
              turnover: turnover,
            };
          } else if (option == 2) {
            url = '/api/expenseRecoverDataValidation';
            return {
              referenceProduct: item.referencia_producto,
              product: item.producto,
              percentage: item.porcentaje_recuperado,
            };
          }
        });
        checkExpense(expenseToImport, url);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkExpense = (data, url) => {
    $.ajax({
      type: 'POST',
      url: url,
      data: { importExpense: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          $('#fileExpenses').val('');
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
              option == 1
                ? (url = '/api/addExpensesDistribution')
                : (url = '/api/addExpenseRecover');

              saveExpenses(data, url);
            } else $('#fileExpenses').val('');
          },
        });
      },
    });
  };

  saveExpenses = (data, url) => {
    $.ajax({
      type: 'POST',
      url: url,
      data: { importExpense: data },
      success: function (r) {
        /* Mensaje de exito */
          $('#fileExpenses').val('');

        if (r.success == true) {
          $('.cardImportExpenses').hide(800);
          $('#formImportExpenses').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        async function updateTable() {
          if ($.fn.dataTable.isDataTable('#tblExpenses')) {
            $('#tblExpenses').DataTable().destroy();
            $('#tblExpenses').empty();
          }
          if (flag_expense_distribution == 2) {
            await loadTableFamilies();
            await loadTableExpensesDistributionFamilies();
          } else
            option == 1
              ? loadTableExpensesDistribution()
              : loadTableExpenseRecover();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsExpenses').click(async function (e) {
    e.preventDefault();

    // option == 1
    //   ? (url = 'assets/formatsXlsx/Distribucion_Gastos.xlsx')
    //   : (url = 'assets/formatsXlsx/Recuperacion_Gastos.xlsx');

    // let link = document.createElement('a');
    // link.target = '_blank';

    // link.href = url;
    // document.body.appendChild(link);
    // link.click();

    // document.body.removeChild(link);
    // delete link;
    /* Tipo de gasto */
    let wb = XLSX.utils.book_new();

    let data = [];
    if (flag_expense == '1') {
      if (flag_expense_distribution == '1') {
        namexlsx = 'distribucion_gastos.xlsx';
        url = '/api/expensesDistribution';
        op = 1;
      }
      else {
        namexlsx = 'distribucion_gastos_familia.xlsx';
        url = '/api/expensesDistributionFamilies';
        op = 2;
      }
    } else {
      namexlsx = 'recuperacion_gastos.xlsx';
      url = '/api/expensesRecover';
      op = 3;
    }
    dataTypeExpense = await searchData(url);

    if (op == 1) {
      if (dataTypeExpense.length > 0) {
        for (i = 0; i < dataTypeExpense.length; i++) {
          data.push({
            referencia_producto: dataTypeExpense[i].reference,
            producto: dataTypeExpense[i].product,
            unidades_vendidas: parseFloat(dataTypeExpense[i].units_sold),
            total_ventas: parseFloat(dataTypeExpense[i].turnover),
          });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Distribucion Producto');
      }
    }
    else if (op == 2) {
      if (dataTypeExpense.length > 0) {
        for (i = 0; i < dataTypeExpense.length; i++) {
          data.push({
            // referencia: dataProducts[i].id_family,
            familia: dataTypeExpense[i].family,
            unidades_vendidas: parseFloat(dataTypeExpense[i].units_sold),
            total_ventas: parseFloat(dataTypeExpense[i].turnover),
          });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Distribucion Familia');
      }
    }
    else {
      if (dataTypeExpense.length > 0) {
        for (i = 0; i < dataTypeExpense.length; i++) {
          data.push({
            reference_producto: dataProducts[i].reference,
            producto: dataTypeExpense[i].product,
            porcentaje_recuperado: parseFloat(dataTypeExpense[i].expense_recover),
          });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Recuperacion Gasto');
      }
    }
    
    XLSX.writeFile(wb, namexlsx);
  });
});
