
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
    .then(async (data) => {
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

      let resp = await validateDataCategories(arr);

      if (resp.importStatus == true)
        checkCategories(resp.importCategories, resp.insert, resp.update);
    })
    .catch(() => {
      console.log('Ocurrio un error. Intente Nuevamente');
    });
});

// Validar data
const validateDataCategories = async (data) => {
  let importCategories = [];
  let importStatus = true;
  let insert = 0;
  let update = 0;

  // Obtener dataCategory una vez fuera del bucle
  const dataCategory = JSON.parse(sessionStorage.getItem('dataCategory'));

  const isValid = data.every((arr, i) => {
    if (!arr.categoria || !arr.categoria.toString().trim()) {
      $('.cardLoading').remove();
      $('.cardBottons').show(400);
      $('#fileCategories').val('');
      toastr.error(`Campos vacios. Fila: ${i + 2}`);
      importStatus = false;
      return false;
    }
    return true;
  });

  if (!isValid) return false;

  importCategories = data.map(arr => {
    let category = dataCategory.find(item => item.category == arr.categoria.toString().toUpperCase().trim());
    if (!category) insert++;
    else update++;

    return {
      idCategory: category ? category.id_category : null,
      category: arr.categoria
    };
  });

  return { importCategories, importStatus, insert, update };
};

/* Mensaje de advertencia */
const checkCategories = (data, insert, update) => {
  bootbox.confirm({
    title: '¿Desea continuar con la importación?',
    message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${insert} <br>Datos a actualizar: ${update}`,
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
      if (result) {
        saveCategoriesTable(data);
      } else {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileCategories').val('');
      }
    },
  });
};

// Guardar data
const saveCategoriesTable = (data) => {
  $.ajax({
    type: 'POST',
    url: '/api/categories/addCategory',
    data: { importCategories: data },
    success: function (r) {
      messageCategories(r, 3);
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

