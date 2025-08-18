
let real_price = 100;
let cant = 1;
let dataPO = [];

$('.cardCalcPriceObjectives').hide();

$('#btnNewCalcPO').click(function () {
    // e.preventDefault();

    let checks = $('.checkProduct');
    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

    for (let i = 0; i < checks.length; i++) {
        let id_product = checks[i].id.split('-')[1];
        let newValue;

        if ($(checks[i]).is(':checked'))
            newValue = 1;
        else
            newValue = 0;

        dataProducts = dataProducts.map(item => {
            if (item.id_product === id_product) {
                return { ...item, check: newValue };
            }
            return item;
        });
    }

    sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

    $('.cardCalcPriceObjectives').toggle(800);
});

// Cuando se ingrese rentabilidad general
$('#calcPriceObj').click(function (e) {
    e.preventDefault();

    let profitability_1 = parseFloat($('#profitability1').val());
    let profitability_2 = parseFloat($('#profitability2').val());
    let profitability_3 = parseFloat($('#profitability3').val());

    let unit1 = parseFloat($('#unity-1').val());
    let unit2 = parseFloat($('#unity-2').val());
    let unit3 = parseFloat($('#unity-3').val());

    isNaN(profitability_1) ? profitability_1 = 0 : profitability_1;
    isNaN(profitability_2) ? profitability_2 = 0 : profitability_2;
    isNaN(profitability_3) ? profitability_3 = 0 : profitability_3;
    isNaN(unit1) ? unit1 = 0 : unit1;
    isNaN(unit2) ? unit2 = 0 : unit2;
    isNaN(unit3) ? unit3 = 0 : unit3;

    let data = profitability_1 * profitability_2 * profitability_3 * unit1 * unit2 * unit3;

    if (data <= 0) {
        toastr.error('Ingrese todos los campos');
        return false;
    }

    if (!$('.checkProduct').is(':checked')) {
        // let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

        // dataProducts = dataProducts.map((item) => ({ ...item, check: 0 }));

        // sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));
        toastr.error('Seleccione un campo para realizar el calculo');
        return false;
    }

    dataPO = [];

    $('.cardBottons').hide();
    $('.cardCalcPriceObjectives').hide(800);

    let form = document.getElementById('formProducts');

    form.insertAdjacentHTML(
        'beforeend',
        `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
                <div class="spinner-border text-secondary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>`
    );

    generalCalc([unit1, unit2, unit3], [profitability_1, profitability_2, profitability_3]);
});

    // Calculo general economia de escala para obtener unidades
    /* */ const generalCalc = async (units, profitabilities) => {
    try {
        let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
        let allEconomyScale = JSON.parse(sessionStorage.getItem('allEconomyScale'));

        cant = 1;

        // Definir una función asíncrona para manejar cada iteración del ciclo
        const handleIteration = async (arr, c_unit, profitability) => {
            if (c_unit > 0) {
                /* Costos Variables */
                let economyScale = allEconomyScale.find(item => item.id_product == arr.id_product);

                let totalVariableCost = economyScale.variableCost * c_unit;

                let costFixed = economyScale.costFixed;

                /* Total Costos y Gastos */
                let totalCostsAndExpense = costFixed + totalVariableCost;

                /* Calculo Total Ingresos */
                let totalRevenue = c_unit * real_price;

                /* Calculo Costo x Unidad */
                let unityCost = parseFloat(totalCostsAndExpense) / c_unit;

                /* Calculo Utilidad x Unidad */
                let unitUtility = real_price - unityCost;

                /* Calculo Utilidad Neta */
                let netUtility = unitUtility * c_unit;

                /* Porcentaje */
                let percentage = (netUtility / totalRevenue) * 100;

                if (profitability <= percentage)
                    return real_price;

                real_price = Math.ceil((totalCostsAndExpense / c_unit) + cant);

                await new Promise(resolve => setTimeout(resolve, 0));
                cant += 100;
                return -1;
            } else
                return c_unit;
        };

        let check = dataProducts.filter(item => item.check == 1);

        if (check.length == dataProducts.length) {
            for (let i = 0; i < dataProducts.length; i++) {
                // Iterar sobre cada índice 
                for (let j = 0; j < units.length; j++) {
                    let product_price = await handleIteration(dataProducts[i], units[j], profitabilities[j]);

                    if (product_price === -1) {
                        j = j - 1;
                    } else {
                        dataProducts[i][`profitability_${j + 1}`] = profitabilities[j];
                        dataProducts[i][`unit_${j + 1}`] = units[j];
                        dataProducts[i][`price_${j + 1}`] = product_price;
                        cant = 1;
                        real_price = 100;
                    }
                }

                let dataCPts = JSON.parse(JSON.stringify(dataProducts));

                if (flag_currency_usd == '1' || flag_currency_eur == '1') {
                    let arr = await setCurrency([dataCPts[i]]);
                    dataCPts[i] = arr[0];
                }

                dataPO.push(dataCPts[i]);

                loadTblProducts(dataPO, 1);
            }
        } else {
            // Si no hay ningun producto checkeado calcula global
            for (let i = 0; i < check.length; i++) {
                // Iterar sobre cada índice 
                for (let j = 0; j < units.length; j++) {
                    let product_price = await handleIteration(check[i], units[j], profitabilities[j]);

                    if (product_price === -1) {
                        j = j - 1;
                    } else {
                        check[i][`profitability_${j + 1}`] = profitabilities[j];
                        check[i][`unit_${j + 1}`] = units[j];
                        check[i][`price_${j + 1}`] = product_price;
                        cant = 1;
                        real_price = 100;
                    }
                }

                let dataCPts = JSON.parse(JSON.stringify(check));

                if (flag_currency_usd == '1' || flag_currency_eur == '1') {
                    let arr = await setCurrency([dataCPts[i]]);
                    dataCPts[i] = arr[0];
                }
                dataPO.push(dataCPts[i]);
                loadTblProducts(dataPO, 1);

                dataProducts = dataProducts.map(item => {
                    if (item.id_product === check[i].id_product) {
                        return {
                            ...item,
                            profitability_1: check[i].profitability_1,
                            profitability_2: check[i].profitability_2,
                            profitability_3: check[i].profitability_3,
                            unit_1: check[i].unit_1,
                            unit_2: check[i].unit_2,
                            unit_3: check[i].unit_3,
                            price_1: check[i].price_1,
                            price_2: check[i].price_2,
                            price_3: check[i].price_3,
                        };
                    }
                    return item;
                });
            };
            loadTblProducts(dataProducts, 1);
        }

        sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

        savePriceObjectives(dataProducts);
    } catch (error) {
        console.log(error);
    }
};

// Guardar datos objetivos de ventas
const savePriceObjectives = (data) => {
    $.ajax({
        type: "POST",
        url: "/api/objetivesPrices/savePriceObjectives",
        data: { products: data },
        success: function (resp) {
            if (resp.reload) {
                location.reload();
            }

            dataPO = [];

            let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
            // dataProducts = dataProducts.map((item) => ({ ...item, check: 0 }));
            sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));

            $('html, body').animate({ scrollTop: 0 }, 1000);
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('.cardCalcPriceObjectives').show(400);

            if (resp.success == true) {
                toastr.success(resp.message);
                return false;
            } else if (resp.error == true) toastr.error(resp.message);
            else if (resp.info == true) toastr.info(resp.message);

        }
    });
};

$('#btnExportPObjectives').click(function (e) {
    e.preventDefault();

    let wb = XLSX.utils.book_new();
    let data = [];

    /* Productos */
    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

    if (dataProducts.length > 0) {

        if (flag_currency_usd == '1' || flag_currency_eur == '1')
            dataProducts = setCurrency(dataProducts);

        // Definir nombres de los campos
        let unit_1_name = dataProducts[0].unit_1.toString();
        let unit_2_name = dataProducts[0].unit_2.toString();
        let unit_3_name = dataProducts[0].unit_3.toString();

        for (let i = 0; i < dataProducts.length; i++) {
            let sale_price = parseFloat(dataProducts[i].sale_price);

            if (sale_price <= 0) {
                sale_price = parseFloat(dataProducts[i].price);
            };

            let item = {
                referencia: dataProducts[i].reference,
                producto: dataProducts[i].product,
                precio: sale_price
            };

            item[unit_1_name] = parseFloat(dataProducts[i].price_1) > parseFloat(dataProducts[i].sale_price) ? (parseFloat(dataProducts[i].price_1) * -1) : parseFloat(dataProducts[i].price_1.toString().replace('.', ','));
            item[unit_2_name] = parseFloat(dataProducts[i].price_2) > parseFloat(dataProducts[i].sale_price) ? (parseFloat(dataProducts[i].price_2) * -1) : parseFloat(dataProducts[i].price_2.toString().replace('.', ','));
            item[unit_3_name] = parseFloat(dataProducts[i].price_3) > parseFloat(dataProducts[i].sale_price) ? (parseFloat(dataProducts[i].price_3) * -1) : parseFloat(dataProducts[i].price_3.toString().replace('.', ','));

            data.push(item);
        }

        // Ordenar los campos al generar la hoja
        let ws = XLSX.utils.json_to_sheet(data, {
            header: ['referencia', 'producto', 'precio', unit_1_name, unit_2_name, unit_3_name]
        });

        XLSX.utils.book_append_sheet(wb, ws, 'Productos');
    }
    XLSX.writeFile(wb, 'Objetivos_Precios.xlsx');
});

$(document).on('click', '.warningPrice', function () {
    toastr.error('Precio por encima de precio de lista.');
});

// Checkear producto manualmente
$(document).on('click', '.checkProduct', function () {
    $('.cardCalcPriceObjectives').show(800);
    $('html, body').animate({ scrollTop: 0 }, 1000);

    // Obtener el ID del elemento 
    let id = $(this).attr('id');
    $('.calcPrice').val('');

    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

    if (id.includes('all')) {
        let check = 0;

        if ($(`#${id}`).is(':checked')) {
            check = 1;
            $('.checkProduct').prop('checked', true);
        } else {
            check = 0;
            $('.checkProduct').prop('checked', false);
        }

        dataProducts = dataProducts.map((item) => ({ ...item, check: check }));
    } else {
        // Obtener la parte después del guion '-'
        let id_product = id.split('-')[1];
        let statusInputs = true;
        let check = 0;

        if ($(`#${id}`).is(':checked')) check = 1;

        for (let i = 0; i < dataProducts.length; i++) {
            if (id_product == dataProducts[i].id_product) {
                dataProducts[i].check = check;
                break;
            }
        }

        let arr = dataProducts.find(item => item.id_product == id_product);
        let checks = dataProducts.filter(item => item.check == 1 && item.id_product != id_product);

        for (let i = 0; i < checks.length; i++) {
            if (
                checks[i].profitability_1 != arr.profitability_1 ||
                checks[i].profitability_2 != arr.profitability_2 ||
                checks[i].profitability_3 != arr.profitability_3 ||
                checks[i].unit_1 != arr.unit_1 ||
                checks[i].unit_2 != arr.unit_2 ||
                checks[i].unit_3 != arr.unit_3
            ) {
                statusInputs = false;
                break;
            }
        }

        if (statusInputs == true) {
            $('#unity-1').val(arr.unit_1);
            $('#unity-2').val(arr.unit_2);
            $('#unity-3').val(arr.unit_3);
            $('#profitability1').val(arr.profitability_1);
            $('#profitability2').val(arr.profitability_2);
            $('#profitability3').val(arr.profitability_3);
        }
    }

    sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));
});
