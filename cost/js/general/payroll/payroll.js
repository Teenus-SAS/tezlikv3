$(document).ready(function () {
  let dataPayroll = {};

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

  /* Agregar nueva carga nomina */

  $('#btnCreatePayroll').click(function (e) {
    e.preventDefault();
    let idPayroll = sessionStorage.getItem('id_payroll');

    if (idPayroll == '' || idPayroll == null) {
      let employee = $('#employee').val();
      let process = parseInt($('#idProcess').val());

      let salary = $('#basicSalary').val();
      let transport = $('#transport').val();
      let endowment = $('#endowment').val();
      let extraT = $('#extraTime').val();
      let bonification = $('#bonification').val();

      let workingHD = parseInt($('#workingHoursDay').val());
      let workingDM = parseInt($('#workingDaysMonth').val());
      //factor = parseInt($('#typeFactor').val());

      let data = process * workingDM * workingHD;
      let income = salary * transport * endowment * extraT * bonification;

      if (!data || income == null || process == '' || process == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      $('#factor').prop('disabled', false);

      let payroll = $('#formCreatePayroll').serialize();

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

    let idPayroll = this.id;
    sessionStorage.setItem('id_payroll', idPayroll);

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
    let idPayroll = sessionStorage.getItem('id_payroll');
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
});
