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
        data: '',
        className: 'uniqueClassName',
      },
      {
        title: 'Referencia',
        data: 'num_order',
        className: 'uniqueClassName',
      },
      {
        title: 'Kardex',
        data: 'product',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Cadenas',
        data: 'client',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Venta Directa',
        data: 'original_quantity',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Expo',
        data: 'quantity',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Total Pedidos',
        data: 'accumulated_quantity',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Total Semana',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Promedio Mes',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Dias Inventario',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Stock Minimo',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'A Producir Con Stock Minimo',
      },
      {
        title: 'A Producir Ajustado',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
    ],
  });
});
