
//let selectedFile;

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
    .then(async (data) => {
      let arr = data.rowObject;

      if (arr.length == 0) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileMaterials').val('');
        toastr.error('Archivo vacio. Verifique nuevamente');
        return false;
      }

      const expectedHeaders = ['referencia', 'material', 'magnitud', 'unidad', 'costo', 'costo_importacion', 'costo_nacionalizacion', 'tipo_moneda'];

      // price_usd == '0' ||
      if (export_import == '0' || flag_export_import == '0') {
        expectedHeaders.splice(expectedHeaders.length - 2, 1);
        expectedHeaders.splice(expectedHeaders.length - 2, 1);
      }

      if (flag_currency_usd == '0') { // COP
        expectedHeaders.splice(expectedHeaders.length - 1, 1);
      }

      const actualHeaders = data.actualHeaders;

      const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

      if (missingHeaders.length > 0) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileMaterials').val('');

        toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
        return false;
      }

      let resp = await validateDataRM(arr);

      if (resp.importStatus == true)
        checkProduct(resp.materialsToImport, resp.insert, resp.update);
    })
    .catch(() => {
      console.log('Ocurrio un error. Intente Nuevamente');
    });
});

// Función para obtener y parsear datos de sessionStorage
const getSessionData = (key) => JSON.parse(sessionStorage.getItem(key));

// Función para validar campos
const validateFields = (fields, rowIndex) => {
  if (fields.some(field => {
    // Si el campo es un número, solo verifica si es null o undefined
    if (typeof field === 'number') {
      return field === null || field === undefined;
    }
    // Si el campo es una cadena, verifica si está vacío o contiene solo espacios
    return !field || !field.toString().trim();
  })) {
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileMaterials').val('');
    importStatus = false;
    toastr.error(`Columna vacía en la fila: ${rowIndex + 2}`);
    return false;
  }
  return true;
};

// Función para validar un valor de costo
const validateCost = (cost, rowIndex) => {
  let valCost = parseFloat(cost.replace(',', '.')) * 1;
  if (isNaN(valCost) || valCost <= 0) {
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileMaterials').val('');
    importStatus = false;
    toastr.error(`El costo debe ser mayor a cero (0). Fila: ${rowIndex + 2}`);
    return false;
  }
  return true;
};

/* Validar Data */
const validateDataRM = (data) => {
  let materialsToImport = [];
  let importStatus = true;
  let insert = 0;
  let update = 0;

  // Cargar datos desde sessionStorage una vez
  const dataMagnitudes = getSessionData('dataMagnitudes');
  const dataUnits = getSessionData('dataUnits');
  const dataCategory = getSessionData('dataCategory');
  const dataMaterials = getSessionData('dataMaterials');

  // Convertir dataMaterials en un Map para búsquedas rápidas
  const dataMaterialsMap = new Map(
    dataMaterials.map(item => {
      const ref = item.reference?.toString().trim().toUpperCase() || '';
      const mat = item.material?.toString().trim().toUpperCase() || '';
      return [`${ref}-${mat}`, item];
    })
  );

  for (let i = 0; i < data.length; i++) {
    let arr = data[i];
    let cost = arr.costo > 0 ? arr.costo.toString() : '';
    let cost_import = arr.costo_importacion > 0 ? arr.costo_importacion.toString() : '0';
    let cost_export = arr.costo_nacionalizacion > 0 ? arr.costo_nacionalizacion.toString() : '0';

    let fieldsToValidate = [arr.referencia, arr.material, arr.magnitud, arr.unidad, cost];

    if (flag_currency_usd === '0') {
      if (export_import === '1' && flag_export_import === '1') {
        fieldsToValidate.push(cost_import, cost_export);
      }
    } else
      fieldsToValidate.push(arr.tipo_moneda);

    if (!validateFields(fieldsToValidate, i) || !validateCost(cost, i)) {
      break;
    }

    let magnitude = dataMagnitudes.find(item => item.magnitude == arr.magnitud.toString().trim().toUpperCase());
    if (!magnitude) {
      $('.cardLoading').remove();
      $('.cardBottons').show(400);
      $('#fileMaterials').val('');
      importStatus = false;
      toastr.error(`Magnitud no existe en la base de datos. Fila: ${i + 2}`);
      break;
    }

    let unity = dataUnits.find(item => item.id_magnitude == magnitude.id_magnitude && item.unit == arr.unidad.toString().trim().toUpperCase());
    if (!unity) {
      $('.cardLoading').remove();
      $('.cardBottons').show(400);
      $('#fileMaterials').val('');
      importStatus = false;
      toastr.error(`Unidad no existe en la base de datos. Fila: ${i + 2}`);
      break;
    }

    materialsToImport.push({ idMagnitude: magnitude.id_magnitude, unit: unity.id_unit, idCategory: 0 });

    if (arr.categoria) {
      let category = dataCategory.find(item => item.category == arr.categoria.toString().trim().toUpperCase());
      if (category)
        materialsToImport[i].idCategory = category.id_category;
    }

    const refKey = arr.referencia?.toString().trim().toUpperCase() || '';
    const matKey = arr.material?.toString().trim().toUpperCase() || '';
    const key = `${refKey}-${matKey}`;

    const material = dataMaterialsMap.get(key);

    if (material) {
      update += 1;
      materialsToImport[i].idMaterial = material.id_material;
    } else
      insert += 1;

    // Transformar el elemento y añadirlo al nuevo array
    materialsToImport[i].refRawMaterial = arr.referencia;
    materialsToImport[i].nameRawMaterial = arr.material;
    materialsToImport[i].category = arr.categoria;
    materialsToImport[i].costRawMaterial = arr.costo;
    materialsToImport[i].costImport = arr.costo_importacion;
    materialsToImport[i].costExport = arr.costo_nacionalizacion;
    materialsToImport[i].typeCost = arr.tipo_moneda || 'COP';
  }

  return { importStatus, materialsToImport, insert, update };
};

/* Mensaje de advertencia */
const checkProduct = (data, insert, update) => {
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
      if (result == true) {
        saveMaterialTable(data);
      } else {
        $('#fileMaterials').val('');
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
      }
    },
  });
};

const saveMaterialTable = (data) => {
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

  let url = 'assets/formatsXlsx/Materia_prima(COP).xlsx';

  if (flag_currency_usd == '1') {
    if (export_import == '1' && flag_export_import == '1')
      url = 'assets/formatsXlsx/Materia_prima(Export_Usd).xlsx';
    else
      url = 'assets/formatsXlsx/Materia_prima(USD).xlsx';
  }
  else {
    if (export_import == '1' && flag_export_import == '1')
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

