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
      },
      {
        title: 'No Pedido',
        data: 'order',
        className: 'uniqueClassName',
      },
      {
        title: 'Producto',
        data: 'reference',
        className: 'uniqueClassName',
      },
      {
        title: 'Cantidad',
        data: 'quantity',
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
        className: 'uniqueClassName',
      },
      {
        title: 'Saldo Acumulado',
        data: 'accumulated_quantity',
        className: 'uniqueClassName',
      },
    ],
  });
});
