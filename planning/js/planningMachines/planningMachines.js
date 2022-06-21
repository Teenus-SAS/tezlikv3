$(document).ready(function () {
  //Abrir modal crear plan de maquinas
  $('#btnNewPlanMachine').click(function (e) {
    e.preventDefault();

    $('#createPlanMachine').modal('show');
    $('#btnCreatePlanMachine').html('Crear');

    sessionStorage.removeItem('id_planning_machine');

    $('#formCreatePlanMachine').trigger('reset');
  });

  //Ocultar modal Plan maquinas
  $('#btnClosePlanMachine').click(function (e) {
    e.preventDefault();

    $('#createPlanMachine').modal('hide');
  });

  //Crear Plan maquinas
  $('#btnCreatePlanMachine').click(function (e) {
    e.preventDefault();
    let id_planning_machine = sessionStorage.getItem('id_planning_machine');

    if (id_planning_machine == '' || id_planning_machine == null) {
      idMachine = parseInt($('#idMachine').val());
      numberWorkers = $('#numberWorkers').val();
      hoursDay = $('#hoursDay').val();
      hourStart = $('#hourStart').val();
      hourEnd = $('#hourEnd').val();
      year = $('#year').val();
      january = $('#january').val();
      february = $('#february').val();
      march = $('#march').val();
      april = $('#april').val();
      may = $('#may').val();
      june = $('#june').val();
      july = $('#july').val();
      august = $('#august').val();
      september = $('#september').val();
      october = $('#october').val();
      november = $('#november').val();
      december = $('#december').val();

      data =
        idMachine *
        numberWorkers *
        hoursDay *
        hourStart *
        hourEnd *
        year *
        january *
        february *
        march *
        april *
        may *
        june *
        july *
        august *
        september *
        october *
        november *
        december;

      if (!data || data == '' || data == null || data == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      planningMachines = $('#formCreatePlanMachine').serialize();

      $.post(
        '/api/addPlannningMachines',
        planningMachines,
        function (data, textStatus, jqXHR) {
          $('#createPlanMachine').modal('hide');
          message(data);
        }
      );
    } else {
      updatePlanningMachines();
    }
  });

  //Actualizar Plan maquina
  $(document).on('click', '.updatePMachines', function (e) {
    $('#createPlanMachine').modal('show');
    $('#btnCreatePlanMachine').html('Actualizar');

    id_planning_machine = this.id;
    id_planning_machine = sessionStorage.setItem(
      'id_planning_machine',
      id_planning_machine
    );

    let row = $(this).parent().parent()[0];
    let data = tblPMachines.fnGetData(row);

    $(`#idMachine option:contains(${data.machine})`).prop('selected', true);
    $('#numberWorkers').val(data.number_workers);

    $('#hoursDay').val(data.hours_day);
    $('#hourStart').val(data.hour_start);
    $('#hourEnd').val(data.hour_end);
    $('#year').val(data.year);

    $('#january').val(data.january);
    $('#february').val(data.february);
    $('#march').val(data.march);
    $('#april').val(data.april);
    $('#may').val(data.may);
    $('#june').val(data.june);
    $('#july').val(data.july);
    $('#august').val(data.august);
    $('#september').val(data.september);
    $('#october').val(data.october);
    $('#november').val(data.november);
    $('#december').val(data.december);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updatePlanningMachines = () => {
    let data = $('#formCreatePlanMachine').serialize();
    id_planning_machine = sessionStorage.getItem('id_planning_machine');
    data = `${data}&idProgramMachines=${id_planning_machine}`;

    $.post(
      '/api/updatePlanningMachines',
      data,
      function (data, textStatus, jqXHR) {
        $('#createPlanMachine').modal('hide');
        message(data);
      }
    );
  };

  // Eliminar Plan maquina
  $(document).on('click', '.deletePMachines', function (e) {
    let id_program_machines = this.id;

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
            `/api/deletePlanningMachines/${id_program_machines}`,
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
      $('#formCreatePlanMachine')[0].reset();
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblPMachines').DataTable().clear();
    $('#tblPMachines').DataTable().ajax.reload();
  }
});
