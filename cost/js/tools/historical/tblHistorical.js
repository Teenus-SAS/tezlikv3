$(document).ready(function () {
    $(document).on('click', '.seeDetail', function (e) {
        sessionStorage.removeItem('idProduct');
        let id_product = this.id;
        sessionStorage.setItem('idProduct', id_product);
    });
    /* Cargue tabla de Precios */
  
    loadTblPrices = async (op, month, year) => {
        let data = await searchData('/api/historical');
        // const fechaActual = new Date();

        // Obtener el año actual
        if (op == 1) {
            // const month = fechaActual.getMonth() + 1;
            data = data.filter((item) => item.month == month);
        } else if (op == 2) {
            // const year = fechaActual.getFullYear();
            data = data.filter((item) => item.year == year);
        } 

        tblHistorical = $('#tblHistorical').DataTable({
            destroy:true,
            pageLength: 50,
            data: data,
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
                    // visible: visible,
                    render: function (data) {
                        if (data > 0)
                            return `$ ${data.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`;
                        else return '';
                    },
                },
                {
                    title: 'Rentabilidad',
                    data: 'profitability',
                    className: 'classCenter',
                },
                // {
                //     title: 'Mes',
                //     data: 'month',
                //     className: 'classCenter',
                // },
                // {
                //     title: 'Año',
                //     data: 'year',
                //     className: 'classCenter',
                // },
                {
                    title: 'Acciones',
                    data: 'id_product',
                    className: 'uniqueClassName',
                    render: function (data) {
                        return `<a href="/cost/details-historical" <i id="${data}" class="mdi mdi-playlist-check seeDetail" data-toggle='tooltip' title='Ver Detalle' style="font-size: 30px;"></i></a>`;
                    },
                },
            ],
            // rowCallback: function (row, data, index) {
            //   let dataCost = getDataCost(data);
            //   !isFinite(dataCost.actualProfitability) ? dataCost.actualProfitability = 0 : dataCost.actualProfitability;

            //   if (dataCost.actualProfitability < data.profitability && dataCost.actualProfitability > 0 && data.sale_price > 0) $(row).css('color', 'orange');
            //   else if (dataCost.actualProfitability < data.profitability && data.sale_price > 0) $(row).css('color', 'red');
      
            //   if (data.details_product == 0) {
            //     tblPrices.column(7).visible(false);
            //   }
            // },
        });
    }

    loadTblPrices(null, null, null);
});
