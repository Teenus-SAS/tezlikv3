$(document).ready(function () {
  /* Ocultar panel Nueva carga fabril */

  $('.cardCreatePayroll').hide();
  $('#factor').prop('disabled', true);

  $('#btnCloseCardPayroll').click(function (e) {
    e.preventDefault();
    $('#createPayroll').modal('hide');
  });

  /* Abrir panel crear carga nomina */

  $('#btnNewPayroll').click(function (e) {
    e.preventDefault();

    $('.cardImportPayroll').hide(800);
    $('#createPayroll').modal('show');
    $('#btnCreatePayroll').html('Crear');

    sessionStorage.removeItem('id_payroll');

    $('#formCreatePayroll').trigger('reset');
  });

  /* Mostrar factor prestacional */

  $(document).on('change', '#typeFactor', function (e) {
    $('#factor').prop('disabled', true);

    if (this.value == 0) 'Seleccione una opción';
    else if (this.value == 1) value = 38.35;
    else if (this.value == 2) value = 0;
    else if (this.value == 3) {
      $('#factor').prop('disabled', false);
      value = $('#factor').val();
    }

    $('#factor').val(value);
  });

  /* Agregar nueva carga nomina */

  $('#btnCreatePayroll').click(function (e) {
    e.preventDefault();
    let idPayroll = sessionStorage.getItem('id_payroll');

    if (idPayroll == '' || idPayroll == null) {
      employee = $('#employee').val();
      process = parseInt($('#idProcess').val());

      salary = $('#basicSalary').val();
      transport = $('#transport').val();
      endowment = $('#endowment').val();
      extraT = $('#extraTime').val();
      bonification = $('#bonification').val();

      workingHD = parseInt($('#workingHoursDay').val());
      workingDM = parseInt($('#workingDaysMonth').val());
      //factor = parseInt($('#typeFactor').val());

      data = process * workingDM * workingHD;
      income = salary * transport * endowment * extraT * bonification;

      if (!data || income == null || process == '' || process == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      $('#factor').prop('disabled', false);

      payroll = $('#formCreatePayroll').serialize();

      $.post('/api/addPayroll', payroll, function (data, textStatus, jqXHR) {
        $('#factor').prop('disabled', true);
        $('#createPayroll').modal('hide');
        message(data);
      });
    } else {
      updatePayroll();
    }
  });

  /* Actualizar nomina */

  $(document).on('click', '.updatePayroll', function (e) {
    $('.cardImportPayroll').hide(800);
    $('#createPayroll').modal('show');
    $('#btnCreatePayroll').html('Actualizar');

    idPayroll = this.id;
    idPayroll = sessionStorage.setItem('id_payroll', idPayroll);

    let row = $(this).parent().parent()[0];
    let data = tblPayroll.fnGetData(row);
    $('#employee').val(data.employee);
    $(`#idProcess option:contains(${data.process})`).prop('selected', true);

    $('#basicSalary').val(data.salary.toLocaleString());
    $('#transport').val(data.transport.toLocaleString());
    $('#endowment').val(data.endowment.toLocaleString());
    $('#extraTime').val(data.extra_time.toLocaleString());
    $('#bonification').val(data.bonification.toLocaleString());

    $('#workingHoursDay').val(data.hours_day);
    $('#workingDaysMonth').val(data.working_days_month);

    if (data.type_contract == 'Nomina') {
      $(`#typeFactor option[value=1]`).prop('selected', true);
      $('#factor').val(data.factor_benefit);
    } else if (data.type_contract == 'Servicios') {
      $(`#typeFactor option[value=2]`).prop('selected', true);
      $('#factor').val(data.factor_benefit);
    } else if (data.type_contract == 'Calculo Manual') {
      $(`#typeFactor option[value=3]`).prop('selected', true);
      $('#factor').val(data.factor_benefit);
    }

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updatePayroll = () => {
    $('#factor').prop('disabled', false);
    let data = $('#formCreatePayroll').serialize();
    idPayroll = sessionStorage.getItem('id_payroll');
    data = `${data}&idPayroll=${idPayroll}`;

    $.post('/api/updatePayroll', data, function (data, textStatus, jqXHR) {
      $('#factor').prop('disabled', true);
      $('#createPayroll').modal('hide');
      message(data);
    });
  };

  /* Eliminar carga nomina */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblPayroll.fnGetData(row);

    dataPayroll = [];
    dataPayroll['idPayroll'] = data.id_payroll;
    dataPayroll['idProcess'] = data.id_process;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta nómina? Esta acción no se puede reversar.',
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
            '/api/deletePayroll',
            dataPayroll,
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
      $('.cardCreatePayroll').hide(800);
      $('#formCreatePayroll').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblPayroll').DataTable().clear();
    $('#tblPayroll').DataTable().ajax.reload();
  }

  $(document).on('keyup', '#workingHoursDay', function (e) {
    debugger;
    value = this.value;
    if (value > 16) {
      toastr.error('El número de horas por dia no puede ser mayor a 16');
      $('#workingHoursDay').val('');
      return false;
    }
  });

  $(document).on('keyup', '#workingDaysMonth', function (e) {
    value = this.value;
    if (value > 31) {
      toastr.error('El número de días por mes no puede ser mayor a 31');
      $('#workingDaysMonth').val('');
      return false;
    }
  });
});
