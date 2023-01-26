$(document).ready(function () {
  let selectedFile;

  $('.cardImportProductsMaterials').hide();

  $('#btnImportNewProductsMaterials').click(function (e) {
    e.preventDefault();
    $('.cardAddMaterials').hide(800);
    $('.cardImportProductsMaterials').toggle(800);
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

    importFile(selectedFile)
      .then((data) => {
        let productMaterialsToImport = data.map((item) => {
          item.referencia_producto == undefined || !item.referencia_producto
            ? (referenceProduct = '')
            : (referenceProduct = item.referencia_producto.trim());
          item.producto == undefined || !item.producto
            ? (product = '')
            : (product = item.producto.trim());
          item.referencia_material == undefined || !item.referencia_material
            ? (refRawMaterial = '')
            : (refRawMaterial = item.referencia_material.trim());
          item.material == undefined || !item.material
            ? (nameRawMaterial = '')
            : (nameRawMaterial = item.material.trim());
          item.cantidad == undefined || !item.cantidad
            ? (quantity = '')
            : (quantity = item.cantidad);
          item.costo == undefined || !item.costo
            ? (cost = '')
            : (cost = item.costo);

          return {
            referenceProduct: referenceProduct,
            product: product,
            refRawMaterial: refRawMaterial,
            nameRawMaterial: nameRawMaterial,
            quantity: quantity,
            cost: cost,
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
            } else $('#fileProductsMaterials').val('');
          },
        });
      },
    });
  };

  saveProductMaterialTable = (data) => {
    console.log(data);
    $.ajax({
      type: 'POST',
      url: '/api/addProductsMaterials',
      data: { importProductsMaterials: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportProductsMaterials').hide(800);
          $('#formImportProductMaterial').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) {
          $('#fileProductsMaterials').val('');
          toastr.error(r.message);
        } else if (r.info == true) {
          $('#fileProductsMaterials').val('');
          toastr.info(r.message);
        }

        /* Actualizar tabla */
        function updateTable() {
          $('#tblConfigMaterials').DataTable().clear();
          $('#tblConfigMaterials').DataTable().ajax.reload();
        }
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
