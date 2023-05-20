$(document).ready(function () {
  /* Cargue tabla de Proyectos */
  tblCustomPrices = $('#tblCustomPrices').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/customPrices',
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
        className: 'uniqueClassName',
      },
      {
        title: 'Nombre Precio',
        data: 'price_name',
        className: 'uniqueClassName',
      },
      {
        title: 'Precio',
        data: 'price',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Acciones',
        data: 'id_custom_price',
        className: 'uniqueClassName',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateCustomPrice" data-toggle='tooltip' title='Actualizar Precio' style="font-size: 30px;"></i></a>
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Precio' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>
          `;
        },
      },
    ],
  });
});
