$(document).ready(function () {
  let selectedFile;

  $('.cardImportCustom').hide();

  $('#btnNewImportCustom').click(function (e) {
    e.preventDefault();
    $('.cardCreateCustomPrices').hide(800);
    $('.cardCreateCustomPercentages').hide(800);
    $('.cardImportCustom').toggle(800);
  });

  $('#fileCustom').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportCustom').click(function (e) {
    e.preventDefault();

    let file = $('#fileCustom').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formCustom');

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
          $('#fileCustom').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia', 'producto','lista_precio','valor'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileCustom').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let customToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia,
            product: item.producto,
            priceName: item.lista_precio,
            customPricesValue: item.valor
          };
        });
        checkCustom(customToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkCustom = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/customDataValidation',
      data: { importCustom: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#fileCustom').val('');
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
              saveCustomTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileCustom').val('');
            }
          },
        });
      },
    });
  };

  saveCustomTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/updateCustomPrice',
      data: { importCustom: data },
      success: function (r) {
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsCustom').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Precios_Personalizados.xlsx';

    let link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
