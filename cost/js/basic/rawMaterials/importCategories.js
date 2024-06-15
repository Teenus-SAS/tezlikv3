$(document).ready(function () {
  let selectedFile;

  $('.cardImportCategories').hide();

  $('#btnImportNewCategory').click(function (e) {
    e.preventDefault();
    $('.cardAddCategories').hide(800);
    $('.cardImportCategories').toggle(800);
  });

  $('#fileCategories').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportCategory').click(function (e) {
    e.preventDefault();

    let file = $('#fileCategories').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formCategory');

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
          $('#fileCategories').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['categoria'];
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileCategories').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let categoriesToImport = arr.map((item) => {
          return {
            category: item.categoria,
          };
        });
        checkCategories(categoriesToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkCategories = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/categoriesDataValidation',
      data: { importCategories: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#fileCategories').val('');
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
              saveCategoriesTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileCategories').val('');
            }
          },
        });
      },
    });
  };

  const saveCategoriesTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addCategory',
      data: { importCategories: data },
      success: function (r) {
        messageCategories(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsCategories').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Categorias.xlsx';

    let link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
