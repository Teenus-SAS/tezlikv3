$(document).ready(function () {
  let selectedFile;

  $('.cardImportExternalServices').hide();

  $('#btnImportNewExternalServices').click(function (e) {
    e.preventDefault();
    $('.cardAddService').hide(800);
    $('.cardImportExternalServices').toggle(800);
    // $('.cardProducts').toggle(800);
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
      .then((data) => {
        let arr = data.rowObject;

         if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExternalServices').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['servicio', 'costo'];
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExternalServices').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let externalServiceToImport = arr.map((item) => {
          let costService = '';

          if (item.costo)
            costService = item.costo.toString().replace('.', ',');

          return {
            // referenceProduct: item.referencia_producto,
            // product: item.producto,
            service: item.servicio,
            costService: costService,
          };
        });
        checkExternalService(externalServiceToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkExternalService = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/generalExternalServiceDataValidation',
      data: { importExternalService: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileExternalServices').val('');

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
              saveExternalServiceTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileExternalServices').val('');
            }
          },
        });
      },
    });
  };

  saveExternalServiceTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addGExternalService',
      data: { importExternalService: data },
      success: function (r) {
        messageServices(r); 
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsExternalServices').click(function (e) {
    e.preventDefault();

    let dataServices = JSON.parse(sessionStorage.getItem('dataGServices'));

    if (dataServices.length > 0) {
      let wb = XLSX.utils.book_new();

      let data = [];

      namexlsx = 'Servicios_Externos_Generales.xlsx';
      for (i = 0; i < dataServices.length; i++) {
        data.push({
          servicio: dataServices[i].name_service,
          costo: dataServices[i].cost,
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Servicios Externos');
      XLSX.writeFile(wb, namexlsx);
    } else {
      let url = 'assets/formatsXlsx/Servicios_Externos_Generales.xlsx';

      let link = document.createElement('a');
      link.target = '_blank';

      link.href = url;
      document.body.appendChild(link);
      link.click();

      document.body.removeChild(link);
      delete link;
    }
  });
});
