$(document).ready(function () {
    const loadAllData = async () => {
        const [dataProducts, dataEconomyScale] = await Promise.all([
            searchData('/api/saleObjectives'),
            searchData('/api/calcEconomyScale')
        ]); 
            
        sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

        sessionStorage.setItem('allEconomyScale', JSON.stringify(dataEconomyScale));
 
        await loadTblProducts(dataProducts);

        // if (dataProducts.length > 0) {
        //     $('#profitability').val(dataProducts[0].profitability);
        // };
    };

    /* Cargue tabla de Proyectos */

    const loadTblProducts = (data) => {
        if ($.fn.dataTable.isDataTable("#tblProducts")) {
            var table = $("#tblProducts").DataTable();
            var pageInfo = table.page.info(); // Guardar información de la página actual
            table.clear();
            table.rows.add(data).draw();
            table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
            return;
        }

        tblProducts = $('#tblProducts').dataTable({
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
                    className: 'uniqueClassName',
                },
                {
                    title: 'Precio 1',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data) {
                        // data.unit_sold == 0 ? units = '' : units = parseInt(data.unit_sold).toLocaleString('es-CO', { minimumFractionDigits: 0 });

                        return `<div id="realPrice-1-${data.id_product}">
                            <span class="badge badge-success" style="font-size: 16px;"></span>
                        </div>`;
                    },
                }, 
                {
                    title: 'Precio 2',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data) { 
                        return `<div id="realPrice-2-${data.id_product}">
                            <span class="badge badge-success" style="font-size: 16px;"></span>
                        </div>`;
                    },
                }, 
                {
                    title: 'Precio 3',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data) { 
                        return `<div id="realPrice-3-${data.id_product}">
                            <span class="badge badge-success" style="font-size: 16px;"></span>
                        </div>`;
                    },
                }, 
            ],
        });
    }

    loadAllData();
});