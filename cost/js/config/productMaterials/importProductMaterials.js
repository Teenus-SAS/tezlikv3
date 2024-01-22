$(document).ready(function () {
  let selectedFile;

  $('.cardImportProductsMaterials').hide();

  $('#btnImportNewProductsMaterials').click(function (e) {
    e.preventDefault();
    $('.cardAddMaterials').hide(800);
    $('.cardAddNewProduct').hide(800);
    $('.cardImportProductsMaterials').toggle(800);
    $('.cardProducts').toggle(800);
  });

  $('#fileProductsMaterials').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProductsMaterials').click(function (e) {
    e.preventDefault();

    let file = $('#fileProductsMaterials').val();
    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formProductMaterials');

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
          $('#fileProductsMaterials').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia_producto', 'producto', 'referencia_material', 'material', 'magnitud', 'unidad', 'cantidad', 'tipo'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let productMaterialsToImport = data.map((item) => {
          let quantity = '';

          if (item.cantidad)
            quantity = item.cantidad.toString().replace('.', ',');

          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            magnitude: item.magnitud,
            unit: item.unidad,
            quantity: quantity,
            type: item.tipo,
          };
        });
        checkProductMaterial(productMaterialsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProductMaterial = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsMaterialsDataValidation',
      data: { importProductsMaterials: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');
          toastr.error(resp.message);
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
              saveProductMaterialTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileProductsMaterials').val('');
            }
          },
        });
      },
    });
  };

  saveProductMaterialTable = (data) => {
    // console.log(data);
    $.ajax({
      type: 'POST',
      url: '/api/addProductsMaterials',
      data: { importProductsMaterials: data },
      success: function (r) {
        $('.cardProducts').toggle(800);

        messageMaterials(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProductsMaterials').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Productos_Materias.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
