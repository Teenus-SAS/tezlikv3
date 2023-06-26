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

    let decimals = contarDecimales(data.cost);
    let cost = formatNumber(data.cost, decimals);
    $('#costRawMaterial').val(cost);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data materia prima */
  checkDataMaterial = async (url, idMaterial) => {
    let ref = $('#refRawMaterial').val();
    let material = $('#nameRawMaterial').val();
    let unity = $('#unit').val();
    let cost = $('#costRawMaterial').val();

    if (
      ref == '' ||
      ref == 0 ||
      material == '' ||
      material == 0 ||
      unity == '' ||
      unity == 0 ||
      cost == ''
    ) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    cost = parseFloat(strReplaceNumber(cost));

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

    if (!data.status == 0) {
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
    if (data.success == true) {
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
});
