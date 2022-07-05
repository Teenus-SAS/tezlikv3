$(document).ready(function () {
  /* Cargar Inventario productos */
  loadTable = (data) => {
    tblInventories = $('#tblInventories').dataTable({
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
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Descripción',
          data: 'description',
          className: 'uniqueClassName',
        },
        {
          title: 'Categoria',
          data: 'category',
          className: 'classCenter',
        },
        {
          title: 'Unidad',
          data: 'unit',
          className: 'classCenter',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'uniqueClassName',
        },
      ],
    });
  };
});
