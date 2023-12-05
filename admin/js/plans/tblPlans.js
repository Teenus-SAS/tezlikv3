$(document).ready(function () {
  tblPlans = $('#tblPlans').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/plansAccess',
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
        title: 'Precios (Detalle Producto)',
        data: 'cost_price',
        className: 'uniqueClassName',
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
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Analisis Materia Prima',
        data: 'cost_analysis_material',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Economia De Escala',
        data: 'cost_economy_scale',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Pto De Equilibrio Multiproducto',
        data: 'cost_multiproduct',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Simulador',
        data: 'cost_simulator',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Cotizaciones',
        data: 'cost_quote',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Soporte',
        data: 'cost_support',
        className: 'uniqueClassName',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
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
});
