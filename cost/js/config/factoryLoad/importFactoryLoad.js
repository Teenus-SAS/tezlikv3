
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

  $('.cardBottons').hide();

  let form = document.getElementById('formFactoryLoad');

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
        $('#fileFactoryLoad').val('');
        toastr.error('Archivo vacio. Verifique nuevamente');
        return false;
      }

      const expectedHeaders = ['maquina', 'descripcion', 'costo'];
      const actualHeaders = data.actualHeaders;

      const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

      if (missingHeaders.length > 0) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileFactoryLoad').val('');

        toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
        return false;
      }

      let factoryLoadToImport = arr.map((item) => {
        let costFactory = '';

        if (item.costo)
          costFactory = item.costo.toString().replace('.', ',');

        return {
          machine: item.maquina,
          descriptionFactoryLoad: item.descripcion,
          costFactory: costFactory,
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
    url: '/api/factoryLoad/factoryLoadDataValidation',
    data: { importFactoryLoad: data },
    success: function (resp) {
      if (resp.reload) {
        location.reload();
      }

      if (resp.error == true) {
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
            saveFactoryLoadTable(data);
          } else {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileFactoryLoad').val('');
          }
        },
      });
    },
  });
};

saveFactoryLoadTable = (data) => {
  $.ajax({
    type: 'POST',
    url: '/api/factoryLoad/addFactoryLoad',
    data: { importFactoryLoad: data },
    success: function (r) {
      message(r);
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

