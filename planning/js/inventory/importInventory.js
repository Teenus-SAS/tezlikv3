$(document).ready(function () {
  let selectedFile;

  $('.cardImportInventory').hide();

  $('#btnImportNewInventory').click(function (e) {
    e.preventDefault();
    $('.cardAddMonths').hide(800);
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
            reference: item.referencia.trim(),
            nameInventory: item.nombre.trim(),
            referenceMold: item.referencia_molde.trim(),
            mold: item.molde.trim(),
            unityRawMaterial: item.unidad.trim(),
            quantity: item.cantidad,
            category: item.categoria.trim(),
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
        if (!resp.reference) table = '';
        else
          table = `<br><br>
        <p>Los siguientes registros no existen en la base de datos:</p>
          <table class="table table-striped text-center">
            <thead>
              <tr>
                <th class="text-center">Referencia</th>
                <th class="text-center">Descripción</th>
              </tr>
            </thead>
            <tbody>
              ${(row = addRow(resp))}
            </tbody>
          </table>`;

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros a modificar: ${resp.update}${table}`,
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
              saveInventoryTable();
            } else deleteSession();
          },
        });
      },
    });
  };

  // Mostrar Inventarios no existentes
  addRow = (data) => {
    reference = data.reference;
    nameInventory = data.nameInventory;

    row = [];
    for (i = 0; i < reference.length; i++) {
      row.push(`<tr>
      <td>${reference[i]}</td>
      <td>${nameInventory[i]}</td>
                </tr>`);
    }
    return row.join('');
  };

  // Opcion SI
  saveInventoryTable = () => {
    $.ajax({
      type: 'POST',
      url: '../../api/addInventory',
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
          $('#category').change();
        }
      },
    });
  };

  // Opcion NO
  deleteSession = () => {
    $('#fileInventory').val('');
    $.get('/api/deleteInventorySession');
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
