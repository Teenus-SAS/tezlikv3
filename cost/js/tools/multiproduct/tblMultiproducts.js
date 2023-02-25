$(document).ready(function () {
  $('#btnShowTbl').click(function (e) {
    e.preventDefault();

    $('.cardGraphicMultiproducts').hide(800);
    $('.cardTblMultiproducts').show(800);
  });

  loadTblMultiproducts = async () => {
    let data = await searchData('/api/multiproducts');

    tblMultiproducts = $('#tblMultiproducts').dataTable({
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
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName',
        },
        {
          title: 'No Unidades Vendidas',
          data: 'id_product',
          className: 'uniqueClassName',
          render: function (data) {
            return `<input class="form-control" type="number" id="unity-${data}">`;
          },
        },
        {
          title: 'Precio Venta Unitario',
          data: 'price',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Costo Unitario',
          data: 'cost',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Participacion',
          data: null,
          className: 'classCenter',
          render: function (data) {
            return `<p class="text-center"></p>`;
          },
        },
      ],

      /*footerCallback: function (row, data, start, end, display) {
        total = this.api()
          .column(5)
          .data()
          .reduce(function (a, b) {
            return parseInt(a) + parseInt(b);
          }, 0);

        $(this.api().column(5).footer()).html(
          new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
          }).format(total)
        );
        subTotal = this.api()
          .column(6)
          .data()
          .reduce(function (a, b) {
            return a + b;
          }, 0);

        $(this.api().column(6).footer()).html(`${subTotal.toFixed(0)} %`);
      }, */
    });
  };
});
