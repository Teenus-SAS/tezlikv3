$(document).ready(function () {
    const loadAllData = async () => {
        let [dataProducts, dataEconomyScale] = await Promise.all([
            searchData('/api/saleObjectives'),
            searchData('/api/calcEconomyScale')
        ]); 

        sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

        sessionStorage.setItem('allEconomyScale', JSON.stringify(dataEconomyScale));
    
        if (flag_currency_usd == '1' || flag_currency_eur == '1')
            dataProducts = setCurrency(dataProducts);

        await loadTblProducts(dataProducts, 1);

        if (dataProducts.length > 0) {
            $('#profitability').val(dataProducts[0].profitability);
        };
    };

    // Seleccionar moneda
    $(document).on('change', '#selectCurrency', function () {
        let currency = this.value;
 
        sessionStorage.setItem('typeCurrency', currency);
        $('.cardUSD').hide();
        $('.cardEUR').hide();
        let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
        
        switch (currency) {
            case '1': // Pesos
                break;
            case '2': // Dolares
                for (let i = 0; i < dataProducts.length; i++) {
                    dataProducts[i].real_price = parseFloat(dataProducts[i].real_price) / parseFloat(coverage_usd);
                }
                
                $('.cardUSD').show(800);
                break;
            case '3': // Euros
                for (let i = 0; i < dataProducts.length; i++) {
                    dataProducts[i].real_price = parseFloat(dataProducts[i].real_price) / parseFloat(coverage_eur);
                }

                $('.cardEUR').show(800);
                break;
    
            default:
                break;
        }

        loadTblProducts(dataProducts, 2);
    });

    setCurrency = (data) => {
        let typeCurrency = '1';
    
        typeCurrency = sessionStorage.getItem('typeCurrency');

        $('.selectTypeExpense').hide();
 
        switch (typeCurrency) {
            case '2': // Dolares
                for (let i = 0; i < data.length; i++) {
                    data[i].real_price = parseFloat(data[i].real_price) / parseFloat(coverage_usd);
                }
                break;
            case '3': // Euros
                for (let i = 0; i < data.length; i++) {
                    data[i].real_price = parseFloat(data[i].real_price) / parseFloat(coverage_eur); 
                }
                break;
            default:// Pesos COP 
                break;
        }

        return data;
    }
    
    /* Cargue tabla de Proyectos */
    loadTblProducts = (data, op) => {
        let typeCurrency = '1';
    
        if (flag_currency_usd == '1' || flag_currency_eur == '1')
            typeCurrency = sessionStorage.getItem('typeCurrency');

        if ($.fn.dataTable.isDataTable("#tblProducts") && op == 1) {
            var table = $("#tblProducts").DataTable();
            var pageInfo = table.page.info(); // Guardar información de la página actual
            table.clear();
            table.rows.add(data).draw();
            table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
            return;
        }

        tblProducts = $('#tblProducts').DataTable({
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
                    title: 'Unidades',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data) {
                        if (data.unit_sold === false)
                            return '';
                        else {
                            data.unit_sold == 0 ? units = '' : units = parseInt(data.unit_sold).toLocaleString('es-CO', { minimumFractionDigits: 0 });

                            if (data.error === 'false')
                                return `<span class="badge badge-success" style="font-size: 16px;">${units}</span>`;
                            else
                                return `<a href="javascript:;" ><span class="badge badge-danger warningUnit" style="font-size: 16px;">${units}</span></a>`;
                        }
                    },
                },
                {
                    title: 'Precio Real',
                    data: 'real_price',
                    className: 'classCenter',
                    render: function (data) {
                        let price = parseFloat(data);

                        if (isNaN(price)) {
                            price = 0;
                        } else if (Math.abs(price) < 0.01 ) { 
                            price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
                        } else if (typeCurrency != '1') {  
                            price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        } else
                            price = price.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
                        return `$ ${price}`;
                    },
                }, 
            ],
        });
    }

    loadAllData();
});