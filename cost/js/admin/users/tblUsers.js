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

  plan_cost_quote == 1 ? (plan_cost_quote = true) : (plan_cost_quote = false);

  plan_cost_support == 1
    ? (plan_cost_support = true)
    : (plan_cost_support = false);

  tblUsers = $('#tblUsers').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/costUsersAccess',
      dataSrc: '',
    },
    dom: '<"datatable-error-console">frtip',
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
    },
    fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
      if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
        console.error(oSettings.json.error);
      }
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
        title: 'Nombres',
        data: 'firstname',
        className: 'uniqueClassName',
      },
      {
        title: 'Email',
        data: 'email',
        className: 'uniqueClassName',
      },
      {
        title: 'Crear Productos',
        data: 'create_product',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Crear Materiales',
        data: 'create_materials',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Crear Máquinas',
        data: 'create_machines',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Crear Procesos',
        data: 'create_process',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Ficha Técnica Materiales',
        data: 'product_materials',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Ficha Técnica Procesos',
        data: 'product_process',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },

      {
        title: 'Carga fabril',
        data: 'factory_load',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Servicios Externos',
        data: 'external_service',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Carga Nómina',
        data: 'payroll_load',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Asignación Gastos',
        data: 'expense',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Distribución Gastos',
        data: 'expense_distribution',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Usuarios',
        data: 'user',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Backup',
        data: 'backup',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Metodos de pago',
        data: 'quote_payment_method',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Compañias',
        data: 'quote_company',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Contactos',
        data: 'quote_contact',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Precios',
        data: 'price',
        className: 'uniqueClassName',
        visible: plan_cost_price,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Precios USD',
        data: 'price_usd',
        className: 'uniqueClassName',
        visible: price_usd,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Precios Personalizados',
        data: 'custom_price',
        className: 'uniqueClassName',
        visible: plan_custom_price,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Analisis Materia Prima',
        data: 'analysis_material',
        className: 'uniqueClassName',
        visible: plan_cost_analysis_material,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Simulador',
        data: 'simulator',
        className: 'uniqueClassName',
        visible: plan_cost_simulator,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Economia De Escala',
        data: 'economy_scale',
        className: 'uniqueClassName',
        visible: plan_cost_economy_sale,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Pto De Equilibrio Multiproducto',
        data: 'multiproduct',
        className: 'uniqueClassName',
        visible: plan_cost_multiproduct,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Cotizaciones',
        data: 'quote',
        className: 'uniqueClassName',
        visible: plan_cost_quote,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Soporte',
        data: 'support',
        className: 'uniqueClassName',
        visible: plan_cost_support,
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Acciones',
        data: 'id_user',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUser" data-toggle='tooltip' title='Actualizar Usuario' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Usuario' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
    columnDefs: [
      {
        targets: [1],
        render: function (data, type, row) {
          return data + '  ' + row.lastname + ' ';
        },
      },
    ],
  });
});
