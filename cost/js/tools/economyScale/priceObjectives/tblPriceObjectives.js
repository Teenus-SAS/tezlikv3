$(document).ready(function () {
    const loadAllData = async () => {
        const [dataProducts, dataEconomyScale] = await Promise.all([
            searchData('/api/priceObjectives'),
            searchData('/api/calcEconomyScale')
        ]); 
            
        sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

        sessionStorage.setItem('allEconomyScale', JSON.stringify(dataEconomyScale));

        let typeCurrency = '1';
    
        if (flag_currency_usd == '1' || flag_currency_eur == '1')
            typeCurrency = sessionStorage.getItem('typeCurrency');

        $('.selectTypeExpense').hide();

        // price_usd == '1' &&
        switch (typeCurrency) {
            case '2': // Dolares
                for (let i = 0; i < dataProducts.length; i++) {
                    dataProducts[i].sale_price = parseFloat(dataProducts[i].sale_price) / parseFloat(coverage_usd);
                    dataProducts[i].price_1 = parseFloat(dataProducts[i].price_1) / parseFloat(coverage_usd);
                    dataProducts[i].price_2 = parseFloat(dataProducts[i].price_2) / parseFloat(coverage_usd);
                    dataProducts[i].price_3 = parseFloat(dataProducts[i].price_3) / parseFloat(coverage_usd);
                }
                break;
            case '3': // Euros
                for (let i = 0; i < dataProducts.length; i++) {
                    dataProducts[i].sale_price = parseFloat(dataProducts[i].sale_price) / parseFloat(coverage_eur);
                    dataProducts[i].price_1 = parseFloat(dataProducts[i].price_1) / parseFloat(coverage_eur);
                    dataProducts[i].price_2 = parseFloat(dataProducts[i].price_2) / parseFloat(coverage_eur);
                    dataProducts[i].price_3 = parseFloat(dataProducts[i].price_3) / parseFloat(coverage_eur);
                }
                break;
            default:// Pesos COP 
                break;
        }
 
        await loadTblProducts(dataProducts);

        if (dataProducts.length > 0) {
            $('#profitability').val(dataProducts[0].profitability_po);
            $('#unity-1').val(dataProducts[0].unit_1);
            $('#unity-2').val(dataProducts[0].unit_2);
            $('#unity-3').val(dataProducts[0].unit_3);
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
                    dataProducts[i].sale_price = parseFloat(dataProducts[i].sale_price) / parseFloat(coverage_usd);
                    dataProducts[i].price_1 = parseFloat(dataProducts[i].price_1) / parseFloat(coverage_usd);
                    dataProducts[i].price_2 = parseFloat(dataProducts[i].price_2) / parseFloat(coverage_usd);
                    dataProducts[i].price_3 = parseFloat(dataProducts[i].price_3) / parseFloat(coverage_usd);
                }
                
                $('.cardUSD').show(800);
                break;
            case '3': // Euros
                for (let i = 0; i < dataProducts.length; i++) {
                    dataProducts[i].sale_price = parseFloat(dataProducts[i].sale_price) / parseFloat(coverage_eur);
                    dataProducts[i].price_1 = parseFloat(dataProducts[i].price_1) / parseFloat(coverage_eur);
                    dataProducts[i].price_2 = parseFloat(dataProducts[i].price_2) / parseFloat(coverage_eur);
                    dataProducts[i].price_3 = parseFloat(dataProducts[i].price_3) / parseFloat(coverage_eur);
                }

                $('.cardEUR').show(800);
                break;
    
            default:
                break;
        }

        loadTblProducts(dataProducts);
    });

    /* Cargue tabla de Proyectos */
    loadTblProducts = (data) => { 
        // if ($.fn.dataTable.isDataTable("#tblProducts")) {
        //     var table = $("#tblProducts").DataTable();
        //     var pageInfo = table.page.info(); // Guardar información de la página actual
        //     table.clear();
        //     table.rows.add(data).draw();
        //     table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
        //     return;
        // }

        let typeCurrency = '1';
    
        if (flag_currency_usd == '1' || flag_currency_eur == '1')
            typeCurrency = sessionStorage.getItem('typeCurrency');


        // Obtener los títulos dinámicamente del primer elemento de datos
        let columnTitles = data.length > 0 ? {
            title1: data[0].unit_1,
            title2: data[0].unit_2,
            title3: data[0].unit_3
        } : { title1: "Precio 1", title2: "Precio 2", title3: "Precio 3" };

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
                    title: 'Precio Lista',
                    data: 'sale_price',
                    className: 'classCenter',
                    render: function (data) {
                        let sale_price = parseFloat(data);
                        
                        if (Math.abs(sale_price) < 0.01) {
                            sale_price = sale_price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
                        } else if (typeCurrency != '1') {
                            sale_price = sale_price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2  });
                        } else
                            sale_price = sale_price.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
                        return `$ ${sale_price}`;
                    },
                },
                {
                    title: columnTitles.title1,
                    data: null,
                    className: 'classCenter',
                    render: function (data) {
                        if (data.price_1 === false) {
                            return '';
                        } else {
                            data.price_1 == 0 ? price_1 = '' : price_1 = parseInt(data.price_1);

                            let txt = '';

                            if (typeCurrency != '1') {
                                price_1 = price_1.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            } else
                                price_1 = price_1.toLocaleString('es-CO', { maximumFractionDigits: 0 });

                            if (price_1 > data.sale_price) {
                                txt = `<a href="javascript:;" ><span class="badge badge-danger warningPrice" style="font-size: 13px;">$ ${price_1}</span></a>`;
                            } else
                                txt = `<span class="badge badge-success" style="font-size: 13px;">$ ${price_1}</span>`;
                        
                            return txt;
                        }
                    },
                }, 
                {
                    title: columnTitles.title2,
                    data: null,
                    className: 'classCenter',
                    render: function (data) {
                        if (data.price_2 === false) {
                            return '';
                        } else {
                            data.price_2 == 0 ? price_2 = '' : price_2 = parseInt(data.price_2);
                            let txt = '';

                            if (typeCurrency != '1') {
                                price_2 = price_2.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            } else
                                price_2 = price_2.toLocaleString('es-CO', { maximumFractionDigits: 0 });

                            if (price_2 > data.sale_price) {
                                txt = `<a href="javascript:;" ><span class="badge badge-danger warningPrice" style="font-size: 13px;">$ ${price_2}</span></a>`;
                            } else
                                txt = `<span class="badge badge-success" style="font-size: 13px;">$ ${price_2}</span>`;
                        
                            return txt;
                        }
                    },
                }, 
                {
                    title: columnTitles.title3,
                    data: null,
                    className: 'classCenter',
                    render: function (data) {
                        if (data.price_3 === false) {
                            return '';
                        } else {
                            data.price_3 == 0 ? price_3 = '' : price_3 = parseInt(data.price_3);
                            let txt = '';

                            if (typeCurrency != '1') {
                                price_3 = price_3.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            } else
                                price_3 = price_3.toLocaleString('es-CO', { maximumFractionDigits: 0 });

                            if (price_3 > data.sale_price) {
                                txt = `<a href="javascript:;" ><span class="badge badge-danger warningPrice" style="font-size: 13px;">$ ${price_3}</span></a>`;
                            } else
                                txt = `<span class="badge badge-success" style="font-size: 13px;">$ ${price_3}</span>`;
                        
                            return txt;
                        }
                    },
                }, 
            ],
        });
    }

    loadAllData();
});