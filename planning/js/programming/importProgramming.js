$(document).ready(function () {
  let selectedFile;

  $('.cardImportProgramming').hide();

  $('#btnImportNewProgramming').click(function (e) {
    e.preventDefault();
    $('.cardCreateProgramming').hide(800);
    $('.cardImportProgramming').toggle(800);
  });

  $('#fileProgramming').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProgramming').click(function (e) {
    e.preventDefault();

    file = $('#fileProgramming').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let ProgrammingToImport = data.map((item) => {
          return {
            Programming: item.proceso,
          };
        });
        checkProgramming(ProgrammingToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProgramming = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/planProgrammingDataValidation',
      data: { importProgramming: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportProgramming').trigger('reset');
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
              saveProgrammingTable(data);
            } else $('#fileProgramming').val('');
          },
        });
      },
    });
  };

  saveProgrammingTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addPlanProgramming',
      data: { importProgramming: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportProgramming').hide(800);
          $('#formImportProgramming')[0].reset();
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblProgramming').DataTable().clear();
          $('#tblProgramming').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProgramming').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Procesos.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
