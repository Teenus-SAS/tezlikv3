$(document).ready(function () {
  /* Ocultar panel Nueva carga fabril */

  $('.cardFactoryLoad').hide();
  $('#costMinute').prop('disabled', true);

  /* Abrir panel crear carga fabril */

  $('#btnNewFactoryLoad').click(function (e) {
    e.preventDefault();

    $('.cardImportFactoryLoad').hide(800);
    $('.cardFactoryLoad').toggle(800);
    $('#btnCreateFactoryLoad').html('Asignar');

    sessionStorage.removeItem('id_manufacturing_load');

    $('#idMachine option:contains(Seleccionar)').prop('selected', true);
    $('#descriptionFactoryLoad').val('');
    $('#costFactory').val('');
    $('#costMinute').val('');
  });

  /* Adicionar nueva carga fabril */

  $('#btnCreateFactoryLoad').click(function (e) {
    e.preventDefault();
    let idManufacturingLoad = sessionStorage.getItem('id_manufacturing_load');

    if (idManufacturingLoad == '' || idManufacturingLoad == null) {
      valueMinute = parseInt($('#costMinute').val());

      if (valueMinute == '' || valueMinute == 0) {
        toastr.error('El costo de la carga fabril debe ser mayor a cero');
        return false;
      }

      $('#costMinute').prop('disabled', false);
      factoryLoad = $('#formNewFactoryLoad').serialize();

      $.post(
        '../../api/addFactoryLoad',
        factoryLoad,
        function (data, textStatus, jqXHR) {
          $('#costMinute').prop('disabled', true);
          message(data);
        }
      );
    } else {
      updateFactoryLoad();
    }
  });

  /* Actualizar carga fabril */

  $(document).on('click', '.updateFactoryLoad', function (e) {
    $('.cardImportFactoryLoad').hide(800);
    $('.cardFactoryLoad').show(800);
    $('#btnCreateFactoryLoad').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblFactoryLoad.fnGetData(row);

    sessionStorage.setItem('id_manufacturing_load', data.id_manufacturing_load);

    $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);
    $('#descriptionFactoryLoad').val(data.input);
    $('#costFactory').val(data.cost.toLocaleString());

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateFactoryLoad = () => {
    $('#costMinute').prop('disabled', false);
    let data = $('#formNewFactoryLoad').serialize();
    idManufacturingLoad = sessionStorage.getItem('id_manufacturing_load');
    data = data + '&idManufacturingLoad=' + idManufacturingLoad;

    $.post(
      '../../api/updateFactoryLoad',
      data,
      function (data, textStatus, jqXHR) {
        $('#costMinute').prop('disabled', true);
        message(data);
      }
    );
  };

  /* Eliminar carga fabril */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblFactoryLoad.fnGetData(row);

    let id_manufacturing_load = data.id_manufacturing_load;

    idMachine = data.id_machine;

    dataFactoryLoad = {};
    dataFactoryLoad['idManufacturingLoad'] = id_manufacturing_load;
    dataFactoryLoad['idMachine'] = idMachine;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este fabril? Esta acción no se puede reversar.',
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
            '../../api/deleteFactoryLoad',
            dataFactoryLoad,
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
      $('.cardFactoryLoad').hide(800);
      $('#formNewFactoryLoad')[0].reset();
      updateTable();
      toastr.success(data.message);
      //return false
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblFactoryLoad').DataTable().clear();
    $('#tblFactoryLoad').DataTable().ajax.reload();
  }
});
