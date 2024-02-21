$(document).ready(function () {
  let idProduct;
  let dataProductProcess = {};
  let checkBoxEmployees;

  /* Ocultar panel crear producto */

  $('.cardAddProcess').hide();
  $('.checkMachine').hide();

  /* Abrir panel crear producto */

  $('#btnCreateProcess').click(function (e) {
    e.preventDefault();

    $('.cardImportProductsProcess').hide(800);
    $('.cardAddProcess').toggle(800);
    $('#btnAddProcess').html('Asignar');

    sessionStorage.removeItem('id_product_process');

    if (inyection == '1')
      $('#enlistmentTime').prop('readonly', true);

    $('#formAddProcess').trigger('reset');
    $('#checkMachine').prop('checked', false);
  });

  // $('#idProcess').change(function (e) {
  //   e.preventDefault();

  //   let status = parseInt($(this).find('option:selected').attr('class'));

  //   if (!$('#checkMachine').is(':checked')) {
  //     if (status === 0) {
  //       toastr.error('Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto');
  //       return false;
  //     }
  //   }
  // });
  
  /* Seleccionar producto */
  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    idProduct = $('#selectNameProduct').val();
  });

  /* calcular el tiempo total proceso */

  $(document).on('click keyup', '.time', function (e) {
    let tOperation = $('#operationTime').val();
    let tEnlistment = $('#enlistmentTime').val();

    tOperation == '' ? (tOperation = '0') : tOperation;
    // tOperation = strReplaceNumber(tOperation);

    tEnlistment == '' ? (tEnlistment = '0') : tEnlistment;
    // tEnlistment = strReplaceNumber(tEnlistment);

    if (inyection == 1)
      val = (parseFloat(tEnlistment) / (parseFloat(tOperation) / 100)).toFixed(2);
    else
      val = parseFloat(tEnlistment) + parseFloat(tOperation);

    !isFinite(val) ? val = 0 : val;

    $('#totalTime').val(val);
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

  $(document).on('click', '.updateProcess', function (e) {
    $('.cardImportProductsProcess').hide(800);
    $('.cardAddProcess').show(800);
    $('#btnAddProcess').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblConfigProcess.fnGetData(row);

    sessionStorage.setItem('id_product_process', data.id_product_process);

    $(`#idProcess option[value=${data.id_process}]`).prop('selected', true);

    data.id_machine == null ? (data.id_machine = 0) : data.id_machine;
    $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);

    // let decimals = contarDecimales(data.enlistment_time);
    // let enlistment_time = formatNumber(data.enlistment_time, decimals);
    if (inyection == '1') {
      $('#enlistmentTime').val(data.unity_time);
      $('#enlistmentTime').prop('readonly', true);
    }
    else
      $('#enlistmentTime').val(data.enlistment_time);

    // decimals = contarDecimales(data.operation_time);
    // let operation_time = formatNumber(data.operation_time, decimals);
    $('#operationTime').val(data.operation_time);

    $('#enlistmentTime').click();

    let employees = data.employee.toString().split(",");
    checkBoxEmployees = employees;

    if (data.auto_machine === 1) {
      $('#checkMachine').prop('checked', true);
      $('.checkMachine').show();
    }

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data productos procesos */
  checkDataProductsProcess = async (url, idProductProcess) => {
    idProduct = parseInt($('#selectNameProduct').val());
    let refP = parseInt($('#idProcess').val());
    let refM = parseInt($('#idMachine').val());
    let enlistmentTime = parseFloat($('#enlistmentTime').val());
    let operationTime = parseFloat($('#operationTime').val());
    let status = parseInt($('#idProcess').find('option:selected').attr('class'));

    // enlistmentTime = parseFloat(strReplaceNumber(enlistmentTime));
    // operationTime = parseFloat(strReplaceNumber(operationTime));

    let data = idProduct * refP * operationTime;

    if (inyection == '0')
      data += enlistmentTime;
    
    if (!data || isNaN(refM) || data == 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataProductProcess = new FormData(formAddProcess);
    let autoMachine = 1;
    
    if (!$('#checkMachine').is(':checked')) {
      if (status === 0) {
        toastr.error('Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto');
        return false;
      }
      autoMachine = 0;
    }

    dataProductProcess.append('autoMachine', autoMachine);
    dataProductProcess.append('idProduct', idProduct);
    
    if (idProductProcess != '' || idProductProcess != null) {
      dataProductProcess.append('idProductProcess', idProductProcess);

      flag_employee == '1' ? employees = checkBoxEmployees : employees = '';
      dataProductProcess.append('employees', employees);
    }

    let resp = await sendDataPOST(url, dataProductProcess);

    messageProcess(resp);
  };

  /* Eliminar proceso */

  deleteProcess = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblConfigProcess.fnGetData(row);

    let idProductProcess = data.id_product_process;
    idProduct = $('#selectNameProduct').val();
    dataProductProcess['idProductProcess'] = idProductProcess;
    dataProductProcess['idProduct'] = idProduct;

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
            dataProductProcess,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            }
          );
        }
      },
    });
  };

  /* Modificar empleados */
  $(document).on('click', '.updateEmployee', async function () {
    let row = $(this).parent().parent()[0];
    let data = tblConfigProcess.fnGetData(row);

    let employees = data.employee.toString().split(",");
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

      options += `<div class="checkbox checkbox-success">
                    <input class="checkboxEmployees" id="${payroll[i].id_payroll}" type="checkbox" ${checked}>
                    <label for="${payroll[i].id_payroll}">${payroll[i].employee}</label>
                  </div>`;
    }

    checkBoxEmployees = employees;

    bootbox.confirm({
      title: 'Empleados',
      message: `${options}`,
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

          $.post('/api/saveEmployees', data,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            },
          );
        }
      },
    });
  });

  $(document).on('click', '.checkboxEmployees', function () {
    $(`#${this.id}`).is(':checked') ? op = true : op = false;
    $(`#${this.id}`).prop('checked', op);
    
    if (!$(`#${this.id}`).is(':checked')) {
      for (let i = 0; i < checkBoxEmployees.length; i++) {
        if (checkBoxEmployees[i] == this.id) checkBoxEmployees.splice(i, 1);
      }
      
    } else {
      if (checkBoxEmployees[0] == '') {
        checkBoxEmployees.splice(0, 1);
      }
      checkBoxEmployees.push(this.id);
    }
  });

  /* Mensaje de exito */

  messageProcess = (data) => {
    $('#fileProductsProcess').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    
    if (data.success == true) {
      $('.cardImportProductsProcess').hide(800);
      $('#formImportProductProcess').trigger('reset');
      $('.cardAddProcess').hide(800);
      // $('.cardAddNewProduct').show(800);
      $('#formAddProcess').trigger('reset');
      let idProduct = $('#selectNameProduct').val();
      if (idProduct)
        updateTable();
      toastr.success(data.message);
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblConfigProcess').DataTable().clear();
    $('#tblConfigProcess').DataTable().ajax.reload();
  }
});
