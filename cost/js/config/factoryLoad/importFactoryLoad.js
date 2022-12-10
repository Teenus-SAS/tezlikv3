$(document).ready(function () {
  let selectedFile;

  $('.cardImportFactoryLoad').hide();

  $('#btnImportNewFactoryLoad').click(function (e) {
    e.preventDefault();
    $('.cardFactoryLoad').hide(800);
    $('.cardImportFactoryLoad').toggle(800);
  });

  $('#fileFactoryLoad').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportFactoryLoad').click(function (e) {
    e.preventDefault();

    let file = $('#fileFactoryLoad').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let factoryLoadToImport = data.map((item) => {
          return {
            machine: item.maquina.trim(),
            descriptionFactoryLoad: item.descripcion.trim(),
            costFactory: item.costo,
          };
        });
        checkFactoryLoad(factoryLoadToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkFactoryLoad = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/factoryLoadDataValidation',
      data: { importFactoryLoad: data },
      success: function (resp) {
        if (resp.error == true) {
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
              saveFactoryLoadTable(data);
            } else $('#fileFactoryLoad').val('');
          },
        });
      },
    });
  };

  saveFactoryLoadTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addFactoryLoad',
      data: { importFactoryLoad: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportFactoryLoad').hide(800);
          $('#formImportFactoryLoad').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblFactoryLoad').DataTable().clear();
          $('#tblFactoryLoad').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsFactoryLoad').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Carga_Fabril.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
