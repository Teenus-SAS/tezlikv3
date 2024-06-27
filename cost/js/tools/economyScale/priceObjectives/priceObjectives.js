$(document).ready(function () {
    let real_price = 100;
    let cant = 1;
    let dataPO = [];

    // Cuando se ingrese rentabilidad general
    $('#calcPriceObj').click(function (e) {
        e.preventDefault();

        let profitability = parseFloat($('#profitability').val());
        let unit1 = parseFloat($('#unity-1').val());
        let unit2 = parseFloat($('#unity-2').val());
        let unit3 = parseFloat($('#unity-3').val());

        isNaN(profitability) ? profitability = 0 : profitability;
        isNaN(unit1) ? unit1 = 0 : unit1;
        isNaN(unit2) ? unit2 = 0 : unit2;
        isNaN(unit3) ? unit3 = 0 : unit3;

        let data = profitability * unit1 * unit2 * unit3;

        if (data <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }
 
        dataPO = [];
        $('.cardBottons').hide();

        let form = document.getElementById('spinnerLoading');
        $('#spinnerLoading').empty();

        form.insertAdjacentHTML(
            'beforeend',
            `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
                <div class="spinner-border text-secondary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>`
        );

        generalCalc(profitability, [unit1, unit2, unit3]);
    });

    // Calculo general economia de escala para obtener unidades
    /* */ const generalCalc = async (profitability, units) => {
        try {
            let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
            let allEconomyScale = JSON.parse(sessionStorage.getItem('allEconomyScale'));

            // Limpiar data 
            // dataProducts = dataProducts.map(item => ({ ...item, price_1: false, price_2: false, price_3: false, }));
            // await loadTblProducts(dataProducts); 
            cant = 1;

            // Definir una función asíncrona para manejar cada iteración del ciclo
            const handleIteration = async (arr, c_unit) => {
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

            
            // Iterar sobre cada índice 
            for (let i = 0; i < dataProducts.length; i++) { 
                for (let j = 0; j < units.length; j++) {
                    let product_price = await handleIteration(dataProducts[i], units[j]); 

                    if (product_price === -1) {
                        j = j - 1;
                    } else { 
                        dataProducts[i].profitability = profitability;
                        dataProducts[i][`unit_${j + 1}`] = units[j];
                        dataProducts[i][`price_${j + 1}`] = product_price;
                        cant = 1;
                        real_price = 100;
                        startTime = performance.now();
                    } 
                }

                dataPO.push(dataProducts[i]);
                loadTblProducts(dataPO);
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
            url: "/api/savePriceObjectives",
            data: { products: data },
            success: function (resp) {
                dataPO = [];
                // console.log(resp);
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
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
            let unit_1 = dataProducts[0].unit_1;
            let unit_2 = dataProducts[0].unit_2;
            let unit_3 = dataProducts[0].unit_3;

            for (i = 0; i < dataProducts.length; i++) {
                data.push({
                    referencia: dataProducts[i].reference,
                    producto: dataProducts[i].product,
                    precio_lista: dataProducts[i].sale_price,
                    [unit_1]: `${parseFloat(dataProducts[i].price_1) > parseFloat(dataProducts[i].sale_price) ? '' : parseFloat(dataProducts[i].price_1)}`,
                    [unit_2]: `${parseFloat(dataProducts[i].price_2) > parseFloat(dataProducts[i].sale_price) ? '' : parseFloat(dataProducts[i].price_2)}`,
                    [unit_3]: `${parseFloat(dataProducts[i].price_3) > parseFloat(dataProducts[i].sale_price) ? '' : parseFloat(dataProducts[i].price_3)}`,
                });
            }

            let ws = XLSX.utils.json_to_sheet(data, {
                header: ['referencia', 'producto', 'precio_lista', unit_1, unit_2, unit_3]
            });

            XLSX.utils.book_append_sheet(wb, ws, 'Productos');
        }
        XLSX.writeFile(wb, 'Objetivos_Precios.xlsx');
        
    });

    $(document).on('click', '.warningPrice', function () {
        toastr.error('Precio por encima de precio de lista.');
    });
});