$(document).ready(function () {
  loadAllDataPlan = async () => {
    let data = await searchData('/api/plansAccess');

    sessionStorage.setItem('dataPlans', JSON.stringify(data));

    loadTblPlans(data);
  };

  loadTblPlans = (data) => {
    tblPlans = $('#tblPlans').dataTable({
      destroy: true,
      pageLength: 50, 
      data: data,
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Tipo plan',
          data: 'id_plan',
          render: function (data) {
            if (data == 0) {
              return '';
            } else if (data == 1) {
              return 'Premium';
            } else if (data == 2) {
              return 'Pro';
            } else if (data == 3) {
              return 'Pyme';
            } else if (data == 4) {
              return 'Emprendedor';
            }
          },
        },
        {
          title: 'Cantidad Productos',
          data: 'cant_products',
          className: 'uniqueClassName',
        },
        {
          title: "Lista de precios",
          data: null,
          //width: "200px",
          render: function (data, type, row) {
            const permissions = [];
          
            permissions.push({
              name: "Precios (Detalle Producto)",
              icon: data.cost_price == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            permissions.push({
              name: "Precios Personalizados",
              icon: data.custom_price == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            let output =
              '<div class="stacked-column text-left" style="width:190px">';
            for (const permission of permissions) {
              output += `<span class="text-${permission.color} mx-1" style="display: flex; justify-content: flex-start;">
            <i class="${permission.icon}"></i> ${permission.name}
          </span>`;
            }
            output += "</div>";

            return output;
          },
        },
        {
          title: "Herramientas",
          data: null,
          //width: "200px",
          render: function (data, type, row) {
            const permissions = [];
          
            permissions.push({
              name: "Analisis Materia Prima",
              icon: data.cost_analysis_material == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            // permissions.push({
            //   name: "Economia de Escala",
            //   icon: data.cost_economy_scale == 1
            //     ? "bi bi-check-circle-fill text-success"
            //     : "bi bi-x-circle-fill text-danger",
            //   color: { text: "black" },
            // });

            permissions.push({
              name: "Negociaciones Eficientes",
              icon: data.cost_economy_scale == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            permissions.push({
              name: "Objetivos De Ventas",
              icon: data.cost_sale_objectives == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            permissions.push({
              name: "Objetivos De Precios",
              icon: data.cost_price_objectives == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            permissions.push({
              name: "Pto De Equilibrio Multiproducto",
              icon: data.cost_multiproduct == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            permissions.push({
              name: "Simulador",
              icon: data.cost_simulator == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            let output =
              '<div class="stacked-column text-left" style="width:190px">';
            for (const permission of permissions) {
              output += `<span class="text-${permission.color} mx-1" style="display: flex; justify-content: flex-start;">
            <i class="${permission.icon}"></i> ${permission.name}
          </span>`;
            }
            output += "</div>";

            return output;
          },
        },
        {
          title: "Accesos",
          data: null,
          //width: "200px",
          render: function (data, type, row) {
            const permissions = [];
          
            permissions.push({
              name: "Cotizaciones",
              icon: data.cost_quote == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            permissions.push({
              name: "Soporte",
              icon: data.cost_support == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

            let output =
              '<div class="stacked-column text-left" style="width:190px">';
            for (const permission of permissions) {
              output += `<span class="text-${permission.color} mx-1" style="display: flex; justify-content: flex-start;">
            <i class="${permission.icon}"></i> ${permission.name}
          </span>`;
            }
            output += "</div>";

            return output;
          },
        },
        {
          title: 'Acciones',
          data: 'id_plan',
          className: 'uniqueClassName',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePlanAccess" data-toggle='tooltip' title='Actualizar Plan' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
  };

  loadAllDataPlan();
});
