$(document).ready(function () {
  /* Ocultar panel para crear Machinees */

  $('.cardCreateMachines').hide();
  $('#depreciationMinute').prop('disabled', true);

  /* Abrir panel para crear Machinees */

  $('#btnNewMachine').click(function (e) {
    e.preventDefault();
    $('.cardCreateMachines').toggle(800);
    $('#btnCreateMachine').html('Crear');

    sessionStorage.removeItem('id_machine');

    $('#formCreateMachine').trigger('reset');
  });

  /* Crear producto */

  $('#btnCreateMachine').click(function (e) {
    e.preventDefault();
    let idMachine = sessionStorage.getItem('id_machine');
    if (idMachine == '' || idMachine == null) {
      Machine = $('#machine').val();
      hoursMachine = $('#hoursMachine').val();
      daysMachine = $('#daysMachine').val();

      data = hoursMachine * daysMachine;

      if (Machine == '' || Machine == null || data == null || data <= 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      machine = $('#formCreateMachine').serialize();

      $.post('/api/addMachines', machine, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateMachine();
    }
  });

  /* Actualizar productos */

  $(document).on('click', '.updateMachines', function (e) {
    $('.cardCreateMachines').show(800);
    $('#btnCreateMachine').html('Actualizar');
    idMachine = this.id;
    idMachine = sessionStorage.setItem('id_machine', idMachine);

    let row = $(this).parent().parent()[0];
    let data = tblMachines.fnGetData(row);

    $('#machine').val(data.machine);
    $('#costMachine').val(data.cost.toLocaleString());
    $('#residualValue').val(data.residual_value.toLocaleString());
    $('#depreciationYears').val(data.years_depreciation);
    $('#hoursMachine').val(data.hours_machine);
    $('#daysMachine').val(data.days_machine);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateMachine = () => {
    let data = $('#formCreateMachine').serialize();
    idMachine = sessionStorage.getItem('id_machine');

    data = data + '&idMachine=' + idMachine;
    $.post('/api/updateMachines', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar productos */

  $(document).on('click', '.deleteMachines', function (e) {
    let id_machine = this.id;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta maquina? Esta acción no se puede reversar.',
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
            `../../api/deleteMachine/${id_machine}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateMachines').hide(800);
      $('#formCreateMachine')[0].reset();
      toastr.success(data.message);
      updateTable();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblMachines').DataTable().clear();
    $('#tblMachines').DataTable().ajax.reload();
  }
});
