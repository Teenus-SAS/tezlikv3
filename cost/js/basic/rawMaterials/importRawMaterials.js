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

        const expectedHeaders = ['referencia', 'material', 'magnitud', 'unidad', 'costo', 'costo_importacion', 'costo_exportacion', 'tipo_moneda'];
        
        // price_usd == '0' ||
        if (flag_currency_usd == '0') { // COP
          if (export_import == '0'){
            expectedHeaders.splice(5, 1);
            expectedHeaders.splice(5, 1);
            expectedHeaders.splice(5, 1);
          }else
            expectedHeaders.splice(7, 1);
        }

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

          // price_usd == '0' || 
          if (flag_currency_usd == '0')
            typeCost = 'COP';
          else
            typeCost = item.tipo_moneda;

          return {
            refRawMaterial: item.referencia,
            nameRawMaterial: item.material,
            category: item.categoria,
            magnitude: item.magnitud,
            unit: item.unidad,
            costRawMaterial: costRawMaterial,
            costImport: item.costo_importacion,
            costExport: item.costo_exportacion,
            typeCost: typeCost,
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
        messageMaterials(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsMaterials').click(function (e) {
    e.preventDefault();

    // price_usd == '0' ||
    // if (flag_currency_usd == '0')
    //   url = 'assets/formatsXlsx/Materia_prima(COP).xlsx';
    // else
    //   url = 'assets/formatsXlsx/Materia_prima(USD).xlsx';

    // let link = document.createElement('a');

    // link.target = '_blank';

    // link.href = url;
    // document.body.appendChild(link);
    // link.click();

    // document.body.removeChild(link);
    // delete link;
    let url = 'assets/formatsXlsx/Materia_prima(COP).xlsx';

    if (flag_currency_usd == '1') {
      if (export_import == '1')
        url = 'assets/formatsXlsx/Materia_prima(Export_Usd).xlsx';
      else
        url = 'assets/formatsXlsx/Materia_prima(USD).xlsx';
    }
    else {
      if (export_import == '1')
        url = 'assets/formatsXlsx/Materia_prima(Export_Cop).xlsx';
    }


    let newFileName = 'Materia_Prima.xlsx';

    fetch(url)
      .then(response => response.blob())
      .then(blob => {
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = newFileName;

        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        URL.revokeObjectURL(link.href); // liberar memoria
      })
      .catch(console.error);
  });
});
