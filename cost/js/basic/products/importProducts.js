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
          expectedHeaders.splice(expectedHeaders.length - 1, 1);
        if (idUser != '1')
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
  const checkProduct = async (data) => {
    try {
      const params = new URLSearchParams();
      params.append('importProducts', JSON.stringify(data));

      PricingSpinner.show("Verificando Data");

      const response = await fetch('/api/productsDataValidation', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: params
      });

      const resp = await response.json();

      PricingSpinner.hide();

      if (resp.reload) {
        location.reload();
        return;
      }

      if (resp.error === true) {
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
          if (result === true) {
            saveProductTable(data);
          } else {
            $('#fileProducts').val('');
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
          }
        },
      });

    } catch (error) {
      console.error('Error en checkProduct:', error);
      $('.cardLoading').remove();
      $('.cardBottons').show(400);
      $('#fileProducts').val('');
      toastr.error('Error al validar los datos. Intente nuevamente.');
    }
  };

  /* Guardar Importacion */
  const saveProductTable = async (data) => {
    try {
      const params = new URLSearchParams();
      params.append('importProducts', JSON.stringify(data));

      PricingSpinner.show("Importando Datos");

      const response = await fetch('/api/addProducts', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: params
      });

      const result = await response.json();

      PricingSpinner.hide();

      message(result);

    } catch (error) {
      console.error('Error en saveProductTable:', error);
      PricingSpinner.hide();
      $('.cardLoading').remove();
      $('.cardBottons').show(400);
      toastr.error('Error al guardar los productos. Intente nuevamente.');
    }
  };

  /* Descargar formato */
  $('#btnDownloadImportsProducts').click(function (e) {
    e.preventDefault();

    let url = idUser == '1'
      ? 'assets/formatsXlsx/Productos(Admin).xlsx'
      : 'assets/formatsXlsx/Productos.xlsx';

    if (flag_composite_product == '1') {
      url = idUser == '1'
        ? 'assets/formatsXlsx/Productos(Compuesto-Admin).xlsx'
        : 'assets/formatsXlsx/Productos(Compuesto).xlsx';
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
        URL.revokeObjectURL(link.href);
      })
      .catch(console.error);
  });
});