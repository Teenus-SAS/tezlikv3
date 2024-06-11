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
        let arr = data.rowObject;

        if (arr.length == 0) {
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
        
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileCustom').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let customToImport = arr.map((item) => {
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
              typePrice: flag_type_price,
              percentage: item.porcentaje
            };
        });
        checkCustom(customToImport, type);
      })
      .catch((error) => {
        console.log('Ocurrio un error. Intente Nuevamente:', error);
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
        flag_type_price == '0' ? namePrice = '(actual)' : namePrice = '(sugerido)';

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `El importe se va a realizar con el precio de venta ${namePrice}.<br>
            Se han encontrado los siguientes registros:<br><br>
            Datos a insertar: ${resp.insert} <br>
            Datos a actualizar: ${resp.update}.`,
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
    flag_type_price == '0' ? namePrice = 'sale_price' : namePrice = 'price';

    $.ajax({
      type: 'POST',
      url: '/api/addCustomPrice',
      data: {
        importCustom: data,
        type: type,
        name: namePrice,
      },
      success: function (r) {
        message(r);

        products = [];
        $('#nameNotProducts').html('Productos No Agregados');
        $('#btnSaveProducts').hide();
        loadPriceList(1);

        if (r.dataNotData.length > 1)
          loadTblNotProducts(r.dataNotData, 1);
        else {
          $('#modalNotProducts').modal('hide');
        }
      },
    });
  };

  /* Descargar formato (Precios)*/
  $('#btnDownloadImportsCustomPrices').click(function (e) {
    e.preventDefault();

    // URLs de los archivos a descargar
    let url = 'assets/formatsXlsx/Precios_Personalizados.xlsx';

    // Función para descargar un archivo dado su URL
    let link = document.createElement('a');
    link.href = url;
    link.target = '_blank';
    link.download = '';
    
    link.addEventListener('click', () => {
      setTimeout(() => {
        document.body.removeChild(link);
        resolve();
      }, 100); // Espera breve antes de eliminar el enlace
    });

    document.body.appendChild(link);
    link.click();
  });

  /* Descargar formato (Porcentaje)*/
  $('#btnDownloadImportsCustomPercentage').click(function (e) {
    e.preventDefault();

    // URLs de los archivos a descargar 
    let url = 'assets/formatsXlsx/Porcentaje_Personalizados.xlsx';

    // Función para descargar un archivo dado su URL 
    let link = document.createElement('a');
    link.href = url;
    link.target = '_blank';
    link.download = '';
    
    link.addEventListener('click', () => {
      setTimeout(() => {
        document.body.removeChild(link);
        resolve();
      }, 100); // Espera breve antes de eliminar el enlace
    });

    document.body.appendChild(link);
    link.click();
  });
  // $('#btnDownloadImportsCustom').click(function (e) {
  //   e.preventDefault();

  //   // URLs de los archivos a descargar
  //   let url1 = 'assets/formatsXlsx/Precios_Personalizados.xlsx';
  //   let url2 = 'assets/formatsXlsx/Porcentaje_Personalizados.xlsx';

  //   // Función para descargar un archivo dado su URL
  //   function descargarArchivo(url) {
  //     return new Promise((resolve, reject) => {
  //       let link = document.createElement('a');
  //       link.href = url;
  //       link.target = '_blank';
  //       link.download = '';
    
  //       link.addEventListener('click', () => {
  //         setTimeout(() => {
  //           document.body.removeChild(link);
  //           resolve();
  //         }, 100); // Espera breve antes de eliminar el enlace
  //       });

  //       document.body.appendChild(link);
  //       link.click();
  //     });
  //   }

  //   // Descargar ambos archivos simultáneamente
  //   Promise.all([descargarArchivo(url1), descargarArchivo(url2)])
  //     .then(() => {
  //       // console.log('Descarga completada.');
  //     })
  //     .catch((error) => {
  //       console.error('Error al descargar los archivos:', error);
  //     });
  // });
});
