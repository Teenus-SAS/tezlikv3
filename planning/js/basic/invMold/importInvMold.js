$(document).ready(function () {
  let selectedFile;

  $('.cardImportInvMold').hide();

  $('#btnImportNewInvMold').click(function (e) {
    e.preventDefault();
    $('.cardCreateInvMold').hide(800);
    $('.cardImportInvMold').toggle(800);
  });

  $('#fileInvMold').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportInvMold').click(function (e) {
    e.preventDefault();

    file = $('#fileInvMold').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let MoldsToImport = data.map((item) => {
          return {
            referenceMold: item.referencia.trim(),
            mold: item.molde.trim(),
            assemblyTime: item.tiempo_montaje,
            assemblyProduction: item.tiempo_montaje_produccion,
            cavity: item.numero_cavidades,
            cavity_available: item.cavidades_disponibles,
          };
        });
        checkMolds(MoldsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkMolds = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/invMoldDataValidation',
      data: { importInvMold: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportInvMold').trigger('reset');
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
              saveMoldTable(data);
            } else $('#fileInvMold').val('');
          },
        });
      },
    });
  };

  saveMoldTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addMold',
      data: { importInvMold: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportInvMold').hide(800);
          $('#formImportInvMold').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblInvMold').DataTable().clear();
          $('#tblInvMold').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsInvMold').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Moldes.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
