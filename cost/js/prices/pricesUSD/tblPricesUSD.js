$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblPricesUSD = $('#tblPricesUSD').DataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/prices',
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
        title: 'Referencia',
        data: 'reference',
        className: 'uniqueClassName',
      },
      {
        title: 'Producto',
        data: 'product',
        className: 'classCenter',
      },
      {
        title: 'Precio',
        data: 'price_usd',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 2, '$ '),
      },
      {
        title: 'Img',
        data: 'img',
        className: 'uniqueClassName',
        render: (data, type, row) => {
          data ? data : (data = '');
          ('use strict');
          return `<img src="${data}" alt="" style="width:50px;border-radius:100px">`;
        },
      },
    ],
  });
});
