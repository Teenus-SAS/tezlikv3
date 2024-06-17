$(document).ready(function () {
  let idProduct;
  let checkBoxEmployees = [''];
  sessionStorage.removeItem('dataMachines');

  /* Ocultar panel crear producto */

  $('.cardAddProcess').hide();
  $('.checkMachine').hide();

  /* Abrir panel crear producto */

  $('#btnCreateProcess').click(async function (e) {
    e.preventDefault();

    $('.cardImportProductsProcess').hide(800);
    $('.employees').hide();
    $('#btnAddProcess').html('Asignar');
    
    sessionStorage.removeItem('id_product_process');
    checkBoxEmployees = [''];
    let display = $('.cardAddProcess').css('display');
    let dataMachines = JSON.parse(sessionStorage.getItem('dataMachines'));
    
    if (display == 'none' && !dataMachines) {
      await findSelectProcess();
      await getSelectMachine('/api/selectMachines');
    };

    $('.cardAddProcess').toggle(800);

    if (inyection == '1') $('#enlistmentTime').prop('readonly', true);

    $('#btnEmployees').html('');
    $('#formAddProcess').trigger('reset');
    $('.inputs').css('border-color', '');
    $('#checkMachine').prop('checked', false);
  });

  /* Validar numero de empleados por proceso */
  if (flag_employee == '1') {
    $('#idProcess').change(async function (e) {
      e.preventDefault();
    
      checkBoxEmployees = [''];
      let count_payroll = parseInt(
        $('#idProcess').find('option:selected').attr('class')
      );

      if (count_payroll == 0) {
        toastr.error('Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto');
      }

      $('#btnEmployees').html(count_payroll);
      let dataPayroll = sessionStorage.getItem('dataPayroll');

      if (!dataPayroll) {
        let data = await searchData('/api/basicPayroll');
        sessionStorage.setItem('dataPayroll', JSON.stringify(data));
      }

      $('.employees').show(800);
    });
  }

  /* Seleccionar producto */
  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    idProduct = $('#selectNameProduct').val();
  });

  /* Calcular el tiempo total proceso */
  $(document).on('click keyup', '.time', function (e) {
    let tOperation = parseFloat($('#operationTime').val());
    let tEnlistment = parseFloat($('#enlistmentTime').val());
    let efficiency = parseFloat($('#efficiency').val());

    isNaN(tOperation) ? (tOperation = 0) : tOperation;
    isNaN(tEnlistment) ? (tEnlistment = 0) : tEnlistment;
    isNaN(efficiency) || efficiency == 0 ? (efficiency = 100) : efficiency;

    // Subtotal
    if (inyection == 1)
      subtotal = (tEnlistment / (tOperation / 100)).toFixed(2);
    else subtotal = tEnlistment + tOperation;

    !isFinite(subtotal) ? (subtotal = 0) : subtotal;

    $('#subTotalTime').val(subtotal);

    // Total
    total = subtotal / (efficiency / 100);
    !isFinite(total) ? (total = 0) : (total = total.toFixed(2));

    $('#totalTime').val(total);
  });

  /* Mostrar operadores */
  $('#btnEmployees').click(function (e) {
    e.preventDefault();
    
    let id_process = $('#idProcess').val();

    let dataPayroll = JSON.parse(sessionStorage.getItem('dataPayroll'));
    let idProductProcess = sessionStorage.getItem('id_product_process');
    let employees = [''];

    // Filtrar empleados por proceso
    let data = dataPayroll.filter(item => item.id_process == id_process);

    if(checkBoxEmployees[0] == '' || checkBoxEmployees.length == 0)
      checkBoxEmployees = data.map(item=> (item.id_payroll).toString());

    if (idProductProcess) {
      let dataProductProcess = JSON.parse(sessionStorage.getItem('dataProductProcess'));

      let arr = dataProductProcess.find(
        (item) => item.id_product_process == idProductProcess
      );

      if (checkBoxEmployees[0] == '') {
        employees = arr.employee.toString().split(',');
      }
      else
        employees = checkBoxEmployees;

      if (arr.employee != '')
        checkBoxEmployees = employees;
    } else
      employees = checkBoxEmployees;

    let copyCheckBoxEmployees = [...checkBoxEmployees];
    
    let options = data.map(payrollItem => {
      let checked = '';
      if (employees[0] == '') checked = 'checked';
      else checked = employees.includes(payrollItem.id_payroll.toString()) ? 'checked' : '';

      return `<div class='checkbox checkbox-success'>
            <input class='checkboxEmployees' id='chk-${payrollItem.id_payroll}' type='checkbox' ${checked}>
            <label for='chk-${payrollItem.id_payroll}'>${payrollItem.employee}</label>
          </div>`;
    }).join('');


    bootbox.confirm({
      title: 'Empleados',
      message: options,
      buttons: {
        confirm: {
          label: 'Guardar',
          className: 'btn-success',
        },
        cancel: {
          label: 'Cancelar',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result) {
          if (checkBoxEmployees.length === 0) {
            toastr.error('Seleccione un empleado');
            return false;
          } 

          $('#btnEmployees').html(checkBoxEmployees.length);
        } else {
          checkBoxEmployees = copyCheckBoxEmployees;
        }
      },
    });
  });

  /* Adicionar nuevo proceso */
  $('#btnAddProcess').click(function (e) {
    e.preventDefault();
    let idProductProcess = sessionStorage.getItem('id_product_process');

    if (idProductProcess == '' || idProductProcess == null) {
      checkDataProductsProcess('/api/addProductsProcess', idProductProcess);
    } else {
      checkDataProductsProcess('/api/updateProductsProcess', idProductProcess);
    }
  });

  /* Actualizar productos Procesos */

  $(document).on('click', '.updateProcess',async function (e) {
    $('.cardImportProductsProcess').hide(800);
    $('.checkMachine').hide();
    $('.inputs').css('border-color', '');
    
    let dataMachines = JSON.parse(sessionStorage.getItem('dataMachines'));
    
    if (!dataMachines) {
      await findSelectProcess();
      await getSelectMachine('/api/selectMachines');
    };
    
    $('.cardAddProcess').show(800);
    $('#btnAddProcess').html('Actualizar');

    let dataProductProcess = JSON.parse(sessionStorage.getItem('dataProductProcess'));

    let data = dataProductProcess.find(
      (item) => item.id_product_process == this.id
    );

    sessionStorage.setItem('id_product_process', data.id_product_process);

    $(`#idProcess option[value=${data.id_process}]`).prop('selected', true);
    let count_employee = data.count_employee;

    let employees = data.employee.toString().split(',');
    checkBoxEmployees = data.employee.toString().split(',');

    if (employees[0] != '') {
      count_employee = employees.length;
    }

    $('#btnEmployees').html(count_employee);

    if (parseInt(data.count_employee) == 0) {
      toastr.error('Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto');
    };

    if (flag_employee == '1') {
      let dataPayroll = sessionStorage.getItem('dataPayroll');

      if (!dataPayroll) {
        let data = await searchData('/api/basicPayroll');
        sessionStorage.setItem('dataPayroll', JSON.stringify(data));
      }

      $('.employees').show(800);
    }

    data.id_machine == null ? (data.id_machine = 0) : data.id_machine;
    $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);

    if (inyection == '1') {
      $('#enlistmentTime').val(data.unity_time);
      $('#enlistmentTime').prop('readonly', true);
    } else $('#enlistmentTime').val(data.enlistment_time);

    $('#operationTime').val(data.operation_time);
    $('#efficiency').val(data.efficiency);

    $('#enlistmentTime').click(); 

    if (data.auto_machine == 'SI') {
      $('#checkMachine').prop('checked', true);
    }

    if(data.id_machine != 0)
      $('.checkMachine').show();    
    
    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  function validateForm() {
    let emptyInputs = [];

    let refP = parseInt($('#idProcess').val());
    let refM = parseInt($('#idMachine').val());
    // let enlistmentTime = parseFloat($('#enlistmentTime').val());
    // let operationTime = parseFloat($('#operationTime').val());

    // Verificar cada campo y agregar los vacíos a la lista
    if (!refP) {
      emptyInputs.push('#idProcess'); 
    }

    if (isNaN(refM)) {
      emptyInputs.push('#idMachine');
    }
    
    // if (inyection == '0') {
    //   if (enlistmentTime) {
    //     emptyInputs.push('#enlistmentTime');
    //   }
    // }

    // if (!operationTime) {
    //   emptyInputs.push('#operationTime');
    // }

    // Marcar los campos vacíos con borde rojo
    emptyInputs.forEach(function (selector) {
      $(selector).css('border-color', 'red');
    });

    // Mostrar mensaje de error si hay campos vacíos
    if (emptyInputs.length > 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    return true;
  };

  /* Revision data productos procesos */
  checkDataProductsProcess = async (url, idProductProcess) => {
    if (!validateForm()) {
      return false;
    }
    let idProduct = parseInt($('#selectNameProduct').val());
    // let refP = parseInt($('#idProcess').val());
    // let refM = parseInt($('#idMachine').val());
    // let enlistmentTime = parseFloat($('#enlistmentTime').val());
    // let operationTime = parseFloat($('#operationTime').val());
    // // let efficiency = parseFloat($('#efficiency').val());
    let count_payroll = parseInt(
      $('#idProcess').find('option:selected').attr('class')
    );

    // let data = idProduct * refP * operationTime;

    // if (inyection == '0') data += enlistmentTime;

    // if (!data || isNaN(refM) || data == 0) {
    //   toastr.error('Ingrese todos los campos');
    //   return false;
    // }

    // let dataProductProcess1 = new FormData(formAddProcess);
    let autoMachine = 1;

    if (!$('#checkMachine').is(':checked')) {
      if (count_payroll === 0) {
        $('#idProcess').css('border-color', 'red');

        toastr.error(
          'Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto'
        );
        return false;
      }
      autoMachine = 0;
    }
    let employees = '';

    // dataProductProcess1.append('autoMachine', autoMachine);
    // dataProductProcess1.append('idProduct', idProduct);

    // if (idProductProcess != '' || idProductProcess != null) {
    //   dataProductProcess1.append('idProductProcess', idProductProcess);

    flag_employee == '1' ? (employees = checkBoxEmployees.toString()) : (employees);
      // dataProductProcess1.append('employees', employees);
    // }
    $.ajax({
      type: 'POST',
      url: url,
      data: {
        idProcess: $('#idProcess').val(),
        idMachine: $('#idMachine').val(),
        enlistmentTime: $('#enlistmentTime').val(),
        operationTime: $('#operationTime').val(),
        subTotalTime: $('#subTotalTime').val(),
        efficiency: $('#efficiency').val(),
        totalTime: $('#totalTime').val(),
        autoMachine: autoMachine,
        idProduct: idProduct,
        idProductProcess: idProductProcess,
        employees: employees,
      },
      success: function (resp) {
        messageProcess(resp);
      }
    });

    // let resp = await sendDataPOST(url, dataProductProcess1);

    
  };

  /* Eliminar proceso */
  deleteProcess = (id) => {
    // let row = $(this.activeElement).parent().parent()[0];
    // let data = tblConfigProcess.fnGetData(row);
    let dataProductProcess = JSON.parse(sessionStorage.getItem('dataProductProcess'));
    let data = dataProductProcess.find((item) => item.id_product_process == id);

    let idProductProcess = data.id_product_process;
    idProduct = $('#selectNameProduct').val();
    let dataProductProcess1 = {};
    dataProductProcess1['idProductProcess'] = idProductProcess;
    dataProductProcess1['idProduct'] = idProduct;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este proceso? Esta acción no se puede reversar.',
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
            '/api/deleteProductProcess',
            dataProductProcess1,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            }
          );
        }
      },
    });
  };

  /* Modificar empleados 
  $(document).on('click', '.updateEmployee', async function () {
    let dataProductProcess = JSON.parse(sessionStorage.getItem('dataProductProcess'));

    let data = dataProductProcess.find(
      (item) => item.id_product_process == this.id
    );

    let employees = data.employee.toString().split(',');
    id_product_process = data.id_product_process;
    id_product = data.id_product;

    let payroll = await searchData(`/api/employees/${id_product_process}`);

    let options = '';
    for (let i = 0; i < payroll.length; i++) {
      let checked = '';

      if (!employees[0] == '') {
        for (let j = 0; j < employees.length; j++) {
          if (payroll[i].id_payroll == employees[j]) {
            checked = 'checked';
            break;
          }
        }
      }

      options += `<div class='checkbox checkbox-success'>
                    <input class='checkboxEmployees' id='chk-${payroll[i].id_payroll}' type='checkbox' ${checked}>
                    <label for='chk-${payroll[i].id_payroll}'>${payroll[i].employee}</label>
                  </div>`;
    }

    checkBoxEmployees = employees;

    bootbox.confirm({
      title: 'Empleados',
      message: options,
      buttons: {
        confirm: {
          label: 'Guardar',
          className: 'btn-success',
        },
        cancel: {
          label: 'Cancelar',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          if (checkBoxEmployees.length == 0) {
            toastr.error('Seleccione un empleado');
            return false;
          }

          let data = {};
          data['idProductProcess'] = id_product_process;
          data['idProduct'] = id_product;
          data['employees'] = checkBoxEmployees;

          $.post(
            '/api/saveEmployees',
            data,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            }
          );
        }
      },
    });
  }); */

  $(document).on('click', '.checkboxEmployees', function () {
    // Obtener el ID del checkbox
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idPayRoll = id.split('-')[1];
    $(`#${id}`).is(':checked') ? (op = true) : (op = false);
    $(`#${id}`).prop('checked', op);

    if (!$(`#${id}`).is(':checked')) {
      for (let i = 0; i < checkBoxEmployees.length; i++) {
        if (checkBoxEmployees[i] == idPayRoll) checkBoxEmployees.splice(i, 1);
      }
    } else {
      if (checkBoxEmployees[0] == '') {
        checkBoxEmployees.splice(0, 1);
      }
      checkBoxEmployees.push(idPayRoll);
    }
  });

  /* Mensaje de exito */

  messageProcess = (data) => {
    $('#fileProductsProcess').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);

    checkBoxEmployees = [''];
    if (data.success == true) {
      $('.cardImportProductsProcess').hide(800);
      $('#formImportProductProcess').trigger('reset');
      $('.cardAddProcess').hide(800);
      $('.cardProducts').show(800);
      // $('.cardAddNewProduct').show(800);
      $('#formAddProcess').trigger('reset');
      let idProduct = $('#selectNameProduct').val();
      if (idProduct)
        loadAllDataProcess(idProduct);

      toastr.success(data.message);
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
