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

    sessionStorage.removeItem('id_material');

    $('#formCreateMaterial').trigger('reset');
  });

  /* Crear producto */

  $('#btnCreateMaterial').click(function (e) {
    e.preventDefault();
    let idMaterial = sessionStorage.getItem('id_material');

    if (idMaterial == '' || idMaterial == null) {
      let ref = $('#refRawMaterial').val();
      let material = $('#nameRawMaterial').val();
      let unity = $('#unityRawMaterial').val();
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

      cost = decimalNumber(cost);

      cost = 1 * parseFloat(cost);

      if (cost <= 0) {
        toastr.error('El costo debe ser mayor a cero (0)');
        return false;
      }

      let data = $('#formCreateMaterial').serialize();

      $.post(
        '../../api/addMaterials',
        data,
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

    let idMaterial = this.id;
    sessionStorage.setItem('id_material', idMaterial);

    let row = $(this).parent().parent()[0];
    let data = tblRawMaterials.fnGetData(row);

    $('#refRawMaterial').val(data.reference);
    $('#nameRawMaterial').val(data.material);
    $('#unityRawMaterial').val(data.unit);

    let cost = data.cost;

    if (cost.isInteger) cost = cost.toLocaleString('es-CO');
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
    let idMaterial = sessionStorage.getItem('id_material');
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
