$(document).ready(function () {
  let title = [];

  fetch(`/api/consolidated`)
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);

      getOrderTypes(data.orderTypes);
      loadTblConsolidated(data.consolidated);
    });

  getOrderTypes = (data) => {};

  /* Cargar tabla consolidados */
  loadTblConsolidated = (data, title) => {
    tblConsolidated = $('#tblConsolidated').dataTable({
      pageLength: 50,
      data: data,
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
        {
          title: 'Kardex',
          data: 'quantity',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: title,
          data: null,
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: title,
          data: 'venta_directa',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: title,
          data: 'exportadas',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Total Pedidos',
          data: 'total_orders',
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
          data: 'inventory_days',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Stock Minimo x Semanas',
          data: 'week_minimum_stock',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        // {
        //   title: 'A Producir Con Stock Minimo',
        //   data: 'produce_minimum_stock',
        //   className: 'uniqueClassName',
        //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
        // },
        {
          title: 'A Producir Ajustado',
          data: 'produce_ajusted',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
      ],
    });
  };
});
