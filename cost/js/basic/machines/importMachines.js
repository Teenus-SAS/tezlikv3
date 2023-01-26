$(document).ready(function () {
  let selectedFile;

  $('.cardImportMachines').hide();

  $('#btnImportNewMachines').click(function (e) {
    e.preventDefault();
    $('.cardCreateMachines').hide(800);
    $('.cardImportMachines').toggle(800);
  });

  $('#fileMachines').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportMachines').click(function (e) {
    e.preventDefault();

    let file = $('#fileMachines').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let machinesToImport = data.map((item) => {
          item.maquina == undefined || !item.maquina
            ? (machine = '')
            : (machine = item.maquina.trim());
          item.costo == undefined || !item.costo
            ? (cost = '')
            : (cost = item.costo);
          item.años_depreciacion == undefined || !item.años_depreciacion
            ? (depreciationYears = '')
            : (depreciationYears = item.años_depreciacion);
          item.valor_residual == undefined || !item.valor_residual
            ? (residualValue = '')
            : (residualValue = item.valor_residual);
          item.horas_maquina == undefined || !item.horas_maquina
            ? (hoursMachine = '')
            : (hoursMachine = item.horas_maquina);
          item.dias_maquina == undefined || !item.dias_maquina
            ? (daysMachine = '')
            : (daysMachine = item.dias_maquina);

          return {
            machine: machine,
            cost: cost,
            depreciationYears: depreciationYears,
            residualValue: residualValue,
            hoursMachine: hoursMachine,
            daysMachine: daysMachine,
          };
        });
        checkMachine(machinesToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkMachine = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/machinesDataValidation',
      data: { importMachines: data },
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
              saveMachineTable(data);
            } else $('#fileMachines').val('');
          },
        });
      },
    });
  };

  saveMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addMachines',
      data: { importMachines: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportMachines').hide(800);
          $('#formImportMachines').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblMachines').DataTable().clear();
          $('#tblMachines').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsMachines').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Maquinas.xlsx';

    let link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
