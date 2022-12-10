$(document).ready(function () {
  let dataMachine = [];
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
      let Machine = $('#machine').val();
      let hoursMachine = $('#hoursMachine').val();
      let daysMachine = $('#daysMachine').val();

      let data = hoursMachine * daysMachine;

      if (Machine == '' || Machine == null || data == null || data <= 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      let machine = $('#formCreateMachine').serialize();

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
    let idMachine = this.id;
    sessionStorage.setItem('id_machine', idMachine);

    let row = $(this).parent().parent()[0];
    let data = tblMachines.fnGetData(row);

    $('#machine').val(data.machine);
    $('#costMachine').val(data.cost.toLocaleString());
    $('#residualValue').val(data.residual_value.toLocaleString());
    $('#depreciationYears').val(data.years_depreciation);

    let hours_machine = data.hours_machine;

    if (hours_machine.isInteger) hours_machine = hours_machine.toLocaleString();
    else
      hours_machine = hours_machine.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    $('#hoursMachine').val(hours_machine);
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
    let idMachine = sessionStorage.getItem('id_machine');

    data = data + '&idMachine=' + idMachine;
    $.post('/api/updateMachines', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar productos */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblMachines.fnGetData(row);

    dataMachine['idMachine'] = data.id_machine;

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
          $.post(
            '../../api/deleteMachine',
            dataMachine,
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
      $('.cardCreateMachines').hide(800);
      $('#formCreateMachine').trigger('reset');
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
