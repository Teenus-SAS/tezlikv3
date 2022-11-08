$(document).ready(function () {
  /* ACCESOS DE USUARIO */
  $.ajax({
    url: '/api/planAccess',
    success: function (resp) {
      let acces = {
        prices: resp.cost_price,
        analysisMaterials: resp.cost_analysis_material,
        tools: resp.cost_tool,
        orders: resp.plan_order,
        inventories: resp.plan_inventory,
        programs: resp.plan_program,
        loads: resp.plan_load,
        explosionMaterials: resp.plan_explosion_of_material,
        offices: resp.plan_office,
      };

      $.each(acces, (index, value) => {
        if (value === 0) {
          $(`.${index}`).remove();
        }
      });
    },
  });
});
