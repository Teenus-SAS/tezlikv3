$(document).ready(function () {
  /* Ocultar panel para crear materiales */

  $('.cardRawMaterials').hide();

  /* Abrir panel para crear materiales */

  $('#btnNewMaterial').click(function (e) {
    e.preventDefault();
    $('.cardImportMaterials').hide(800);
    $('.cardRawMaterials').toggle(800);
    $('#btnCreateMaterial').html('Crear');

    sessionStorage.removeItem('id_material');

    $('#refRawMaterial').val('');
    $('#nameRawMaterial').val('');
    $('#unityRawMaterial').val('');
    $('#costRawMaterial').val('');
  });

  /* Crear producto */

  $('#btnCreateMaterial').click(function (e) {
    e.preventDefault();
    let idMaterial = sessionStorage.getItem('id_material');

    if (idMaterial == '' || idMaterial == null) {
      ref = $('#refRawMaterial').val();
      material = $('#nameRawMaterial').val();
      unity = $('#unityRawMaterial').val();
      cost = $('#costRawMaterial').val();

      if (
        ref == '' ||
        ref == 0 ||
        material == '' ||
        material == 0 ||
        unity == '' ||
        unity == 0 ||
        cost == '' ||
        cost == 0
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      material = $('#formCreateMaterial').serialize();

      $.post(
        '../../api/addMaterials',
        material,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateMaterial();
    }
  });

  /* Actualizar productos */

  $(document).on('click', '.updateRawMaterials', function (e) {
    $('.cardImportMaterials').hide(800);
    $('.cardRawMaterials').show(800);
    $('#btnCreateMaterial').html('Actualizar');

    idMaterial = this.id;
    idMaterial = sessionStorage.setItem('id_material', idMaterial);

    let row = $(this).parent().parent()[0];
    let data = tblRawMaterials.fnGetData(row);

    $('#refRawMaterial').val(data.reference);
    $('#nameRawMaterial').val(data.material);
    $('#unityRawMaterial').val(data.unit);

    cost = data.cost;

    if (cost.isInteger) cost = cost.toLocaleString();
    else
      cost = cost.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    $('#costRawMaterial').val(cost);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateMaterial = () => {
    let data = $('#formCreateMaterial').serialize();
    idMaterial = sessionStorage.getItem('id_material');
    data = data + '&idMaterial=' + idMaterial;

    $.post(
      '../../api/updateMaterials',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar productos */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblRawMaterials.fnGetData(row);

    let idMaterial = data.id_material;
    dataMaterial = {};
    dataMaterial['idMaterial'] = idMaterial;

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
      $('#formCreateMaterial')[0].reset();
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
