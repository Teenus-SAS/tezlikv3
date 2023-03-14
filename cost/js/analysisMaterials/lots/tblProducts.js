$(document).ready(function () {
  loadTblProducts = (data) => {
    tblProducts = $('#tblProducts').dataTable({
      destroy: true,
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
          title: 'Producto',
          data: 'name',
          className: 'uniqueClassName',
        },
        {
          title: 'Unidades a Fabricar',
          data: 'units',
          className: 'classCenter',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        total = this.api()
          .column(3)
          .data()
          .reduce(function (a, b) {
            return parseInt(a) + parseInt(b);
          }, 0);

        $(this.api().column(3).footer()).html(
          new Intl.NumberFormat('es-CO').format(total)
        );
      },
    });
  };
});
