$(document).ready(function () {
  let selectedFile;
  sessionStorage.removeItem('customImportType');

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

    bootbox.dialog({
      title: 'Importe',
      message: `Seleccione tipo de importe que desea realizar.`,
      backdrop: 'static', // Evita que el modal se cierre haciendo clic fuera de él
      closeButton: false, // Oculta el botón de cierre del modal
      size: 'small',
      buttons: {
        precio: {
          label: 'Precio',
          className: 'btn-success',
          callback: function () {
            sessionStorage.setItem('customImportType', 1);
            checkImportCustom();            
          }
        },
        porcentaje: {
          label: 'Porcentaje',
          className: 'btn-danger',
          callback: function () {
            sessionStorage.setItem('customImportType', 2);
            checkImportCustom();
          }
        }
      }
    }); 
  });

  checkImportCustom = () => {
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

        let type = sessionStorage.getItem('customImportType');

        type == '1' ?
          expectedHeaders = ['referencia', 'producto', 'lista_precio', 'valor'] :
          expectedHeaders = ['referencia', 'producto', 'lista_precio', 'porcentaje'];
        
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
          if (type == '1')
            return {
              referenceProduct: item.referencia,
              product: item.producto,
              priceName: item.lista_precio,
              customPricesValue: item.valor
            };
          else
            return {
              referenceProduct: item.referencia,
              product: item.producto,
              priceName: item.lista_precio,
              percentage: item.porcentaje
            };
        });
        checkCustom(customToImport, type);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  };
  /* Mensaje de advertencia */
  checkCustom = (data, type) => {
    $.ajax({
      type: 'POST',
      url: '/api/customDataValidation',
      data: {
        importCustom: data,
        type: type,
      },
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
              saveCustomTable(data, type);
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

  saveCustomTable = (data, type) => {
    $.ajax({
      type: 'POST',
      url: '/api/addCustomPrice',
      data: {
        importCustom: data,
        type: type
      },
      success: function (r) {
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsCustom').click(function (e) {
    e.preventDefault();

    // URLs de los archivos a descargar
    let url1 = 'assets/formatsXlsx/Precios_Personalizados.xlsx';
    let url2 = 'assets/formatsXlsx/Porcentaje_Personalizados.xlsx';

    // Crear el primer enlace
    let link1 = document.createElement('a');
    link1.target = '_blank';
    link1.href = url1;

    // Crear el segundo enlace
    let link2 = document.createElement('a');
    link2.target = '_blank';
    link2.href = url2;

    // Agregar los enlaces al cuerpo del documento
    document.body.appendChild(link1);
    document.body.appendChild(link2);

    // Simular clics en ambos enlaces
    link1.click();
    link2.click();

    // Eliminar los enlaces después de la descarga
    document.body.removeChild(link1);
    document.body.removeChild(link2);
  });
});
