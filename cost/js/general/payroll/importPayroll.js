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

    $('.cardBottons').hide();

    let form = document.getElementById('formPayroll');

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
          $('#filePayroll').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['nombres_y_apellidos', 'proceso', 'salario_basico', 'transporte', 'dotaciones', 'horas_extras', 'otros_ingresos', 'prestacional', 'horas_trabajo_x_dia', 'dias_trabajo_x_mes', 'tipo_riesgo', 'tipo_nomina', 'factor'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePayroll').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let payrollToImport = data.map((item) => {
          let basicSalary = '';
          let transport = '';
          let endowment = '';
          let extraTime = '';
          let bonification = '';

          if (item.salario_basico)
            basicSalary = item.salario_basico.toString().replace('.', ',');
          if (item.transporte)
            transport = item.transporte.toString().replace('.', ',');
          if (item.dotaciones)
            endowment = item.dotaciones.toString().replace('.', ',');
          if (item.horas_extras)
            extraTime = item.horas_extras.toString().replace('.', ',');
          if (item.otros_ingresos)
            bonification = item.otros_ingresos.toString().replace('.', ',');


          return {
            employee: item.nombres_y_apellidos,
            process: item.proceso,
            basicSalary: item.salario_basico,
            transport: item.transporte,
            endowment: item.dotaciones,
            extraTime: item.horas_extras,
            bonification: item.otros_ingresos,
            benefit: item.prestacional,
            workingHoursDay: item.horas_trabajo_x_dia,
            workingDaysMonth: item.dias_trabajo_x_mes,
            riskLevel: item.tipo_riesgo,
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
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
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
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#filePayroll').val('');
            }
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
        message(r);
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
