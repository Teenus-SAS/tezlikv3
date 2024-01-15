$(document).ready(function () {
  let selectedFile;

  $('.cardImportMaterials').hide();

  $('#btnImportNewMaterials').click(function (e) {
    e.preventDefault();
    $('.cardRawMaterials').hide(800);
    $('.cardImportMaterials').toggle(800);
  });

  $('#fileMaterials').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportMaterials').click(function (e) {
    e.preventDefault();

    let file = $('#fileMaterials').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formMaterials');

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
          $('#fileMaterials').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia', 'material', 'magnitud', 'unidad', 'costo'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMaterials').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let materialsToImport = data.map((item) => {
          let costRawMaterial = '';

          if (item.costo)
            costRawMaterial = item.costo.toString().replace('.', ',');

          return {
            refRawMaterial: item.referencia,
            nameRawMaterial: item.material,
            magnitude: item.magnitud,
            unit: item.unidad,
            costRawMaterial: costRawMaterial,
          };
        });

        checkProduct(materialsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProduct = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/materialsDataValidation',
      data: { importMaterials: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#fileMaterials').val('');
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
              saveMaterialTable(data);
            } else {
              $('#fileMaterials').val('');
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
            }
          },
        });
      },
    });
  };

  saveMaterialTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../api/addMaterials',
      data: { importMaterials: data },
      success: function (r) {
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsMaterials').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Materia_prima.xlsx';

    let link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
