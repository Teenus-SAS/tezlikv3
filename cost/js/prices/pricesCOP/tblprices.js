$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblPrices = $('#tblPrices').DataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/prices',
      dataSrc: '',
    },
    dom: '<"datatable-error-console">frtip',
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
    },
    fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
      if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
        console.error(oSettings.json.error);
      }
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
        title: 'Precio (Sugerido)',
        data: 'price',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Precio (Actual)',
        data: 'sale_price',
        className: 'classCenter',
        render: function (data) { 
          if (data > 0)
            return `$ ${data.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`;
          else return '';
         },
      },
      {
        title: 'Rentabilidad',
        data: null,
        className: 'classCenter',
        render: function (data) {
          let dataCost = getDataCost(data);

          if (dataCost.actualProfitability > 0)
            return `${dataCost.actualProfitability.toLocaleString('es-CO', { maximumFractionDigits: 0 })} %`;
          else return '';
        }
      },
      {
        title: 'Img',
        data: 'img',
        className: 'uniqueClassName',
        render: (data, type, row) => {
          data == '' || !data
            ? (txt = '')
            : (txt = `<img src="${data}" alt="" style="width:50px;border-radius:100px">`);
          return txt;
        },
      },
      {
        title: 'Acciones',
        data: 'id_product',
        className: 'uniqueClassName',
        render: function (data) {
          return `<a href="/cost/details-prices" <i id="${data}" class="mdi mdi-playlist-check seeDetail" data-toggle='tooltip' title='Ver Detalle' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
    rowCallback: function (row, data, index) {
      let dataCost = getDataCost(data);
      if (dataCost.actualProfitability < data.profitability && dataCost.actualProfitability > 0) $(row).css('color', 'red');
      
      if (data.details_product == 0) {
        tblPrices.column(5).visible(false);
      }
    },
  });
});
