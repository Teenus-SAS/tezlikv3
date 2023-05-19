$(document).ready(function () {
  let dataPayroll = {};

  $('#factor').prop('disabled', true);

  /* Ocultar modal Nueva nomina */
  $('#btnCloseCardPayroll').click(function (e) {
    e.preventDefault();
    sessionStorage.removeItem('percentage');
    sessionStorage.removeItem('salary');
    sessionStorage.removeItem('type_salary');

    $('#createPayroll').modal('hide');
  });

  /* Abrir modal crear nomina */

  $('#btnNewPayroll').click(function (e) {
    e.preventDefault();

    $('.cardImportPayroll').hide(800);
    $('#createPayroll').modal('show');
    $('#btnCreatePayroll').html('Crear');

    sessionStorage.removeItem('id_payroll');

    $('#formCreatePayroll').trigger('reset');
  });

  /* Agregar nueva nomina */

  $('#btnCreatePayroll').click(function (e) {
    e.preventDefault();
    let idPayroll = sessionStorage.getItem('id_payroll');

    if (idPayroll == '' || idPayroll == null) {
      checkDataPayroll('/api/addPayroll', idPayroll);
    } else {
      checkDataPayroll('/api/updatePayroll', idPayroll);
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

    $('#basicSalary').val(data.salary.toLocaleString('es-CO'));
    $('#transport').val(data.transport.toLocaleString('es-CO'));
    $('#endowment').val(data.endowment.toLocaleString('es-CO'));
    $('#extraTime').val(data.extra_time.toLocaleString('es-CO'));
    $('#bonification').val(data.bonification.toLocaleString('es-CO'));

    $('#workingHoursDay').val(data.hours_day);
    $('#workingDaysMonth').val(data.working_days_month);

    $(`#risk option[value=${data.id_risk}]`).prop('selected', true);
    $('#valueRisk').val(
      data.percentage.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );

    sessionStorage.setItem('percentage', data.percentage);
    sessionStorage.setItem('salary', data.salary);

    if (data.type_contract == 'Nomina')
      $(`#typeFactor option[value=1]`).prop('selected', true);
    else if (data.type_contract == 'Servicios')
      $(`#typeFactor option[value=2]`).prop('selected', true);
    else if (data.type_contract == 'Manual')
      $(`#typeFactor option[value=3]`).prop('selected', true);

    $('#typeFactor').change();

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data nomina */
  checkDataPayroll = async (url, idPayroll) => {
    let employee = $('#employee').val();
    let process = $('#idProcess').val();

    let salary = $('#basicSalary').val();
    let transport = $('#transport').val();
    let endowment = $('#endowment').val();
    let extraTime = $('#extraTime').val();
    let bonification = $('#bonification').val();
    let factor = $('#factor').val();

    salary = parseFloat(strReplaceNumber(salary));
    transport = parseFloat(strReplaceNumber(transport));
    endowment = parseFloat(strReplaceNumber(endowment));
    extraTime = parseFloat(strReplaceNumber(extraTime));
    bonification = parseFloat(strReplaceNumber(bonification));
    factor = parseFloat(strReplaceNumber(factor));

    isNaN(transport) ? (transport = 0) : transport;
    isNaN(endowment) ? (endowment = 0) : endowment;
    isNaN(extraTime) ? (extraTime = 0) : extraTime;
    isNaN(bonification) ? (bonification = 0) : bonification;
    isNaN(factor) ? (factor = 0) : factor;

    let workingHD = $('#workingHoursDay').val();
    let workingDM = $('#workingDaysMonth').val();
    let valueRisk = parseFloat(strReplaceNumber($('#valueRisk').val()));

    let data = process * workingDM * workingHD * salary;

    if (isNaN(data) || data <= 0 || employee == '' || factor == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (workingDM > 31 || workingHD > 24) {
      toastr.error(
        'El campo dias trabajo x mes debe ser menor a 31, y horas trabajo x dia menor a 24'
      );
      return false;
    }

    let dataPayroll = new FormData(formCreatePayroll);

    dataPayroll.append('transport', transport);
    dataPayroll.append('endowment', endowment);
    dataPayroll.append('extraTime', extraTime);
    dataPayroll.append('bonification', bonification);
    dataPayroll.append('valueRisk', valueRisk);

    let dataBenefits = sessionStorage.getItem('dataBenefits');
    dataBenefits = JSON.parse(dataBenefits);
    valueBenefits = 0;

    let typeFactor = $('#typeFactor').val();

    if (typeFactor == 1 || typeFactor == 2) {
      if (bonification > 0) {
        salary = sessionStorage.getItem('salary');

        if (!salary) salary = $('#basicSalary').val();

        salary = parseFloat(strReplaceNumber(salary));
      }

      for (i = 0; i < dataBenefits.length + 1; i++) {
        if (!dataBenefits[i]) {
          let valueBenefit =
            (salary + endowment + extraTime) * (valueRisk / 100);
          valueBenefits += valueBenefit;
        } else if (
          dataBenefits[i].id_benefit == 1 ||
          dataBenefits[i].id_benefit == 3
        ) {
          let valueBenefit =
            (salary + endowment + extraTime) *
            (dataBenefits[i].percentage / 100);
          valueBenefits += valueBenefit;
        } else if (dataBenefits[i].id_benefit == 2) {
          if (salary > 1160000 * 10) {
            let valueBenefit =
              (salary + endowment + extraTime) *
              (dataBenefits[i].percentage / 100);
            valueBenefits += valueBenefit;
          }
        } else if (
          dataBenefits[i].id_benefit == 4 ||
          dataBenefits[i].id_benefit == 5
        ) {
          let valueBenefit =
            (salary + endowment + extraTime + transport) *
            (dataBenefits[i].percentage / 100);
          valueBenefits += valueBenefit;
        } else if (dataBenefits[i].id_benefit == 6) {
          let valueBenefit =
            (salary + endowment + transport + extraTime + bonification) *
            (dataBenefits[i].percentage / 100);
          valueBenefits += valueBenefit;
        } else if (dataBenefits[i].id_benefit == 7) {
          let valueBenefit =
            (salary + endowment) * (dataBenefits[i].percentage / 100);
          valueBenefits += valueBenefit;
        }
      }
    } else {
      valueBenefits = (salary + transport) * (factor / 100);
    }

    dataPayroll.append('factor', valueBenefits);

    $('#factor').prop('disabled', false);

    if (idPayroll != '' || idPayroll != null)
      dataPayroll.append('idPayroll', idPayroll);

    let resp = await sendDataPOST(url, dataPayroll);

    message(resp);
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
      sessionStorage.removeItem('percentage');
      sessionStorage.removeItem('salary');
      sessionStorage.removeItem('type_salary');

      $('#factor').prop('disabled', true);
      $('#createPayroll').modal('hide');
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
