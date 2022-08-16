$(document).ready(function () {
  /* Cargar tabla consolidados */
  tblConsolidated = $('#tblConsolidated').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/consolidated',
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
        title: 'Item',
        data: 'num_order',
        className: 'uniqueClassName',
      },
      {
        title: 'Referencia',
        data: 'reference',
        className: 'uniqueClassName',
      },
      // {
      //   title: 'Kardex',
      //   data: 'kardex',
      //   className: 'uniqueClassName',
      //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
      // },
      // {
      //   title: 'Cadenas',
      //   data: '',
      //   className: 'uniqueClassName',
      //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
      // },
      // {
      //   title: 'Venta Directa',
      //   data: 'direct_sale',
      //   className: 'uniqueClassName',
      //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
      // },
      // {
      //   title: 'Exportadas',
      //   data: 'export',
      //   className: 'uniqueClassName',
      //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
      // },
      {
        title: 'Total Pedidos',
        data: 'total_order',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Promedio Mes',
        data: 'average_month',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Dias Inventario',
        data: 'inventory_day',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      // {
      //   title: 'Stock Minimo 2 Semanas',
      //   data: 'week_minimum_stock',
      //   className: 'uniqueClassName',
      //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
      // },
      // {
      //   title: 'A Producir Con Stock Minimo',
      //   data: 'produce_minimum_stock',
      //   className: 'uniqueClassName',
      //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
      // },
      // {
      //   title: 'A Producir Ajustado',
      //   data: 'produce_ajusted',
      //   className: 'uniqueClassName',
      //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
      // },
      // {
      //   title: 'Acciones',
      //   data: 'id_order',
      //   className: 'uniqueClassName',
      //   render: function (data) {
      //     return `
      //           <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt" data-toggle='tooltip' title='Actualizar Consolidado' style="font-size: 30px;"></i></a>
      //           <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Consolidado' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
      //   },
      // },
    ],
  });
});
