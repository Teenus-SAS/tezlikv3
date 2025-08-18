
const loadAllData = async () => {
    let [dataProducts, dataEconomyScale] = await Promise.all([
        searchData('/api/saleObjectives'),
        searchData('/api/negotiations/calcEconomyScale')
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

    if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency'))
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
        headerCallback: function (thead, data, start, end, display) {
            $(thead).find("th").css({
                "background-color": "#386297",
                color: "white",
                "text-align": "center",
                "font-weight": "bold",
                padding: "10px",
                border: "1px solid #ddd",
            });
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
                title: 'Und Vendidas / Distribucion',
                data: 'units_sold',
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
                            return `${units}`;
                        else
                            return `<a href="javascript:;" class="warningUnit" style="color:red;">${units}</a>`;
                    }
                },
            },
            {
                title: '% Cumplimiento',
                data: null,
                className: 'uniqueClassName',
                render: function (data) {
                    let percentage = (parseInt(data.units_sold) / parseInt(data.unit_sold)) * 100;
                    isNaN(percentage) || !isFinite(percentage) ? percentage = 0 : percentage;

                    const percentageClasses = [
                        { limit: 100, className: 'badge-success' },
                        { limit: 80, className: 'badge-info' },
                        { limit: 50, className: 'badge-warning' },
                        { limit: 0, className: 'badge-danger' },
                    ];

                    let className = 'badge-danger'; // Default class

                    for (let i = 0; i < percentageClasses.length; i++) {
                        if (percentage > percentageClasses[i].limit) {
                            className = percentageClasses[i].className;
                            break;
                        }
                    }

                    return `<span class="badge ${className}" style="font-size: 16px;">${percentage.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} %</span>`;
                },
            },
            {
                title: 'Precio',
                data: null,
                className: 'classCenter',
                render: function (data) {
                    let price = parseFloat(data.real_price);
                    let title = 'Precio Real';

                    if (price <= 0) {
                        if (data.sale_price <= 0) {
                            price = parseFloat(data.price);
                            title = 'Precio Lista';
                        }
                        else {
                            price = parseFloat(data.sale_price);
                            title = 'Precio Sugerido';
                        }
                    };

                    if (isNaN(price)) {
                        price = 0;
                    } else if (Math.abs(price) < 0.01) {
                        price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
                    } else if (typeCurrency != '1') {
                        price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    } else
                        price = price.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

                    return `<a href="javascript:;" <i title="${title}" style="color:black;">$ ${price}</i></a>`;
                },
            },
        ],
        headerCallback: function (thead, data, start, end, display) {
            $(thead).find("th").css({
                "background-color": "#386297",
                color: "white",
                "text-align": "center",
                "font-weight": "bold",
                padding: "10px",
                border: "1px solid #ddd",
            });
        },
    });
}

$(document).ready(function () {
    loadAllData();
});