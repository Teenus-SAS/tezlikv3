$(document).ready(function () {
  //Abrir modal crear plan de maquinas
  $('#btnNewPlanMachine').click(function (e) {
    e.preventDefault();

    $('#createPlanMachine').modal('show');
    $('#btnCreatePlanMachine').html('Crear');

    sessionStorage.removeItem('id_planning_machine');

    $('.month').css('border-color', '');
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
      numberWorkers = parseInt($('#numberWorkers').val());
      hoursDay = parseInt($('#hoursDay').val());
      hourStart = $('#hourStart').val();
      hourEnd = $('#hourEnd').val();
      // january = $('#january').val();
      // february = $('#february').val();
      // march = $('#march').val();
      // april = $('#april').val();
      // may = $('#may').val();
      // june = $('#june').val();
      // july = $('#july').val();
      // august = $('#august').val();
      // september = $('#september').val();
      // october = $('#october').val();
      // november = $('#november').val();
      // december = $('#december').val();
      data = idMachine * numberWorkers * hoursDay;

      if (
        !data ||
        data == null ||
        data == 0 /*||
        january == '' ||
        january == null ||
        february == '' ||
        february == null ||
        march == '' ||
        march == null ||
        april == '' ||
        april == null ||
        may == '' ||
        may == null ||
        june == '' ||
        june == null ||
        july == '' ||
        july == null ||
        august == '' ||
        august == null ||
        september == '' ||
        september == null ||
        october == '' ||
        october == null ||
        november == '' ||
        november == null ||
        december == '' ||
        december == null*/
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      if (hourStart.includes('PM') && hourEnd.includes('AM')) {
        if (hourEnd > hourStart) {
          toastr.error('La hora final no puede ser mayor a la hora inicio');
          $('#hourEnd').css('border-color', 'red');
          return false;
        }
      }

      planningMachines = $('#formCreatePlanMachine').serialize();

      $.post(
        '/api/addPlanningMachines',
        planningMachines,
        function (data, textStatus, jqXHR) {
          $('.month').css('border-color', '');
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
    let data = tblPlanMachines.fnGetData(row);

    $(`#idMachine option:contains(${data.machine})`).prop('selected', true);
    $('#numberWorkers').val(data.number_workers);

    $('#hoursDay').val(data.hours_day);

    hourStart = moment(data.hour_start, ['HH:mm']).format('h:mm A');
    hourEnd = moment(data.hour_end, ['HH:mm']).format('h:mm A');
    $('#hourStart').val(hourStart);
    $('#hourEnd').val(hourEnd);

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
    data = `${data}&idProgramMachine=${id_planning_machine}`;

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
    let id_program_machine = this.id;

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
            `/api/deletePlanningMachines/${id_program_machine}`,
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
    $('#tblPlanMachines').DataTable().clear();
    $('#tblPlanMachines').DataTable().ajax.reload();
  }
});
