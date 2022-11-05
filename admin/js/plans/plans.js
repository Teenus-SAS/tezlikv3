$(document).ready(function () {
  // Ocultar Modal Nuevo usuario
  $('#btnClosePlan').click(function (e) {
    e.preventDefault();
    $('#createPlansAccess').modal('hide');
  });

  /* Abrir panel Nuevo usuario 

  $('#btnNewplan').click(function (e) {
    e.preventDefault();
    $('.cardAccessCost').hide();
    $('.separator').hide();
    $('.cardAccessPlanning').hide();
    $('#createPlansAccess').modal('show');
    $('#btnCreateplanAndAccess').html('Crear Usuario y Accesos');

    sessionStorage.removeItem('id_plan');

    $('#nameplan').prop('disabled', false);
    $('#lastnameplan').prop('disabled', false);
    $('#emailplan').prop('disabled', false);

    $('#formCreatePlan').trigger('reset');
  }); */

  /* Agregar nuevo usuario */

  $('#btnCreatePlanAccess').click(function (e) {
    e.preventDefault();
    idPlan = sessionStorage.getItem('id_plan');

    dataPlan = {};
    dataPlan['idPlan'] = idPlan;

    dataPlan = setCheckBoxes(dataPlan);

    $.post(
      '/api/updatePlansAccess',
      dataPlan,
      function (data, textStatus, jqXHR) {
        message(data);
        updateTable();
      }
    );
  });

  /* Actualizar plan */

  $(document).on('click', '.updatePlanAccess', function (e) {
    let idPlan = this.id;
    sessionStorage.setItem('id_plan', idPlan);

    let row = $(this).parent().parent()[0];
    let data = tblPlans.fnGetData(row);

    $(`#plan option[value=${data.id_plan}]`).prop('selected', true);

    // Datos usuario

    let acces = {
      //costos
      prices: data.cost_price,
      analysisMaterials: data.cost_analysis_material,
      tools: data.cost_tool,

      //Planeacion
      inventories: data.plan_inventory,
      orders: data.plan_order,
      programs: data.plan_program,
      loads: data.plan_load,
      explosionMaterials: data.plan_explosion_of_material,
      offices: data.plan_office,
    };

    let i = 1;

    $.each(acces, (index, value) => {
      if (value === 1) {
        $(`#checkbox-${i}`).prop('checked', true);
      } else $(`#checkbox-${i}`).prop('checked', false);
      i++;
    });

    $('#createPlansAccess').modal('show');
    $('#btnCreatePlanAccess').html('Actualizar Accesos');
  });

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataPlan) => {
    for (let i = 1; i <= 9; i++) {
      if ($(`#checkbox-${i}`).is(':checked')) {
        if (i == 1) dataPlan['prices'] = '1';
        if (i == 2) dataPlan['analysisRawMaterials'] = '1';
        if (i == 3) dataPlan['tools'] = '1';
        if (i == 4) dataPlan['inventories'] = '1';
        if (i == 5) dataPlan['orders'] = '1';
        if (i == 6) dataPlan['programming'] = '1';
        if (i == 7) dataPlan['loads'] = '1';
        if (i == 8) dataPlan['explosionOfMaterials'] = '1';
        if (i == 9) dataPlan['offices'] = '1';
      } else {
        if (i == 1) dataPlan['prices'] = '0';
        if (i == 2) dataPlan['analysisRawMaterials'] = '0';
        if (i == 3) dataPlan['tools'] = '0';
        if (i == 4) dataPlan['inventories'] = '0';
        if (i == 5) dataPlan['orders'] = '0';
        if (i == 6) dataPlan['programming'] = '0';
        if (i == 7) dataPlan['loads'] = '0';
        if (i == 8) dataPlan['explosionOfMaterials'] = '0';
        if (i == 9) dataPlan['offices'] = '0';
      }
    }

    return dataPlan;
  };

  /* Eliminar usuario 

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblplans.fnGetData(row);

    let idPlan = data.id_plan;
    let programsMachine = data.programs_machine;
    dataPlan = {};
    dataPlan['idPlan'] = idPlan;
    dataPlan['programsMachine'] = programsMachine;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este Usuario? Esta acción no se puede reversar.',
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
            '/api/deleteplan',
            dataPlan,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  }; */

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('#createPlansAccess').modal('hide');
      $('#formCreatePlan').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblPlans').DataTable().clear();
    $('#tblPlans').DataTable().ajax.reload();
  }
});
