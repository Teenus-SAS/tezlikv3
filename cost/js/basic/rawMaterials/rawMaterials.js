$(document).ready(function () {
  let dataMaterial = {};

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

  /* Crear materia prima */

  $('#btnCreateMaterial').click(function (e) {
    e.preventDefault();
    let idMaterial = sessionStorage.getItem('id_material');

    if (idMaterial == '' || idMaterial == null) {
      checkDataMaterial('/api/addMaterials', idMaterial);
    } else {
      checkDataMaterial('/api/updateMaterials', idMaterial);
    }
  });

  /* Actualizar materia prima */

  $(document).on('click', '.updateRawMaterials', async function (e) {
    $('.cardImportMaterials').hide(800);
    $('#units').empty();
    $('.cardRawMaterials').show(800);
    $('#btnCreateMaterial').html('Actualizar');

    let idMaterial = this.id;
    sessionStorage.setItem('id_material', idMaterial);

    let row = $(this).parent().parent()[0];
    let data = tblRawMaterials.fnGetData(row);
    $('#refRawMaterial').val(data.reference);
    $('#nameRawMaterial').val(data.material);
    $(`#magnitudes option[value=${data.id_magnitude}]`).prop('selected', true);
    await loadUnitsByMagnitude(data.id_magnitude, 1);
    $(`#units option[value=${data.id_unit}]`).prop('selected', true);

    // let decimals = contarDecimales(data.cost);
    // let cost = formatNumber(data.cost, decimals);
    $('#costRawMaterial').val(data.cost);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data materia prima */
  checkDataMaterial = async (url, idMaterial) => {
    let ref = $('#refRawMaterial').val().trim();
    let material = $('#nameRawMaterial').val().trim();
    let unity = $('#units').val();
    let cost = parseFloat($('#costRawMaterial').val());

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

    // cost = parseFloat(strReplaceNumber(cost));

    cost = 1 * cost;

    if (cost <= 0 || isNaN(cost)) {
      toastr.error('El costo debe ser mayor a cero (0)');
      return false;
    }

    let dataMaterial = new FormData(formCreateMaterial);

    if (idMaterial != '' || idMaterial != null)
      dataMaterial.append('idMaterial', idMaterial);

    let resp = await sendDataPOST(url, dataMaterial);

    message(resp);
  };

  /* Eliminar materia prima */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
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
          $.post(
            '../../api/deleteMaterial',
            dataMaterial,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    $('#fileMaterials').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    
    if (data.success == true) {
      $('.cardImportMaterials').hide(800);
      $('#formImportMaterials').trigger('reset');
      $('.cardRawMaterials').hide(800);
      $('#formCreateMaterial').trigger('reset');
      toastr.success(data.message);
      updateTable();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblRawMaterials').DataTable().clear();
    $('#tblRawMaterials').DataTable().ajax.reload();
  }

  /* Ocultar modal productos inactivos */
  $('.btnCloseProducts').click(function (e) {
    e.preventDefault();
    $('#productsByMaterial').modal('hide');
    $('#tblProductsBody').empty();
  });

  /* Productos relacionados */
  $(document).on('click', '.seeDetail', async function () {
    let data = await searchData(`/api/productsByMaterials/${this.id}`);

    $('#tblProductsBody').empty();

    let tblProductsBody = document.getElementById('tblProductsBody');

    for (let i = 0; i < data.length; i++) {
      tblProductsBody.insertAdjacentHTML(
        'beforeend',
        `
        <tr>
            <td>${i + 1}</td>
            <td>${data[i].reference}</td>
            <td>${data[i].product}</td> 
        </tr>
      `
      );
    }

    $('#productsByMaterial').modal('show');
  });

  $(document).on('click', '.billRawMaterial', function () {
    let row = $(this).parent().parent()[0];
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
                    <label for="">Fecha</label>
                  </div>
                  ${div3}
                  <div class="col-sm-12 floating-label enable-floating-label show-label">
                    <textarea class="form-control" id="observation" rows="3" value="${data.observation}">${data.observation}</textarea>
                    <label for="">Observaciones</label>
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
            url: '/api/saveBillMaterial',
            data: dataMaterial,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
              message(response);
            }
          });
        }
      },
    });
  });
});
