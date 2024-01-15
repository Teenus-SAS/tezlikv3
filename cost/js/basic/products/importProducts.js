$(document).ready(function () {
  let selectedFile;

  $('.cardImportProducts').hide();

  $('#btnImportNewProducts').click(function (e) {
    e.preventDefault();
    $('.cardCreateProduct').hide(800);
    $('.cardImportProducts').toggle(800);
  });

  $('#fileProducts').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProducts').click(function (e) {
    e.preventDefault();

    let file = $('#fileProducts').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formProducts');

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
          $('#fileProducts').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia', 'producto', 'rentabilidad', 'comision_ventas'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProducts').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }
        
        let productsToImport = data.map((item) => {
          let salePrice = '';

          if (item.precio_venta)
            salePrice = item.precio_venta.toString().replace('.', ',');

          return {
            referenceProduct: item.referencia,
            product: item.producto,
            salePrice: salePrice,
            profitability: item.rentabilidad,
            commissionSale: item.comision_ventas,
          };
        });

        checkProduct(productsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProduct = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsDataValidation',
      data: { importProducts: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProducts').val('');
          $('#formImportProduct').trigger('reset');

          toastr.error(resp.message);
          return false;
        }
        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se encontraron los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
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
              saveProductTable(data);
            } else {
              $('#fileProducts').val('');
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
            }
          },
        });
      },
    });
  };

  /* Guardar Importacion */
  saveProductTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProducts',
      //data: data,
      data: { importProducts: data },
      success: function (r) {
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProducts').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Productos.xlsx';

    link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
