$(document).ready(function () {
  let selectedFile;

  $('.cardImportExternalServices').hide();

  $('#btnImportNewExternalServices').click(function (e) {
    e.preventDefault();
    $('.cardAddService').hide(800);
    $('.cardImportExternalServices').toggle(800);
    $('.cardProducts').toggle(800);
  });

  $('#fileExternalServices').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportExternalServices').click(function (e) {
    e.preventDefault();

    let file = $('#fileExternalServices').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formExternalServices');

    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then(async (data) => {
        let arr = data.rowObject;

         if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExternalServices').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia_producto', 'producto', 'servicio', 'costo'];
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExternalServices').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }
 
        let resp = await validateDataSX(arr);
          checkExternalService(resp.externalServiceToImport, resp.debugg);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Validar data */ 
  const validateDataSX = async (data) => {
    let externalServiceToImport = [];
    let debugg = []; 

    const dataProducts = JSON.parse(sessionStorage.getItem('dataProducts')); 

    for (let i = 0; i < data.length; i++) {
      let arr = data[i];

      let costService = arr.costo > 0 ? arr.costo.toString() : '0';

      !arr.referencia_producto ? arr.referencia_producto = '' : arr.referencia_producto;
      !arr.producto ? arr.producto = '' : arr.producto;
      !arr.servicio ? arr.servicio = '' : arr.servicio;

      if (
        !arr.referencia_producto || !arr.producto || !arr.servicio || costService.trim() === '' ||
        !arr.referencia_producto.toString().trim() || !arr.producto.toString().trim() || !arr.servicio.toString().trim()
      ) {
        debugg.push({ message: `Columna vacía en la fila: ${i + 2}` });
      }

      let valSX = parseFloat(costService.replace(',', '.')) * 1;
      if (isNaN(valSX) || valSX <= 0) {
        debugg.push({ message: `Costo de servicio debe ser mayor a cero (0). Fila: ${i + 2}` });
      }

      let product = dataProducts.find(item =>
        item.reference == arr.referencia_producto.toString().trim() &&
        item.product == arr.producto.toString().toUpperCase().trim()
      );

      if (!product) {
        debugg.push({ message: `Producto no existe en la base de datos. Fila: ${i + 2}` });
      }

      externalServiceToImport.push({
        idProduct: !product ? '' : product.id_product,
        referenceProduct: arr.referencia_producto,
        product: arr.producto,
        service: arr.servicio,
        costService: costService
      });
    }

    return { externalServiceToImport, debugg };
  };

  /* Mensaje de advertencia */
  const checkExternalService = (data, debugg) => {
    $.ajax({
      type: 'POST',
      url: '/api/externalServiceDataValidation',
      data: {
        importExternalService: data,
        debugg: debugg
      },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExternalServices').val('');

          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExternalServices').val('');

          // Generar el HTML para cada mensaje
          let concatenatedMessages = resp.debugg.map(item =>
            `<li>
              <span class="badge badge-danger" style="font-size: 16px;">${item.message}</span>
            </li>
            <br>`
          ).join('');

          // Mostramos el mensaje con Bootbox
          bootbox.alert({
            title: 'Errores',
            message: `
            <div class="container">
              <div class="col-12">
                <ul>
                  ${concatenatedMessages}
                </ul>
              </div> 
            </div>`,
            size: 'large',
            backdrop: true
          });
          return false;
        }

        if (typeof arr === 'object' && !Array.isArray(arr) && arr !== null && debugg.length == 0) {
          bootbox.confirm({
            title: '¿Desea continuar con la importación?',
            message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${arr.insert} <br>Datos a actualizar: ${arr.update}`,
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
                saveExternalServiceTable(data);
              } else {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileExternalServices').val('');
              }
            },
          });
        }
      },
    });
  };

  const saveExternalServiceTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addExternalService',
      data: { importExternalService: data },
      success: function (r) { 
        messageServices(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsExternalServices').click(function (e) {
    e.preventDefault(); 

    let url = 'assets/formatsXlsx/Servicios_Externos.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link; 
  });
});
