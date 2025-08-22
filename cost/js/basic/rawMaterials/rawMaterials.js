
let dataMaterial = {};

$('.selectNavigation').click(function (e) {
  e.preventDefault();

  let option = this.id;

  switch (option) {
    case 'navMaterials': // Materiales
      $('.cardMaterials').show();
      $('.cardCategories').hide();
      $('.cardRawMaterials').hide();
      $('.cardImportMaterials').hide();
      $('.cardAddCategories').hide();
      $('.cardImportCategories').hide();
      break;
    case 'navCategories': // Categorias
      $('.cardCategories').show();
      $('.cardMaterials').hide();
      $('.cardAddCategories').hide();
      $('.cardImportCategories').hide();
      $('.cardRawMaterials').hide();
      $('.cardImportMaterials').hide();
      break;
  }

  let tables = document.getElementsByClassName(
    'dataTable'
  );

  for (let i = 0; i < tables.length; i++) {
    let attr = tables[i];
    attr.style.width = '100%';
    attr = tables[i].firstElementChild;
    attr.style.width = '100%';
  }
});

// Cambiar moneda
$('#selectPriceUSD').change(function (e) {
  e.preventDefault();

  $('.cardRawMaterials').hide(800);
  $('.cardImportMaterials').hide(800);

  const selectPriceUSD = this.value;
  let titleText;
  let op;

  switch (selectPriceUSD) {
    case '1': // Precios COP
      titleText = 'Ingrese el valor de compra en COP';
      op = 1;
      break;
    case '2': // Precios USD
      titleText = 'Ingrese el valor de compra en USD';
      op = 2;
      break;
  }

  $('#costRawMaterial').attr('data-original-title', titleText);

  if (export_import == '1' && flag_export_import == '1') {
    $('#costImport, #costExport').attr('data-original-title', titleText);
  }

  $('.cardAlertPrice').html(titleText);

  let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));
  let dataCategory = JSON.parse(sessionStorage.getItem('dataCategory'));
  let visible = true;

  if (dataCategory.length == 0) visible = false;

  loadTblRawMaterials(dataMaterials, visible, op);
});

/* Ocultar panel para crear materiales */
$('.cardRawMaterials').hide();

/* Abrir panel para crear materiales */

$('#btnNewMaterial').click(function (e) {
  e.preventDefault();
  $('.cardImportMaterials').hide(800);
  $('.cardRawMaterials').toggle(800);
  $('#btnCreateMaterial').html('Crear');
  $('#units').empty();

  sessionStorage.removeItem('id_material');

  $('#formCreateMaterial').trigger('reset');
});

if (export_import == '1' && flag_export_import == '1') {
  $(document).on('keyup', '.calcCost', function () {
    let costRawMaterial = parseFloat($('#costRawMaterial').val());
    let costImport = parseFloat($('#costImport').val());
    let costExport = parseFloat($('#costExport').val());

    isNaN(costRawMaterial) ? costRawMaterial = 0 : costRawMaterial;
    isNaN(costImport) ? costImport = 0 : costImport;
    isNaN(costExport) ? costExport = 0 : costExport;

    let costTotal = costRawMaterial + costImport + costExport;

    $('#costTotal').val(costTotal);
  });
}

/* Crear materia prima */

$('#btnCreateMaterial').click(function (e) {
  e.preventDefault();
  let idMaterial = sessionStorage.getItem('id_material');

  if (idMaterial == '' || idMaterial == null) {
    checkDataMaterial('/api/materials/addMaterials', idMaterial);
  } else {
    checkDataMaterial('/api/materials/updateMaterials', idMaterial);
  }
});

/* Actualizar materia prima */

$(document).on('click', '.updateRawMaterials', function (e) {
  $('.cardImportMaterials').hide(800);
  $('#units').empty();
  $('.cardRawMaterials').show(800);
  $('#btnCreateMaterial').html('Actualizar');
  $('#formCreateMaterial').trigger('reset');

  let idMaterial = this.id;
  sessionStorage.setItem('id_material', idMaterial);

  let row = $(this).closest('tr')[0];
  let data = tblRawMaterials.fnGetData(row);
  $('#refRawMaterial').val(data.reference);
  $('#nameRawMaterial').val(data.material);
  $(`#idCategory option[value=${data.id_category}]`).prop('selected', true);
  $(`#magnitudes option[value=${data.id_magnitude}]`).prop('selected', true);
  loadUnitsByMagnitude(data.id_magnitude, 1);
  $(`#units option[value=${data.id_unit}]`).prop('selected', true);

  $('#costRawMaterial').val(data.cost);

  const selectPriceUSD = $('#selectPriceUSD').val();

  if (flag_currency_usd === '1' && selectPriceUSD === '2') {
    $('#costRawMaterial').val(data.cost_usd);
  }

  if (export_import === '1' && flag_export_import === '1') {
    switch (selectPriceUSD) {
      case '1': // COP
        $('#costRawMaterial').val(data.cost);
        $('#costImport').val(data.cost_import);
        $('#costExport').val(data.cost_export);
        $('#costTotal').val(data.cost_total);
        break;
      case '2': // USD
        $('#costRawMaterial').val(data.cost_usd);
        $('#costImport').val(data.cost_import_usd);
        $('#costExport').val(data.cost_export_usd);
        $('#costImport').keyup();
        break;
    }
  }

  $('html, body').animate(
    {
      scrollTop: 0,
    },
    1000
  );
});

/* Revision data materia prima */
const checkDataMaterial = async (url, idMaterial) => {
  let ref = $('#refRawMaterial').val().trim();
  let material = $('#nameRawMaterial').val().trim();
  let unity = $('#units').val();
  let category = $('#idCategory').val();
  let cost = parseFloat($('#costRawMaterial').val());
  let costImport = parseFloat($('#costImport').val());
  let costExport = parseFloat($('#costExport').val());
  let costTotal = parseFloat($('#costTotal').val());

  if (
    ref == '' ||
    !ref ||
    material == '' ||
    !material ||
    !unity ||
    unity == 0 ||
    cost == ''
  ) {
    toastr.error('Ingrese todos los campos');
    return false;
  }

  cost = 1 * cost;

  if (cost <= 0 || isNaN(cost)) {
    toastr.error('El costo debe ser mayor a cero (0)');
    return false;
  }

  if (export_import == '1' && flag_export_import == '1') {
    if (!costTotal || costTotal == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }
  }

  let dataMaterial = new FormData(formCreateMaterial);

  let usd = 0;

  if (flag_currency_usd == '1') {
    let priceUSD = $('#selectPriceUSD').val();
    priceUSD == '2' ? usd = 1 : usd = 0;
  } else
    usd = 0;

  dataMaterial.append('usd', usd);
  dataMaterial.append('idCategory', category);

  if (idMaterial != '' || idMaterial != null)
    dataMaterial.append('idMaterial', idMaterial);

  let resp = await sendDataPOST(url, dataMaterial);

  messageMaterials(resp);
};

/* Eliminar materia prima */

$(document).on('click', '.deleteMaterials', function () {
  let row = $(this).closest('tr')[0];
  let data = tblRawMaterials.fnGetData(row);

  let idMaterial = data.id_material;
  dataMaterial['idMaterial'] = idMaterial;
  let status = parseInt(data.status);

  if (!status == 0) {
    toastr.error(
      'Esta materia prima no se puede eliminar, esta configurada a un producto'
    );
    return false;
  }

  bootbox.confirm({
    title: 'Eliminar',
    message:
      'Está seguro de eliminar esta materia prima? Esta acción no se puede reversar.',
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
        $.post('/api/materials/deleteMaterial', dataMaterial, function (data, textStatus, jqXHR) {
          messageMaterials(data);
        }
        );
      }
    },
  });
});

/* Mensaje de exito */
messageMaterials = (data) => {
  if (data.reload) {
    location.reload();
  }

  $('#fileMaterials').val('');
  $('.cardLoading').remove();
  $('.cardBottons').show(400);

  if (data.success == true) {
    $('.cardImportMaterials').hide(800);
    $('#formImportMaterials').trigger('reset');
    $('.cardRawMaterials').hide(800);
    $('#formCreateMaterial').trigger('reset');
    toastr.success(data.message);
    loadAllData(2);
    return false;
  } else if (data.error == true) toastr.error(data.message);
  else if (data.info == true) toastr.info(data.message);
};

/* Ocultar modal productos inactivos */
$('.btnCloseProducts').click(function (e) {
  e.preventDefault();
  $('#productsByMaterial').modal('hide');
  $('#tblProductsBody').empty();
});

/* Productos relacionados */
$(document).on('click', '.seeDetailMaterials', function () {
  let allProductMaterials = JSON.parse(sessionStorage.getItem('dataProductMaterials'));
  let data = allProductMaterials.filter(item => parseInt(item.id_material) == parseInt(this.id));

  $('#tblProductsBody').empty();

  let tblProductsBody = document.getElementById('tblProductsBody');

  for (let i = 0; i < data.length; i++) {
    tblProductsBody.insertAdjacentHTML(
      'beforeend',
      `
        <tr>
            <td>${i + 1}</td>
            <td>${data[i].reference_product}</td>
            <td>${data[i].product}</td> 
            <td> 
              <span class="badge ${data[i].active == 1 ? 'badge-success' : 'badge-danger'}">
                ${data[i].active == 1 ? 'Activo' : 'Inactivo'}
              </span>
            </td> 
        </tr>
      `
    );
  }

  $('#productsByMaterial').modal('show');
});

$(document).on('click', '.indirect', function () {
  let row = $(this).closest('tr')[0];
  let data = tblRawMaterials.fnGetData(row);

  bootbox.confirm({
    title: data.flag_indirect == '0' ? 'Agregar' : 'Eliminar',
    message:
      `Está seguro de que esta materia ${data.flag_indirect == '0' ? 'se agregue a material indirecto' : 'se elimine de material indirecto'}? Esta acción no se puede reversar.`,
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
        $.get(
          `/api/materials/changeIndirect/${data.id_material}/${data.flag_indirect == '0' ? '1' : '0'}`,
          function (data, textStatus, jqXHR) {
            messageMaterials(data);
          }
        );
      }
    },
  });
});

$(document).on('click', '.billRawMaterial', function () {
  let row = $(this).closest('tr')[0];
  let data = tblRawMaterials.fnGetData(row);

  if (!data.date_material)
    div3 = `<div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:20px">
                <input class="form-control" type="file" id="formFile">
                <label for="formFile" class="form-label"> Cargar imagen</label>
              </div>`;
  else
    div3 = `<div class="col-sm-6 text-center" style="margin-bottom:10px">
                <img data-enlargable class="img" src="${data.img}" alt="" style="width:100px;">
              </div>`;

  bootbox.confirm({
    size: 'large',
    title: 'Factura',
    message: `<div class="form-row">
                  <div class="col-sm-6 floating-label enable-floating-label show-label">
                    <input type="date" class="form-control" name="dateMaterial" id="dateMaterial" value="${data.date_material}">
                    <label>Fecha</label>
                  </div>
                  ${div3}
                  <div class="col-sm-12 floating-label enable-floating-label show-label">
                    <textarea class="form-control" id="observation" rows="3" value="${data.observation}">${data.observation}</textarea>
                    <label>Observaciones</label>
                  </div>
                </div>`,
    buttons: {
      confirm: {
        label: 'Guardar',
        className: 'btn-success',
      },
      cancel: {
        label: 'Cancelar',
        className: 'btn-danger',
      },
    },
    callback: function (result) {
      if (result == true) {
        let date = $('#dateMaterial').val();
        let image = $('#formFile')[0].files[0];
        let observation = $('#observation').val();;

        if (!date) {
          toastr.error('Ingrese los campos');
          return false;
        }

        let dataMaterial = new FormData();
        dataMaterial.append('img', image);
        dataMaterial.append('idMaterial', data.id_material);
        dataMaterial.append('date', date);
        dataMaterial.append('observation', observation);

        $.ajax({
          type: "POST",
          url: '/api/materials/saveBillMaterial',
          data: dataMaterial,
          contentType: false,
          cache: false,
          processData: false,
          success: function (response) {
            messageMaterials(response);
          }
        });
      }
    },
  });
});

