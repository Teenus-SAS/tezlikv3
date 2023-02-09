$(document).ready(function () {
  let dataFactoryLoad = {};

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

    $('#formNewFactoryLoad').trigger('reset');
  });

  /* Adicionar nueva carga fabril */

  $('#btnCreateFactoryLoad').click(function (e) {
    e.preventDefault();
    let idManufacturingLoad = sessionStorage.getItem('id_manufacturing_load');

    if (idManufacturingLoad == '' || idManufacturingLoad == null) {
      checkDataFactoryLoad('/api/addFactoryLoad', idManufacturingLoad);
    } else {
      checkDataFactoryLoad('/api/updateFactoryLoad', idManufacturingLoad);
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

    $(`#machine option[value=${data.id_machine}]`).prop('selected', true);
    $('#descriptionFactoryLoad').val(data.input);
    $('#costFactory').val(data.cost.toLocaleString('es-CO'));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revisar data carga fabril */
  checkDataFactoryLoad = async (url, idManufacturingLoad) => {
    let machine = $('#machine').val();
    let descriptionFactoryLoad = $('#descriptionFactoryLoad').val();
    let costFactory = $('#costFactory').val();

    costFactory = parseFloat(strReplaceNumber(costFactory));

    costFactory = 1 * costFactory;

    if (
      machine == '' ||
      descriptionFactoryLoad == '' ||
      isNaN(costFactory) ||
      costFactory <= 0
    ) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    $('#costMinute').prop('disabled', false);
    let dataFactoryLoad = new FormData(formNewFactoryLoad);

    if (idManufacturingLoad != '' || idManufacturingLoad != null)
      dataFactoryLoad.append('idManufacturingLoad', idManufacturingLoad);

    let resp = await sendDataPOST(url, dataFactoryLoad);

    message(resp);
  };

  /* Eliminar carga fabril */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblFactoryLoad.fnGetData(row);

    let id_manufacturing_load = data.id_manufacturing_load;

    let idMachine = data.id_machine;

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
      $('#formNewFactoryLoad').trigger('reset');
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
