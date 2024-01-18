$(document).ready(function () {
  let selectedFile;

  $('.cardImportProductsProcess').hide();

  $('#btnImportNewProductProcess').click(function (e) {
    e.preventDefault();
    $('.cardAddProcess').hide(800);
    $('.cardImportProductsProcess').toggle(800);
  });

  $('#fileProductsProcess').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProductsProcess').click(function (e) {
    e.preventDefault();

    let file = $('#fileProductsProcess').val();
    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formProductProcess');

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
          $('#fileProductsProcess').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia_producto', 'producto', 'proceso', 'maquina', 'tiempo_enlistamiento', 'tiempo_operacion'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsProcess').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let productProcessToImport = data.map((item) => {
          // let enlistmentTime = '';
          // let operationTime = '';

          // if (item.tiempo_enlistamiento)
          //   enlistmentTime = item.tiempo_enlistamiento.toString().replace('.', ',');
          // if (item.tiempo_operacion)
          //   operationTime = item.tiempo_operacion.toString().replace('.', ',');

          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            process: item.proceso,
            machine: item.maquina,
            enlistmentTime: item.tiempo_enlistamiento,
            operationTime: item.tiempo_operacion,
          };
        });
        checkProductProcess(productProcessToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProductProcess = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsProcessDataValidation',
      data: { importProductsProcess: data },
      success: function (resp) {

        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);

          $('#fileProductsProcess').val('');
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
              saveProductProcessTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileProductsProcess').val('');
            }
          },
        });
      },
    });
  };

  saveProductProcessTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProductsProcess',
      data: { importProductsProcess: data },
      success: function (r) {
        messageProcess(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProductsProcess').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Productos_Procesos.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
