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
        $('#tblProductsBody').empty();
        let element = document.getElementById('tblProductsBody');

        for (let i = 0; i < data.length; i++) {
            element.insertAdjacentHTML('beforeend',
                `<tr>
                    <td>${i + 1}</td>
                    <td>${data[i].reference}</td>
                    <td>${data[i].product}</td>
                    <td>
                        <div id="realPrice-1-${data[i].id_product}">
                            <span class="badge badge-success" style="font-size: 16px;"></span>
                        </div>
                    </td>
                    <td>
                        <div id="realPrice-2-${data[i].id_product}">
                            <span class="badge badge-success" style="font-size: 16px;"></span>
                        </div>
                    </td>
                    <td>
                        <div id="realPrice-3-${data[i].id_product}">
                            <span class="badge badge-success" style="font-size: 16px;"></span>
                        </div>
                    </td>
                </tr>`);
        }

        $('#tblProducts').DataTable({
            destroy: true,
            pageLength: 50,
            dom: '<"datatable-error-console">frtip',
            language: {
                url: '/assets/plugins/i18n/Spanish.json',
            },
            fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
                    console.error(oSettings.json.error);
                }
            },
        });
    }

    loadAllData();
});