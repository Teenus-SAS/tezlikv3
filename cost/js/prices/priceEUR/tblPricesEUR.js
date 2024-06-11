$(document).ready(function () {
  /* Cargue tabla de Máquinas */
  loadAllData = async () => {
    try {
      const prices = await searchData('/api/prices');

      parents = prices.filter(item => item.composite == 0);
      composites = prices.filter(item => item.composite == 1);

      if (flag_composite_product == '1') {
        loadTblPricesUSD(parents);
      } else
        loadTblPricesUSD(prices);
      
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadTblPricesUSD = (data) => {
    if ($.fn.DataTable.isDataTable('#tblPricesUSD')) {
      tblPricesUSD.DataTable().clear().rows.add(data).draw();
    } else {
      tblPricesUSD = $('#tblPricesUSD').DataTable({
        destroy: true,
        pageLength: 50,
        data: data,
        dom: '<"datatable-error-console">frtip',
        language: {
          url: '/assets/plugins/i18n/Spanish.json',
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
    }
  }
});
