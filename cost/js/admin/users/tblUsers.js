$(document).ready(function () {
  /* Cargue tabla de Proyectos */

  visible_cost_price = plan_cost_price === '1';
  // price_usd = price_usd === 1;
  visible_custom_price = plan_custom_price === '1';
  visible_analysis_material = plan_cost_analysis_material === '1';
  visible_economy_sale = plan_cost_economy_sale === '1' || flag_economy_scale === '1';
  visible_sale_objectives = plan_cost_economy_sale === '1' || flag_sales_objective === '1';
  visible_multiproduct = plan_cost_multiproduct === '1';
  visible_simulator = plan_cost_simulator === '1';
  visible_historical = plan_cost_historical === '1';
  visible_quote = plan_cost_quote === '1';
  visible_support = plan_cost_support === '1';

  tblUsers = $("#tblUsers").dataTable({
    pageLength: 50,
    ajax: {
      url: "/api/costUsersAccess",
      dataSrc: "",
    },
    dom: '<"datatable-error-console">frtip',
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
    },
    fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
      if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
        console.error(oSettings.json.error);
      }
    },
    columns: [
      {
        title: "No.",
        data: null,
        className: "uniqueClassName",
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: "Acciones",
        data: "id_user",
        className: "uniqueClassName",
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUser" data-toggle='tooltip' title='Actualizar Usuario' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Usuario' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
      {
        title: 'Activar/Inactivar',
        data: null,
        className: 'uniqueClassName',
        render: function (data) {
          data.active == 1 ?
            content = `<a href="javascript:;" <span id="${data.id_user}" class="badge badge-warning checkUser">Inactivar</span></a>` :
            content = `<a href="javascript:;" <span id="${data.id_user}" class="badge badge-success checkUser">Activar</span></a>`;

          return content;
        },
      },
      {
        title: "Nombres",
        data: "firstname",
        className: "uniqueClassName",
      },
      {
        title: "Email",
        data: "email",
        className: "uniqueClassName",
      },
      {
        title: "Maestros",
        data: null,
        //width: "300px",
        render: function (data, type, row) {
          const permissions = [];

          permissions.push({
            name: "Productos",
            icon: data.create_product
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Materias Primas",
            icon: data.create_materials
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Maquinas",
            icon: data.create_machines
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Procesos",
            icon: data.create_process
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          let output = '<div class="stacked-column" style="width:140px">';
          for (const permission of permissions) {
            output += `<span class="text-${permission.color} mx-1" style="display: flex; justify-content: flex-start">
            <i class="${permission.icon}"></i> ${permission.name}
          </span>`;
          }
          output += "</div>";

          return output;
        },
      },
      {
        title: "Configuración",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = [];

          permissions.push({
            name: "Ficha Técnica Productos",
            icon: data.product_materials
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Servicios Externos",
            icon: data.external_service
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Carga Fabril",
            icon: data.factory_load
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          let output = '<div class="stacked-column" style="width:270px">';
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
        title: "General",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = [];

          permissions.push({
            name: "Nomina",
            icon: data.payroll_load
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });
 
          permissions.push({
            name: "Gastos",
            icon: data.expense
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });
          
          permissions.push({
            name: `${flag_expense == 2 ? 'Recuperación Gastos' : 'Distribución Gastos'}`,
            icon: data.expense_distribution
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          if (flag_production_center == '1')
            permissions.push({
              name: "Unidad de Producción",
              icon: data.production_center
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          
          permissions.push({
            name: "Gastos Anuales",
            icon: data.anual_expense
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          let output = '<div class="stacked-column" style="width:170px">';
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
        title: "Cotización",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = [];

          permissions.push({
            name: "Metodos de pago",
            icon: data.quote_payment_method
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Compañias",
            icon: data.quote_company
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Contactos",
            icon: data.quote_contact
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          let output = '<div class="stacked-column" style="width:170px">';
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
        title: "Administrador",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = [];

          permissions.push({
            name: "Backup",
            icon: data.backup
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          }); 

          permissions.push({
            name: "Usuarios",
            icon: data.user
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });          

          let output = '<div class="stacked-column" style="width:60px">';
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
        title: "Menu Navegación",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = [];

          if (visible_cost_price == true)
            permissions.push({
              name: "Precios",
              icon: data.price
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          // if (price_usd == true)
          //   permissions.push({
          //     name: "Precios USD",
          //     icon: data.price_usd
          //       ? "bi bi-check-circle-fill text-success"
          //       : "bi bi-x-circle-fill text-danger",
          //     color: { text: "black" },
          //   });

          if (visible_custom_price == true)
            permissions.push({
              name: "Precios Personalizados",
              icon: data.custom_price
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          
          if (visible_quote == true)
            permissions.push({
              name: "Cotizacion",
              icon: data.quote
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          
          if (visible_support == true)
            permissions.push({
              name: "Soporte",
              icon: data.support
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          let output = '<div class="stacked-column" style="width:190px">';
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

          if (visible_analysis_material == true)
            permissions.push({
              name: "Analisis Materia Prima",
              icon: data.analysis_material
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (visible_economy_sale == true)
            permissions.push({
              name: "Negociaciones Eficientes",
              icon: data.economy_scale
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          
          if (visible_sale_objectives == true)
            permissions.push({
              name: "Objetivos De Ventas",
              icon: data.sale_objectives
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (visible_multiproduct == true)
            permissions.push({
              name: "Pto de Equilibrio",
              icon: data.multiproduct
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (visible_simulator == true)
            permissions.push({
              name: "Simulador",
              icon: data.simulator
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (visible_historical == true)
            permissions.push({
              name: "Historico",
              icon: data.historical
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
        title: "Reportes",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = [];

          permissions.push({
            name: "R. General Costos",
            icon: data.general_cost_report
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
    ],
    columnDefs: [
      {
        targets: [1],
        render: function (data, type, row) {
          return data + "  " + row.lastname + " ";
        },
      },
    ],
  });
});
