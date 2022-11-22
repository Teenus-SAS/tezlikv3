$(document).ready(function () {
  // Ocultar Modal Nuevo usuario
  $('#btnClosePlan').click(function (e) {
    e.preventDefault();
    $('#switchCost').prop('disabled', false);
    $('#switchPlanning').prop('disabled', false);
    $('#formCreatePlan').trigger('reset');

    $('#createPlansAccess').modal('hide');
  });

  /* Accesos de usuario*/
  $('.switch').change(function (e) {
    e.preventDefault();
    if (
      $('#switchCost').is(':checked') &&
      $('#switchPlanning').is(':checked')
    ) {
      $('.cardAccessCost').show(800);
      $('.separator').show(800);
      $('.cardAccessPlanning').show(800);
    } else if ($('#switchCost').is(':checked')) {
      $('.cardAccessCost').show(800);
    } else if ($('#switchPlanning').is(':checked')) {
      $('.cardAccessPlanning').show(800);
    }

    if (!$('#switchCost').is(':checked')) {
      $('.separator').hide();
      $('.cardAccessCost').hide(800);
    }

    if (!$('#switchPlanning').is(':checked')) {
      $('.separator').hide();
      $('.cardAccessPlanning').hide(800);
    }
  });

  $('#btnCreatePlanAccess').click(function (e) {
    e.preventDefault();
    idPlan = sessionStorage.getItem('id_plan');

    dataPlan = {};
    dataPlan['idPlan'] = idPlan;
    dataPlan['cantProducts'] = $('#cantProducts').val();

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

    rol = $('#rol').val();

    if (rol == 1) {
      $('#switchCost').prop('checked', true);
      $('#switchPlanning').prop('disabled', true);
      $('.cardAccessCost').show(800);
      $('.separator').hide();
      $('.cardAccessPlanning').hide();
      $('.inputCantProducts').show();
    }
    if (rol == 2) {
      $('#switchPlanning').prop('checked', true);
      $('#switchCost').prop('disabled', true);
      $('.cardAccessPlanning').show(800);
      $('.separator').hide();
      $('.cardAccessCost').hide();
      $('.inputCantProducts').hide();
    }

    setData(idPlan);
  });

  setData = async (idPlan) => {
    dataPlan = await loaddataAccess();

    for (let i = 0; i < dataPlan.length; i++) {
      if (dataPlan[i]['id_plan'] == idPlan) {
        data = dataPlan[i];
        break;
      }
    }

    $(`#plan option[value=${data.id_plan}]`).prop('selected', true);
    $('#cantProducts').val(data.cant_products);

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
  };

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
    $('#rol').trigger('change');
  }
});
