$(document).ready(function () {
  /* Cargue tabla de Proyectos */

  plan_cost_price == 1 ? (plan_cost_price = true) : (plan_cost_price = false);

  price_usd == 1 ? (price_usd = true) : (price_usd = false);

  plan_custom_price == 1
    ? (plan_custom_price = true)
    : (plan_custom_price = false);

  plan_cost_analysis_material == 1
    ? (plan_cost_analysis_material = true)
    : (plan_cost_analysis_material = false);

  plan_cost_economy_sale == 1
    ? (plan_cost_economy_sale = true)
    : (plan_cost_economy_sale = false);

  plan_cost_multiproduct == 1
    ? (plan_cost_multiproduct = true)
    : (plan_cost_multiproduct = false);

  plan_cost_simulator == 1
    ? (plan_cost_simulator = true)
    : (plan_cost_simulator = false);

  plan_cost_historical == 1
    ? (plan_cost_historical = true)
    : (plan_cost_historical = false);

  plan_cost_quote == 1 ? (plan_cost_quote = true) : (plan_cost_quote = false);

  plan_cost_support == 1
    ? (plan_cost_support = true)
    : (plan_cost_support = false);

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

          // permissions.push({
          //   name: "Ficha Técnica Procesos y Tiempos",
          //   icon: data.product_process
          //     ? "bi bi-check-circle-fill text-success"
          //     : "bi bi-x-circle-fill text-danger",
          //   color: { text: "black" },
          // });

          permissions.push({
            name: "Carga Fabril",
            icon: data.factory_load
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

          if (flag_expense != 2)
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
            name: "Usuarios",
            icon: data.user
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Backup",
            icon: data.backup
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

          if (plan_cost_price == true)
            permissions.push({
              name: "Precios",
              icon: data.price
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (price_usd == true)
            permissions.push({
              name: "Precios USD",
              icon: data.price_usd
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (plan_custom_price == true)
            permissions.push({
              name: "Precios Personalizados",
              icon: data.custom_price
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          
          if (plan_cost_quote == true)
            permissions.push({
              name: "Cotizacion",
              icon: data.quote
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          
          if (plan_cost_support == true)
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

          if (plan_cost_analysis_material == true)
            permissions.push({
              name: "Analisis Materia Prima",
              icon: data.analysis_material
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (plan_cost_economy_sale == true)
            permissions.push({
              name: "Economia De Escala",
              icon: data.economy_scale
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (plan_cost_multiproduct == true)
            permissions.push({
              name: "Pto de Equilibrio",
              icon: data.multiproduct
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (plan_cost_simulator == true)
            permissions.push({
              name: "Simulador",
              icon: data.simulator
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });

          if (plan_cost_historical == true)
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
