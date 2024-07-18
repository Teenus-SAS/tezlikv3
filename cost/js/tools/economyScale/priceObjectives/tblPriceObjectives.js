$(document).ready(function () {
    const loadAllData = async () => {
        let [dataProducts, dataEconomyScale] = await Promise.all([
            searchData('/api/priceObjectives'),
            searchData('/api/calcEconomyScale')
        ]);

        dataProducts = dataProducts.map((item) => ({ ...item, check: 0 }));
            
        sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

        sessionStorage.setItem('allEconomyScale', JSON.stringify(dataEconomyScale));
        // let dataCPts = [...dataProducts];
    
        if (flag_currency_usd == '1' || flag_currency_eur == '1') {
            dataProducts = setCurrency(dataProducts);
        };
 
        await loadTblProducts(dataProducts, 1);

        // if (dataProducts.length > 0) {
        //     $('#profitability').val(dataProducts[0].profitability_po);
        //     $('#unity-1').val(dataProducts[0].unit_1);
        //     $('#unity-2').val(dataProducts[0].unit_2);
        //     $('#unity-3').val(dataProducts[0].unit_3);
        // };
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

        loadTblProducts(dataProducts, 2);
    });

    setCurrency = (data) => {
        let typeCurrency = '1';
    
        typeCurrency = sessionStorage.getItem('typeCurrency');

        $('.selectTypeExpense').hide();
 
        switch (typeCurrency) {
            case '2': // Dolares
                for (let i = 0; i < data.length; i++) {
                    data[i].sale_price = parseFloat(data[i].sale_price) / parseFloat(coverage_usd);
                    data[i].price_1 = parseFloat(data[i].price_1) / parseFloat(coverage_usd);
                    data[i].price_2 = parseFloat(data[i].price_2) / parseFloat(coverage_usd);
                    data[i].price_3 = parseFloat(data[i].price_3) / parseFloat(coverage_usd);
                }
                break;
            case '3': // Euros
                for (let i = 0; i < data.length; i++) {
                    data[i].sale_price = parseFloat(data[i].sale_price) / parseFloat(coverage_eur);
                    data[i].price_1 = parseFloat(data[i].price_1) / parseFloat(coverage_eur);
                    data[i].price_2 = parseFloat(data[i].price_2) / parseFloat(coverage_eur);
                    data[i].price_3 = parseFloat(data[i].price_3) / parseFloat(coverage_eur);
                }
                break;
            default:// Pesos COP 
                break;
        }

        return data;
    };

    /* Cargue tabla de Proyectos */
    loadTblProducts = (data, op) => {
        
        // Destruir DataTable si ya existe
        // if ($.fn.DataTable.isDataTable('#tblProducts')) {
        //     $('#tblProducts').DataTable().destroy();
        // }
            
        // $('#tblProducts').empty();
        // typeCurrency = '1';
    
        // if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency'))
        //     typeCurrency = sessionStorage.getItem('typeCurrency');

        // // Obtener los títulos dinámicamente del primer elemento de datos
        // let title1, title2, title3;

        // if (data.length > 0) {
        //     title1 = String(data[0].unit_1);
        //     title2 = String(data[0].unit_2);
        //     title3 = String(data[0].unit_3);
        // } else {
        //     title1 = 'Precio 1';
        //     title2 = 'Precio 2';
        //     title3 = 'Precio 3';            
        // };

        // let check = data.filter(item => item.check == 1);

        // let tbl = document.getElementById('tblProducts');
        // tbl.insertAdjacentHTML('beforeend',
        //     `<thead>
        //         <tr>
        //             <th class="uniqueClassName">
        //                 <label>Seleccionar Todos</label><br>
        //                 <input class="form-control-updated checkProduct" type="checkbox" id="all" ${check.length == data.length ? 'checked' : ''}>
        //             </th>
        //             <th class="uniqueClassName">No.</th>
        //             <th class="uniqueClassName">Referencia</th>
        //             <th class="uniqueClassName">Producto</th>
        //             <th class="uniqueClassName">Precio</th>
        //             <th class="uniqueClassName">${title1}</th>
        //             <th class="uniqueClassName">${title2}</th>
        //             <th class="uniqueClassName">${title3}</th>
        //         </tr>
        //     </thead>
        //     <tbody id="tblProductsBody">
        //     </tbody>`);
        $('#tblProducts').empty();
        typeCurrency = (flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency') || '1';

        // Obtener los títulos dinámicamente del primer elemento de datos
        let [title1, title2, title3] = data.length > 0
            ? [String(data[0].unit_1), String(data[0].unit_2), String(data[0].unit_3)]
            : ['Precio 1', 'Precio 2', 'Precio 3'];

        const check = data.filter(item => item.check === 1);

        const tblHeader = `
            <thead>
                <tr>
                <th class="uniqueClassName">
                    <label>Seleccionar Todos</label><br>
                    <input class="form-control-updated checkProduct" type="checkbox" id="all" ${check.length === data.length ? 'checked' : ''}>
                </th>
                <th class="uniqueClassName">No.</th>
                <th class="uniqueClassName">Referencia</th>
                <th class="uniqueClassName">Producto</th>
                <th class="uniqueClassName">Precio</th>
                <th class="uniqueClassName">${title1}</th>
                <th class="uniqueClassName">${title2}</th>
                <th class="uniqueClassName">${title3}</th>
                </tr>
            </thead>
            <tbody id="tblProductsBody"></tbody>`;

        $('#tblProducts').append(tblHeader);
 
        // let body = document.getElementById('tblProductsBody');

        // for (let i = 0; i < data.length; i++) {
        //     body.insertAdjacentHTML('beforeend',
        //         `<tr>
        //             <td class="uniqueClassName">
        //                 <input type="checkbox" class="form-control-updated checkProduct" id="check-${data[i].id_product}" ${data[i].check == 1 ? 'checked' : ''}>
        //             </td>
        //             <td class="uniqueClassName">${i + 1}</td>
        //             <td class="uniqueClassName">${data[i].reference}</td>
        //             <td class="uniqueClassName">${data[i].product}</td>
        //             <td class="uniqueClassName">${txtTypePrice(data[i])}</td>
        //             <td class="uniqueClassName">${txtProductPrice(data[i], 'price_1')}</td>
        //             <td class="uniqueClassName">${txtProductPrice(data[i], 'price_2')}</td>
        //             <td class="uniqueClassName">${txtProductPrice(data[i], 'price_3')}</td>
        //         </tr>`);
        // }

        const rows = data.map((item, index) => `
            <tr>
                <td class="uniqueClassName">
                <input type="checkbox" class="form-control-updated checkProduct" id="check-${item.id_product}" ${item.check === 1 ? 'checked' : ''}>
                </td>
                <td class="uniqueClassName">${index + 1}</td>
                <td class="uniqueClassName">${item.reference}</td>
                <td class="uniqueClassName">${item.product}</td>
                <td class="uniqueClassName">${txtTypePrice(item)}</td>
                <td class="uniqueClassName">${txtProductPrice(item, 'price_1')}</td>
                <td class="uniqueClassName">${txtProductPrice(item, 'price_2')}</td>
                <td class="uniqueClassName">${txtProductPrice(item, 'price_3')}</td>
            </tr>`).join('');

        $('#tblProductsBody').append(rows);
        // tblProducts = $('#tblProducts').DataTable({
        //     destroy: true,
        //     pageLength: 50,
        //     dom: '<"datatable-error-console">frtip',
        //     language: {
        //         url: '/assets/plugins/i18n/Spanish.json',
        //     },
        //     fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        //         if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
        //             console.error(oSettings.json.error);
        //         }
        //     },
        // });
        // if ($.fn.dataTable.isDataTable("#tblProducts") && op == 1) {
        //     var table = $("#tblProducts").DataTable();
        //     var pageInfo = table.page.info(); // Guardar información de la página actual
        //     table.clear();
        //     table.rows.add(data).draw();
        //     table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
        //     return;
        // }

        // let typeCurrency = '1';
    
        // if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency'))
        //     typeCurrency = sessionStorage.getItem('typeCurrency');

        // // Obtener los títulos dinámicamente del primer elemento de datos
        // let columnTitles = data.length > 0 ? {
        //     title1: String(data[0].unit_1 || "Precio 1"),
        //     title2: String(data[0].unit_2 || "Precio 2"),
        //     title3: String(data[0].unit_3 || "Precio 3")
        // } : { title1: "Precio 1", title2: "Precio 2", title3: "Precio 3" };

        // tblProducts = $('#tblProducts').dataTable({
        //     destroy: true,
        //     pageLength: 50,
        //     data: data,
        //     dom: '<"datatable-error-console">frtip',
        //     language: {
        //         url: '/assets/plugins/i18n/Spanish.json',
        //     },
        //     fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        //         if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
        //             console.error(oSettings.json.error);
        //         }
        //     },
        //     columns: [
        //         {
        //             title: '',
        //             data: null,
        //             className: 'uniqueClassName',
        //             render: function (data, type, full, meta) {
        //                 return `<input type="checkbox" class="form-control-updated checkProduct" id="check-${data.id_product}">`;
        //             },
        //         },
        //         {
        //             title: 'No.',
        //             data: null,
        //             className: 'uniqueClassName',
        //             render: function (data, type, full, meta) {
        //                 return meta.row + 1;
        //             },
        //         },
        //         {
        //             title: 'Referencia',
        //             data: 'reference',
        //             className: 'uniqueClassName',
        //         },
        //         {
        //             title: 'Producto',
        //             data: 'product',
        //             className: 'uniqueClassName',
        //         }, 
        //         {
        //             title: 'Precio',
        //             data: null,
        //             className: 'classCenter',
        //             render: function (data) {
        //                 let sale_price = parseFloat(data.sale_price);
        //                 let title = 'Precio Lista';

        //                 if (sale_price <= 0) { 
        //                     sale_price = parseFloat(data.price);
        //                     title = 'Precio Sugerido';
        //                 };
                        
        //                 if (Math.abs(sale_price) < 0.01) {
        //                     sale_price = sale_price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
        //                 } else if (typeCurrency != '1') {
        //                     sale_price = sale_price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        //                 } else
        //                     sale_price = sale_price.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            
        //                 return `<a href="javascript:;"><i title="${title}" style="color:black;">$ ${sale_price}</i></a>`;
        //             },
        //         },
        //         {
        //             title: columnTitles.title1,
        //             data: null,
        //             className: 'classCenter',
        //             render: function (data) {
        //                 if (data.price_1 === false) {
        //                     return '';
        //                 } else {
        //                     data.price_1 == 0 ? price_1 = '' : price_1 = parseFloat(data.price_1);
        //                     let sale_price = parseFloat(data.sale_price);

        //                     if (sale_price <= 0) {
        //                         sale_price = parseFloat(data.price);
        //                     };

        //                     let txt = '';

        //                     if (typeCurrency != '1') {
        //                         price_1 = price_1.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        //                     } else
        //                         price_1 = price_1.toLocaleString('es-CO', { maximumFractionDigits: 0 });

        //                     if (parseFloat(data.price_1) > sale_price) {
        //                         txt = `<a href="javascript:;" ><span class="badge badge-danger warningPrice" style="font-size: 13px;">$ ${price_1}</span></a>`;
        //                     } else
        //                         txt = `<span class="badge badge-success" style="font-size: 13px;">$ ${price_1}</span>`;
                        
        //                     return txt;
        //                 }
        //             },
        //         }, 
        //         {
        //             title: columnTitles.title2,
        //             data: null,
        //             className: 'classCenter',
        //             render: function (data) {
        //                 if (data.price_2 === false) {
        //                     return '';
        //                 } else {
        //                     data.price_2 == 0 ? price_2 = '' : price_2 = parseFloat(data.price_2);
        //                     let sale_price = parseFloat(data.sale_price);

        //                     if (sale_price <= 0) {
        //                         sale_price = parseFloat(data.price);
        //                     };

        //                     let txt = '';

        //                     if (typeCurrency != '1') {
        //                         price_2 = price_2.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        //                     } else
        //                         price_2 = price_2.toLocaleString('es-CO', { maximumFractionDigits: 0 });

        //                     if (parseFloat(data.price_2) > sale_price) {
        //                         txt = `<a href="javascript:;" ><span class="badge badge-danger warningPrice" style="font-size: 13px;">$ ${price_2}</span></a>`;
        //                     } else
        //                         txt = `<span class="badge badge-success" style="font-size: 13px;">$ ${price_2}</span>`;
                        
        //                     return txt;
        //                 }
        //             },
        //         }, 
        //         {
        //             title: columnTitles.title3,
        //             data: null,
        //             className: 'classCenter',
        //             render: function (data) {
        //                 if (data.price_3 === false) {
        //                     return '';
        //                 } else {
        //                     data.price_3 == 0 ? price_3 = '' : price_3 = parseFloat(data.price_3);
        //                     let sale_price = parseFloat(data.sale_price);

        //                     if (sale_price <= 0) {
        //                         sale_price = parseFloat(data.price);
        //                     };

        //                     let txt = '';

        //                     if (typeCurrency != '1') {
        //                         price_3 = price_3.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        //                     } else
        //                         price_3 = price_3.toLocaleString('es-CO', { maximumFractionDigits: 0 });

        //                     if (parseFloat(data.price_3) > sale_price) {
        //                         txt = `<a href="javascript:;" ><span class="badge badge-danger warningPrice" style="font-size: 13px;">$ ${price_3}</span></a>`;
        //                     } else
        //                         txt = `<span class="badge badge-success" style="font-size: 13px;">$ ${price_3}</span>`;
                        
        //                     return txt;
        //                 }
        //             },
        //         }, 
        //     ],
        // });
    }

    const txtTypePrice = (data) => {
        let sale_price = parseFloat(data.sale_price);
        let title = 'Precio Lista';

        if (sale_price <= 0) {
            sale_price = parseFloat(data.price);
            title = 'Precio Sugerido';
        };
                        
        if (Math.abs(sale_price) < 0.01) {
            sale_price = sale_price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
        } else if (typeCurrency != '1') {
            sale_price = sale_price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else
            sale_price = sale_price.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
        return `<a href="javascript:;"><i title="${title}" style="color:black;">$ ${sale_price}</i></a>`;
    }

    // function txtProductPrice(data, name) {
    //     if (data[name] === false) {
    //         return '';
    //     } else {
    //         let price;

    //         data[name] == 0 ? price = '' : price = parseFloat(data[name]);
    //         let sale_price = parseFloat(data.sale_price);

    //         if (sale_price <= 0) {
    //             sale_price = parseFloat(data.price);
    //         };

    //         let txt = '';

    //         if (typeCurrency != '1') {
    //             price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    //         } else
    //             price = price.toLocaleString('es-CO', { maximumFractionDigits: 0 });

    //         if (parseFloat(data[name]) > sale_price) {
    //             txt = `<a href="javascript:;" ><span class="badge badge-danger warningPrice" style="font-size: 13px;">$ ${price}</span></a>`;
    //         } else
    //             txt = `<span class="badge badge-success" style="font-size: 13px;">$ ${price}</span>`;
                        
    //         return txt;
    //     }
    // }

    const formatPrice = (price, options = { minimumFractionDigits: 0, maximumFractionDigits: 0 }) =>
        price.toLocaleString('es-CO', options);

    // const txtTypePrice = (data) => {
    //     let sale_price = parseFloat(data.sale_price) || parseFloat(data.price);
    //     let title = sale_price <= 0 ? 'Precio Sugerido' : 'Precio Lista';

    //     sale_price = Math.abs(sale_price) < 0.01
    //         ? formatPrice(sale_price, { minimumFractionDigits: 2, maximumFractionDigits: 9 })
    //         : formatPrice(sale_price, typeCurrency !== '1' ?
    //             { minimumFractionDigits: 2, maximumFractionDigits: 2 } : {});

    //     return `<a href="javascript:;"><i title="${title}" style="color:black;">$ ${sale_price}</i></a>`;
    // }

    const txtProductPrice = (data, name) => {
        if (data[name] === false) return '';

        let price = data[name] == 0 ? '' : parseFloat(data[name]);
        const sale_price = parseFloat(data.sale_price) || parseFloat(data.price);
        const formattedPrice = formatPrice(price, typeCurrency !== '1' ? { minimumFractionDigits: 2, maximumFractionDigits: 2 } : {});

        const isPriceHigher = parseFloat(data[name]) > sale_price;
        const badgeClass = isPriceHigher ? 'badge-danger warningPrice' : 'badge-success';
  
        return `<a href="javascript:;" ><span class="badge ${badgeClass}" style="font-size: 13px;">$ ${formattedPrice}</span></a>`;
    }
    loadAllData();
});