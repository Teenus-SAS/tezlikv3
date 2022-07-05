$(document).ready(function () {
  let selectedFile;

  $('.cardImportInventory').hide();

  $('#btnImportNewInventory').click(function (e) {
    e.preventDefault();
    $('.cardImportInventory').toggle(800);
  });

  $('#fileInventory').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportInventory').click(function (e) {
    e.preventDefault();

    file = $('#fileInventory').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let InventoryToImport = data.map((item) => {
          return {
            reference: item.referencia,
            nameInventory: item.nombre,
            referenceMold: item.referencia_molde,
            mold: item.molde,
            unityRawMaterial: item.unidad,
            quantity: item.cantidad,
            category: item.categoria,
          };
        });
        checkInventory(InventoryToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkInventory = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/inventoryDataValidation',
      data: { importInventory: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportInventory').trigger('reset');
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
              saveInventoryTable(data);
            } else $('#fileInventory').val('');
          },
        });
      },
    });
  };

  saveInventoryTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addInventory',
      data: { importInventory: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportInventory').hide(800);
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          // $('.table').DataTable().clear();
          // $('.table').DataTable().ajax.reload();
          $('#category').change();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsInventory').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Inventario.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
