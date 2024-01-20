$(document).ready(function () {
  let dataMachine = {};
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
      checkDataMachines('/api/addMachines', idMachine);
    } else {
      checkDataMachines('/api/updateMachines', idMachine);
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
    // let decimals = contarDecimales(data.cost);
    // let cost = formatNumber(data.cost, decimals);
    $('#costMachine').val(data.cost);
    $('#residualValue').val(data.residual_value);
    $('#depreciationYears').val(data.years_depreciation);

    $('#hoursMachine').val(data.hours_machine);
    $('#daysMachine').val(data.days_machine);
    
    $('#ciclesMachine').val(data.cicles_machine);
    $('#cavities').val(data.cavities);
    
    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Verificar datos */
  checkDataMachines = async (url, idMachine) => {
    let Machine = $('#machine').val();
    let costMachine = $('#costMachine').val();
    let yearsDepreciation = $('#depreciationYears').val();
    let hoursMachine = $('#hoursMachine').val();
    let daysMachine = $('#daysMachine').val();
    let ciclesMachine = $('#ciclesMachine').val();
    let cavities = $('#cavities').val();

    costMachine = parseFloat(costMachine);
    yearsDepreciation = parseFloat(yearsDepreciation);
    hoursMachine = parseFloat(hoursMachine);
    daysMachine = parseFloat(daysMachine);

    let data = costMachine * yearsDepreciation * hoursMachine * daysMachine;
    
    if (inyection == 1)
      data = data * ciclesMachine * cavities;

    if (Machine.trim() == '' || Machine.trim() == null || isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (hoursMachine > 24) {
      toastr.error('Las horas de trabajo no pueden ser mayor a 24');
      return false;
    }

    if (daysMachine > 31) {
      toastr.error('Los dias de trabajo no pueden ser mayor a 31');
      return false;
    }

    let dataMachine = new FormData(formCreateMachine);

    if (idMachine != '' || idMachine != null)
      dataMachine.append('idMachine', idMachine);

    let resp = await sendDataPOST(url, dataMachine);

    message(resp);
  };

  /* Eliminar productos */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblMachines.fnGetData(row);
    let status = parseInt(data.status);

    if (!status == 0) {
      toastr.error('Esta maquina no se puede eliminar, esta configurada a un producto o carga fabril');
      return false;
    }

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
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileMachines').val('');
    
    if (data.success == true) { 
      $('.cardImportMachines').hide(800);
      $('#formImportMachines').trigger('reset');
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
