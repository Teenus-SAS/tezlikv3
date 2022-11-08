$(document).ready(function () {
  sessionStorage.removeItem('businessDays');

  let date = new Date();
  let arr = [];

  //Abrir modal crear plan de maquinas
  $('#btnNewPlanMachine').click(function (e) {
    e.preventDefault();

    $('#createPlanMachine').modal('show');
    $('#btnCreatePlanMachine').html('Crear');

    sessionStorage.removeItem('id_planning_machine');

    $('.month').css('border-color', '');
    $('#formCreatePlanMachine').trigger('reset');

    // Mostrar dias habiles x mes
    for (i = 1; i <= 12; i++) {
      month = new Date(date.getFullYear(), i, 0);
      lastDay = month.getDate();

      businessDays = getBusinessDays(lastDay, i - 1);
      arr[i] = businessDays;

      $(`#month-${i}`).val(businessDays);
    }
    businessDays = JSON.stringify(arr);
    sessionStorage.setItem('businessDays', businessDays);
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
      data = idMachine * numberWorkers * hoursDay;

      if (!data || data == null || data == 0) {
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

    $('#month-1').val(data.january);
    $('#month-2').val(data.february);
    $('#month-3').val(data.march);
    $('#month-4').val(data.april);
    $('#month-5').val(data.may);
    $('#month-6').val(data.june);
    $('#month-7').val(data.july);
    $('#month-8').val(data.august);
    $('#month-9').val(data.september);
    $('#month-10').val(data.october);
    $('#month-11').val(data.november);
    $('#month-12').val(data.december);

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

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblPlanMachines.fnGetData(row);

    let id_program_machine = data.id_program_machine;

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
  };

  /* Mensaje de exito */

  const message = (data) => {
    if (data.success == true) {
      $('#formCreatePlanMachine').trigger('reset');
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
