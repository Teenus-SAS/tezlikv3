$(document).ready(function () {
  loadTblProducts = (data) => {
    if ($.fn.DataTable.isDataTable('#tblProducts')) {
      tblProducts.DataTable().clear().rows.add(data).draw();
    } else {
      tblProducts = $('#tblProducts').dataTable({
        destroy: true,
        pageLength: 50,
        data: data,
        language: {
          url: '/assets/plugins/i18n/Spanish.json',
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
          {
            title: 'Acciones',
            data: null,
            className: 'uniqueClassName',
            render: function (data, type, full, meta) {
              return `
                <a href="javascript:;" <i id="${meta.row}" class="bx bx-edit-alt updateProduct" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>`;
            },
          },
        ],
        footerCallback: function (row, data, start, end, display) {
          totalUnits = this.api()
            .column(3)
            .data()
            .reduce(function (a, b) {
              return parseInt(a) + parseInt(b);
            }, 0);

          $(this.api().column(3).footer()).html(
            new Intl.NumberFormat('es-CO').format(totalUnits)
          );
        },
      });
    }
  };
});
