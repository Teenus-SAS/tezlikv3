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

    $('.cardBottons').hide();

    let form = document.getElementById('formMachines');

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
        let arr = data.rowObject;
        if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMachines').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }
        
        const expectedHeaders = ['maquina', 'costo', 'años_depreciacion', 'horas_maquina', 'dias_maquina'];

        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMachines').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let machinesToImport = arr.map((item) => {
          let cost = '';
          let residualValue = '';

          if(item.costo)
            cost = item.costo.toString().replace('.', ',');
          if(item.residualValue)
            residualValue = item.valor_residual.toString().replace('.', ',');

          return {
            machine: item.maquina,
            cost: cost,
            depreciationYears: item.años_depreciacion,
            residualValue: residualValue,
            hoursMachine: item.horas_maquina,
            daysMachine: item.dias_maquina,
          };
        });
        checkMachine(machinesToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkMachine = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/machinesDataValidation',
      data: { importMachines: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMachines').val('');
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
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileMachines').val('');
            }
          },
        });
      },
    });
  };

  const saveMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addMachines',
      data: { importMachines: data },
      success: function (r) {
        message(r);
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
