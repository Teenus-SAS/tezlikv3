$(document).ready(function () {
  let selectedFile;

  $('.cardImportExternalServices').hide();

  $('#btnImportNewExternalServices').click(function (e) {
    e.preventDefault();
    $('.cardAddService').hide(800);
    $('.cardImportExternalServices').toggle(800);
  });

  $('#fileExternalServices').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportExternalServices').click(function (e) {
    e.preventDefault();

    file = $('#fileExternalServices').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let externalServiceToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            service: item.servicio,
            costService: item.costo,
          };
        });
        checkExternalService(externalServiceToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkExternalService = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/externalServiceDataValidation',
      data: { importExternalService: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          $('#fileExternalServices').val('');
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
              saveExternalServiceTable(data);
            } else $('#fileExternalServices').val('');
          },
        });
      },
    });
  };

  saveExternalServiceTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addExternalService',
      data: { importExternalService: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportExternalServices').hide(800);
          $('#formImportExternalServices')[0].reset();
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblExternalServices').DataTable().clear();
          $('#tblExternalServices').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsExternalServices').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Servicios_Externos.xlsx';

    link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
