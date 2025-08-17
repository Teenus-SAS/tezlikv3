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
      url: '../../api/multiproductsDataValidation',
      data: { importMultiproducts: data },
      success: function (resp) {
        if (data.reload) {
          location.reload();
        }

        if (resp.error == true) {
          toastr.error(resp.message);
          $('#fileMultiproducts').val('');
          return false;
        }
        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros:<br>Datos a actualizar: ${resp}`,
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
              saveImportMultiproducts();
            } else $('#fileMultiproducts').val('');
          },
        });

        // try {
        //   // if (resp[0].expense > 0) {
        //   //   $('#importExpense').val(resp[0].expense);
        //   //   $('#importExpense').prop('disabled', true);
        //   // } else {
        //   //   $('#importExpense').val();
        //   //   $('#importExpense').prop('disabled', false);
        //   // }

        //   // let tblMultiproductsImportBody = document.getElementById(
        //   //   'multiproductsImportBody'
        //   // );

        //   // for (let i = 0; i < resp.length; i++) {
        //   //   tblMultiproductsImportBody.insertAdjacentHTML(
        //   //     'beforeend',
        //   //     `<tr>
        //   //       <td>${i + 1}</td>
        //   //       <td>${resp[i].referenceProduct}</td>
        //   //       <td>${resp[i].product}</td>
        //   //       <td>${resp[i].soldUnit}</td>
        //   //      </tr>`
        //   //   );
        //   // }

        //   // $('#tblImportMultiproducts').dataTable({
        //   //   destroy: true,
        //   //   pageLength: 5,
        //   // });

        //   $('#modalImportMultiproducts').modal('show');
        // } catch (error) {
        //   console.log(error);
        // }
      },
    });
  };

  saveImportMultiproducts = async () => {
    multiproductsToImport[0].expense = expenseAsignation;
    $.ajax({
      type: 'POST',
      url: '/api/multiproducts/addMultiproduct',
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
  };

  // $('#btnCloseImportProducts').click(function (e) {
  //   e.preventDefault();
  //   $('#fileMultiproducts').val('');
  //   $('#importExpense').val('');
  //   $('#multiproductsImportBody').empty();
  //   $('#modalImportMultiproducts').modal('hide');
  // });

  // $('#btnImportProducts').click(function (e) {
  //   e.preventDefault();

  //   let expenseAsignation = parseFloat(
  //     strReplaceNumber($('#importExpense').val())
  //   );

  //   multiproductsToImport[0].expense = expenseAsignation;

  //   $.ajax({
  //     type: 'POST',
  //     url: '/api/addMultiproduct',
  //     data: { importMultiproducts: multiproductsToImport },
  //     success: function (r) {
  //       if (r.success == true) {
  //         $('#fileMultiproducts').val('');
  //         $('#importExpense').val('');
  //         $('#multiproductsImportBody').empty();
  //         $('#modalImportMultiproducts').modal('hide');
  //         $('.cardImportMultiproducts').hide(800);
  //         loadTblMultiproducts();
  //         toastr.success(r.message);
  //         return false;
  //       } else if (r.error == true) toastr.error(r.message);
  //       else if (r.info == true) toastr.info(r.message);
  //     },
  //   });
  // });

  /* Descargar formato XLSX */
  $('#btnDownloadMultiproducts').click(function (e) {
    e.preventDefault();
    let data = [];

    for (let i = 0; i < multiproducts.length; i++) {
      data.push({
        referencia: multiproducts[i].reference,
        producto: multiproducts[i].product,
        unidades_vendidas: multiproducts[i].soldUnit,
      });
    }

    let wb = XLSX.utils.book_new();
    let ws = XLSX.utils.json_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, 'Multiproductos');
    XLSX.writeFile(wb, 'Pto_Equilibrio.xlsx');
  });
});
