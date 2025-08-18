
let selectedFile;

$('.cardImportProcess').hide();

$('#btnImportNewProcess').click(function (e) {
  e.preventDefault();
  $('.cardCreateProcess').hide(800);
  $('.cardImportProcess').toggle(800);
});

$('#fileProcess').change(function (e) {
  e.preventDefault();
  selectedFile = e.target.files[0];
});

$('#btnImportProcess').click(function (e) {
  e.preventDefault();

  let file = $('#fileProcess').val();

  if (!file) {
    toastr.error('Seleccione un archivo');
    return false;
  }

  $('.cardBottons').hide();

  let form = document.getElementById('formProcess');

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
        $('#fileProcess').val('');
        toastr.error('Archivo vacio. Verifique nuevamente');
        return false;
      }

      const expectedHeaders = ['proceso'];
      const actualHeaders = data.actualHeaders;

      const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

      if (missingHeaders.length > 0) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProcess').val('');

        toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
        return false;
      }

      let ProcessToImport = arr.map((item) => {
        return {
          process: item.proceso,
        };
      });
      checkProcess(ProcessToImport);
    })
    .catch(() => {
      console.log('Ocurrio un error. Intente Nuevamente');
    });
});

/* Mensaje de advertencia */
const checkProcess = (data) => {
  $.ajax({
    type: 'POST',
    url: '/api/process/processDataValidation',
    data: { importProcess: data },
    success: function (resp) {
      if (resp.reload) {
        location.reload();
      }

      if (resp.error == true) {
        $('#fileProcess').val('');
        $('.cardLoading').remove();
        $('.cardBottons').show(400);

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
            saveProcessTable(data);
          } else {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProcess').val('');
          }
        },
      });
    },
  });
};

const saveProcessTable = (data) => {
  $.ajax({
    type: 'POST',
    url: '/api/process/addProcess',
    data: { importProcess: data },
    success: function (r) {
      message(r);
    },
  });
};

/* Descargar formato */
$('#btnDownloadImportsProcess').click(function (e) {
  e.preventDefault();

  let url = 'assets/formatsXlsx/Procesos.xlsx';

  let link = document.createElement('a');

  link.target = '_blank';

  link.href = url;
  document.body.appendChild(link);
  link.click();

  document.body.removeChild(link);
  delete link;
});

