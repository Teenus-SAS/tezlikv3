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

    let data = allPayroll.find(item => item.id_payroll == idPayroll);

    $('#employee').val(data.employee);
    // $(`#idProcess option:contains(${data.process})`).prop('selected', true);
    $(`#idProcess option[value=${data.id_process}]`).prop('selected', true);

    $('#basicSalary').val(data.salary);
    $('#transport').val(data.transport);
    $('#endowment').val(data.endowment);
    $('#extraTime').val(data.extra_time);
    $('#bonification').val(data.bonification);

    $('#workingHoursDay').val(data.hours_day);
    $('#workingDaysMonth').val(data.working_days_month);

    $(`#risk option[value=${data.id_risk}]`).prop('selected', true);
    $('#valueRisk').val(
      data.percentage.toLocaleString('en-US', {
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
    let dataPayroll = new FormData(formCreatePayroll);

    if (type_payroll == '1') {
      let salary = parseFloat($('#basicSalary').val());
      let transport = parseFloat($('#transport').val());
      let endowment = parseFloat($('#endowment').val());
      let extraTime = parseFloat($('#extraTime').val());
      let bonification = parseFloat($('#bonification').val());
      let factor = parseFloat($('#factor').val());
      let risk = parseFloat($('#risk').val());

      // salary = parseFloat(strReplaceNumber(salary));
      basicSalary = salary;
      // transport = parseFloat(strReplaceNumber(transport));
      // endowment = parseFloat(strReplaceNumber(endowment));
      // extraTime = parseFloat(strReplaceNumber(extraTime));
      // bonification = parseFloat(strReplaceNumber(bonification));
      // factor = parseFloat(factor);

      isNaN(transport) ? (transport = 0) : transport;
      isNaN(endowment) ? (endowment = 0) : endowment;
      isNaN(extraTime) ? (extraTime = 0) : extraTime;
      isNaN(bonification) ? (bonification = 0) : bonification;
      isNaN(factor) ? (factor = 0) : factor;

      let workingHD = $('#workingHoursDay').val();
      let workingDM = $('#workingDaysMonth').val();
      let valueRisk = parseFloat(sessionStorage.getItem('percentage'));

      let data = process * workingDM * workingHD * salary * risk;

      if (isNaN(data) || data <= 0 || employee.trim() == '' || factor == '') {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      if (workingDM > 31 || workingHD > 24) {
        toastr.error(
          'El campo dias trabajo x mes debe ser menor a 31, y horas trabajo x dia menor a 24'
        );
        return false;
      }

      $('#factor').prop('disabled', false);
      dataPayroll.append('basicSalary', basicSalary);
      dataPayroll.append('transport', transport);
      dataPayroll.append('endowment', endowment);
      dataPayroll.append('extraTime', extraTime);
      dataPayroll.append('bonification', bonification);
      dataPayroll.append('factor', factor);
      dataPayroll.append('valueRisk', valueRisk);

      salary = parseFloat(
        strReplaceNumber(
          sessionStorage.getItem('salary') || $('#basicSalary').val()
        )
      );
  
      dataPayroll.append('salary', salary);
    }

    if (idPayroll != '' || idPayroll != null)
      dataPayroll.append('idPayroll', idPayroll);

    let resp = await sendDataPOST(url, dataPayroll);

    message(resp);
  };

  /* Eliminar carga nomina */

  deleteFunction = async (id) => {
    let data = allPayroll.find(item => item.id_payroll == id);

    let process = allPayroll.filter(item => item.id_process == data.id_process);

    if (process.length == 1) {
      toastr.error('Proceso asociado a Ficha de productos');
      return false;
    }

    let id_product_process = data.id_product_process.toString().split(",");

    if (id_product_process[0] != 0) {
      if (id_product_process.length == 1) {
        toastr.error('Nomina asociada directamente a Ficha de productos');
        return false;
      }
    }

    dataPayroll['idPayroll'] = data.id_payroll;
    dataPayroll['idProcess'] = data.id_process;
    dataPayroll['id_product_process'] = data.id_product_process;

    let employee = allPayroll.filter(item => item.employee == data.employee);
    // dataPayroll['employee'] = data.employee;
    // let resp = await searchData(`/api/checkEmployee/${data.employee}`);

    employee.length > 1 ? msg = '' : msg = 'Es el unico empleado de nomina.<br>';

    bootbox.confirm({
      title: 'Eliminar',
      message: msg +
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

  /* Copiar Nomina */
  copyFunction = async (id, employee) => {
    var options = ``;

    // let dataProcess = await searchData(`/api/process/${employee}`);
    let process = leaveUniqueKey(allProductProcess, 'id_process');
    let processByEmployee = allPayroll.filter(item => item.employee == employee);

    // Filtrar el primer array para eliminar los elementos que están en el segundo array
    process = process.filter(item => !processByEmployee.find(p => p.id_process === item.id_process));

    if (process.length == 0) {
      toastr.info('No hay procesos disponibles para este empleado');
      return false;
    }

    for (var i = 0; i < process.length; i++) {
      options += `<option value="${process[i].id_process}"> ${process[i].process} </option>`;
    }

    // let row = $(this.activeElement).parent().parent()[0];
    // let data = tblPayroll.fnGetData(row);
    let data = allPayroll.find(item => item.id_payroll == id);

    bootbox.confirm({
      title: 'Clonar Nomina',
      message: `<div class="row">
                  <div class="col-12">
                    <label for="process">Proceso</label>
                    <select class="form-control" id="process">
                      <option disabled selected value="0">Seleccionar</option>
                      ${options}
                    </select>
                  </div>
                </div>`,
      buttons: {
        confirm: {
          label: 'Ok',
          className: 'btn-success',
        },
        cancel: {
          label: 'Cancel',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          let process = $('#process').val();

          if (!process || process == '0') {
            toastr.error('Seleccione proceso');
            return false;
          }

          let dataPayroll = {};
          dataPayroll['idOldPayroll'] = data.id_payroll;
          dataPayroll['idProcess'] = process;
          dataPayroll['employee'] = data.employee;
          dataPayroll['basicSalary'] = data.salary;
          dataPayroll['transport'] = data.transport;
          dataPayroll['extraTime'] = data.extra_time;
          dataPayroll['bonification'] = data.bonification;
          dataPayroll['endowment'] = data.endowment;
          dataPayroll['workingDaysMonth'] = data.working_days_month;
          dataPayroll['workingHoursDay'] = data.hours_day;
          dataPayroll['factor'] = data.factor_benefit;
          dataPayroll['typeFactor'] = data.type_contract;
          dataPayroll['risk'] = data.id_risk;
          dataPayroll['minuteValue'] = data.minute_value;
          dataPayroll['salaryNet'] = data.salary_net;

          $.post(
            '/api/copyPayroll',
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
    $('#filePayroll').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    
    if (data.success == true) {
      $('.cardImportPayroll').hide(800);
      $('#formImportPayroll').trigger('reset');
      sessionStorage.removeItem('percentage');
      sessionStorage.removeItem('salary');
      sessionStorage.removeItem('type_salary');

      $('#factor').prop('disabled', true);
      $('#createPayroll').modal('hide');
      $('#formCreatePayroll').trigger('reset');
      loadAllTblData();
      toastr.success(data.message); 
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
