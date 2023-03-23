$(document).ready(function () {
  let selectedFile;

  $('.cardImportMultiproducts').hide();

  $('#btnImportNewMultiproducts').click(function (e) {
    e.preventDefault();

    $('.cardImportMultiproducts').show(800);
    $('.cardGraphicMultiproducts').hide(800);
    $('.cardTblBreakeven').show(800);
    $('.cardTblMultiproducts').show(800);
  });

  $('#fileMultiproducts').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportMultiproducts').click(function (e) {
    e.preventDefault();

    let file = $('#fileMultiproducts').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        multiproductsToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia,
            product: item.producto,
            soldUnit: item.unidades_vendidas,
          };
        });
        checkMultiproducts(multiproductsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  checkMultiproducts = async (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/multiproductsDataValidation',
      data: { importMultiproducts: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          return false;
        }

        try {
          if (resp[0].expense > 0) {
            $('#importExpense').val(resp[0].expense.toLocaleString('es-CO'));
            $('#importExpense').prop('disabled', true);
          } else {
            $('#importExpense').val();
            $('#importExpense').prop('disabled', false);
          }

          let tblMultiproductsImportBody = document.getElementById(
            'multiproductsImportBody'
          );

          for (let i = 0; i < resp.length; i++) {
            tblMultiproductsImportBody.insertAdjacentHTML(
              'beforeend',
              `<tr>
                <td>${i + 1}</td>
                <td>${resp[i].referenceProduct}</td>
                <td>${resp[i].product}</td>
                <td>${resp[i].soldUnit}</td>
               </tr>`
            );
          }

          $('#tblImportMultiproducts').dataTable({
            destroy: true,
            pageLength: 5,
          });

          $('#modalImportMultiproducts').modal('show');
        } catch (error) {
          console.log(error);
        }
      },
    });
  };

  $('#btnCloseImportProducts').click(function (e) {
    e.preventDefault();
    $('#fileMultiproducts').val('');
    $('#importExpense').val('');
    $('#multiproductsImportBody').empty();
    $('#modalImportMultiproducts').modal('hide');
  });

  $('#btnImportProducts').click(function (e) {
    e.preventDefault();

    let expenseAsignation = parseFloat(
      strReplaceNumber($('#importExpense').val())
    );

    multiproductsToImport[0].expense = expenseAsignation;

    $.ajax({
      type: 'POST',
      url: '/api/addMultiproduct',
      data: { importMultiproducts: multiproductsToImport },
      success: function (r) {
        if (r.success == true) {
          $('#fileMultiproducts').val('');
          $('#importExpense').val('');
          $('#multiproductsImportBody').empty();
          $('#modalImportMultiproducts').modal('hide');
          $('.cardImportMultiproducts').hide(800);
          loadTblMultiproducts();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);
      },
    });
  });

  /* Descargar formato XLSX */
  $('#btnDownloadImportsMultiproducts').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Multiproductos.xlsx';

    let link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
