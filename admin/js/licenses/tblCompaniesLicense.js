$(document).ready(function () {
  /* Cargue tabla Empresas licencia */
  tblCompaniesLic = $('#tblCompaniesLicense').dataTable({
    pageLength: 50,
    // ajax: {
    //   url: '/api/licenses',
    //   dataSrc: '',
    // },
    ajax: function (data, callback, settings) {
      fetch(`/api/licenses`)
        .then(response => response.json())
        .then(data => {
          // Si el servidor indica recargar la página
          if (data.reload) {
            location.reload();
          } else if (Array.isArray(data) && data.length > 0) {
            // Si `data` es un array, se envía en un objeto para que DataTables lo interprete correctamente
            callback({ data: data });
          } else if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
            // Verificar estructura `{ data: [...] }`
            callback(data);
          } else {
            console.error("Formato de datos inesperado o datos vacíos:", data);
            callback({ data: [] }); // Envía un array vacío para evitar errores en la tabla
          }
        })
        .catch(error => {
          console.error("Error en la carga de datos:", error);
          callback({ data: [] }); // Enviar un array vacío en caso de error
        });
    },
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
        title: 'NIT',
        data: 'nit',
      },
      {
        title: 'Empresa',
        data: 'company',
      },
      {
        title: 'Inicio Licencia',
        data: 'license_start',
      },
      {
        title: 'Final Licencia',
        data: 'license_end',
      },
      {
        title: 'Días de Licencia',
        data: 'license_days',
      },
      {
        title: 'Cant. Usuarios',
        data: 'quantity_user',
      },
      {
        title: 'Estado',
        data: 'license_status',
        render: function (data) {
          if (data == 1) {
            return 'Activo';
          } else {
            return 'Inactivo';
          }
        },
      },
      {
        title: 'Tipo plan',
        data: 'plan',
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
        title: "Accesos",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = [];

          // permissions.push({
          //   name: "Materiales USD",
          //   icon: data.flag_materials_usd == 1
          //     ? "bi bi-check-circle-fill text-success"
          //     : "bi bi-x-circle-fill text-danger",
          //   color: { text: "black" },
          // });
          
          permissions.push({
            name: "Moneda USD",
            icon: data.flag_currency_usd == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });
          
          permissions.push({
            name: "Moneda EUR",
            icon: data.flag_currency_eur == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Procesos Nomina",
            icon: data.flag_employee == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Productos Compuestos",
            icon: data.flag_composite_product == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          if (data.cost_economy_scale == 0) {
            permissions.push({
              name: "Negociaciones Eficientes",
              icon: data.flag_economy_scale == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          }
          
          if (data.cost_sale_objectives == 0) {
            permissions.push({
              name: "Objetivos de Venta",
              icon: data.flag_sales_objective == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          }

          if (data.cost_price_objectives == 0) {
            permissions.push({
              name: "Objetivos de Precios",
              icon: data.flag_price_objective == 1
                ? "bi bi-check-circle-fill text-success"
                : "bi bi-x-circle-fill text-danger",
              color: { text: "black" },
            });
          }
          
          permissions.push({
            name: "Historico",
            icon: data.cost_historical == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Materiales",
            icon: data.flag_indirect == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Importar/Nacionalizar",
            icon: data.flag_export_import == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Inyección",
            icon: data.inyection == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "C. Produccion",
            icon: data.flag_production_center == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          });

          permissions.push({
            name: "Gastos Anuales",
            icon: data.flag_expense_anual == 1
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
        data: 'id_company',
        className: 'uniqueClassName',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateLicenses" data-toggle='tooltip' title='Actualizar Licencia' style="font-size: 30px;"></i></a>                              
          <a href="javascript:;" <i id="${data}" class="bx bx-check-circle licenseStatus" data-toggle='tooltip' title='Estado Licencia' style="font-size: 30px;"></i></a>
          `;
        },
      },
    ],
  });
});
