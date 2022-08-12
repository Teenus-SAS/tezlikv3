$(document).ready(function () {
  /* Cargar pedidos */
  tblOrder = $('#tblOrder').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/orders',
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
        title: 'Fecha Pedido',
        data: 'date_order',
        className: 'uniqueClassName',
        // render: $.fn.dataTable.render.moment('YYYY/MM/DD'),
      },
      {
        title: 'No Pedido',
        data: 'num_order',
        className: 'uniqueClassName',
      },
      {
        title: 'Producto',
        data: 'product',
        className: 'uniqueClassName',
      },
      {
        title: 'Cliente',
        data: 'client',
        className: 'uniqueClassName',
      },
      {
        title: 'Cantidad Original',
        data: 'original_quantity',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Cantidad Pendiente',
        data: 'quantity',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Saldo Acumulado',
        data: 'accumulated_quantity',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Fecha Entrega',
        data: 'delivery_date',
        className: 'classCenter',
      },
    ],
  });
});
