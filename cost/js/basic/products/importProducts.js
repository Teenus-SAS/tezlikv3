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
        let arr = data.rowObject;

        if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProducts').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        let expectedHeaders = ['id', 'referencia', 'producto', 'precio_venta', 'rentabilidad', 'comision_ventas', 'sub_producto'];

        if (flag_composite_product == '0')
          // expectedHeaders = ['referencia', 'producto', 'precio_venta', 'rentabilidad', 'comision_ventas', ];
          expectedHeaders.splice(expectedHeaders.length - 1, 1);
        if(idUser !='1')
          expectedHeaders.splice(0, 1);
        
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProducts').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }
        
        let productsToImport = arr.map((item) => {
          let salePrice = '';

          if (item.precio_venta)
            salePrice = item.precio_venta.toString().replace('.', ',');

          let dataImport = { 
            referenceProduct: item.referencia,
            product: item.producto,
            salePrice: salePrice,
            profitability: item.rentabilidad,
            commissionSale: item.comision_ventas,
            composite: item.sub_producto,
            active: item.activo
          };

          if (idUser == '1') {
            let id = '';

            if (item.id)
              id = item.id;

            dataImport = {
              id: id,
              referenceProduct: item.referencia,
              product: item.producto,
              salePrice: salePrice,
              profitability: item.rentabilidad,
              commissionSale: item.comision_ventas,
              composite: item.sub_producto,
              active: item.activo,
            };
          }
          
          return dataImport;
        });

        checkProduct(productsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkProduct = (data) => {
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
  const saveProductTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProducts',
      data: { importProducts: data },
      success: function (r) {
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProducts').click(function (e) {
    e.preventDefault();

    let url = idUser == '1' ? 'assets/formatsXlsx/Productos(Admin).xlsx':'assets/formatsXlsx/Productos.xlsx';

    if(flag_composite_product == '1'){
      url = idUser == '1' ? 'assets/formatsXlsx/Productos(Compuesto-Admin).xlsx': 'assets/formatsXlsx/Productos(Compuesto).xlsx';
    }

    let newFileName = 'Productos.xlsx';

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
