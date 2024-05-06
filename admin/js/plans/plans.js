$(document).ready(function () {
  // Ocultar Modal Nuevo usuario
  $('#btnClosePlan').click(function (e) {
    e.preventDefault();
    $('#formCreatePlan').trigger('reset');

    $('#createPlansAccess').modal('hide');
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
    $('#cantProducts').val(data.cant_products);

    let acces = {
      prices: data.cost_price,
      customPrices: data.custom_price,
      analysisMaterials: data.cost_analysis_material,
      economyScale: data.cost_economy_scale,
      // salesOjective: data.cost_sale_objectives,
      multiproduct: data.cost_multiproduct,
      simulator: data.cost_simulator, 
      quotes: data.cost_quote,
      support: data.cost_support,
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
    let i = 1;

    let access = {
      prices: 0,
      customPrices: 0,
      analysisRawMaterials: 0,
      economyScale: 0,
      // salesOjective: 0,
      multiproduct: 0,
      simulator: 0,
      // historical: 0,
      quotes: 0,
      support: 0,
    };

    $.each(access, (index, value) => {
      if ($(`#checkbox-${i}`).is(':checked')) dataPlan[`${index}`] = 1;
      else dataPlan[`${index}`] = 0;
      i++;
    });

    return dataPlan;
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('#createPlansAccess').modal('hide');
      $('#formCreatePlan').trigger('reset');
      loadAllDataPlan();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
