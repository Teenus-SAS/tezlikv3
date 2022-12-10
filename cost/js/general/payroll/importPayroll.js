$(document).ready(function () {
  let selectedFile;

  $('.cardImportPayroll').hide();

  $('#btnImportNewPayroll').click(function (e) {
    e.preventDefault();
    $('#createPayroll').modal('hide');
    $('.cardImportPayroll').toggle(800);
  });

  $('#filePayroll').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportPayroll').click(function (e) {
    e.preventDefault();

    let file = $('#filePayroll').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let payrollToImport = data.map((item) => {
          return {
            employee: item.nombres_y_apellidos.trim(),
            process: item.proceso.trim(),
            basicSalary: item.salario_basico,
            transport: item.transporte,
            endowment: item.dotaciones,
            extraTime: item.horas_extras,
            bonification: item.otros_ingresos,
            workingHoursDay: item.horas_trabajo_x_dia,
            workingDaysMonth: item.dias_trabajo_x_mes,
            typeFactor: item.tipo_nomina,
            factor: item.factor,
          };
        });
        checkPayroll(payrollToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkPayroll = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/payrollDataValidation',
      data: { importPayroll: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          $('#filePayroll').val('');
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
              savePayroll(data);
            } else $('#filePayroll').val('');
          },
        });
      },
    });
  };

  savePayroll = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addPayroll',
      data: { importPayroll: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportPayroll').hide(800);
          $('#formImportPayroll').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblPayroll').DataTable().clear();
          $('#tblPayroll').DataTable().ajax.reload();
        }
      },
    });
  };

  $('#btnDownloadImportsPayroll').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Carga_Nomina.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
