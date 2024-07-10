$(document).ready(function () {
    let unit = 1;
    let cant = 1; 
    let dataSO = [];

    // Seleccionar tipo de gasto
    let typeExpense = '1';

    if (anual_expense == '1' && flag_expense_anual == '1') 
        typeExpense = sessionStorage.getItem('selectTypeExpense');
    
    if(typeExpense)
        $('#selectTypeExpense').val(typeExpense);

    $('#selectTypeExpense').change(function (e) { 
        e.preventDefault();

        let type = this.value;
        sessionStorage.setItem('selectTypeExpense', type);

        let profitability = $('#profitability').val();

        if (profitability) {
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

            generalCalc(parseFloat(profitability));
        }
    });

    // Cuando se ingrese rentabilidad general
    $(document).on('blur', '#profitability', function () {
        if (this.value == '' || !this.value) {
            return false;
        }

        dataSO = [];
 
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

        generalCalc(parseFloat(this.value));
    });

    // Calculo general economia de escala para obtener unidades
    const generalCalc = async (profitability) => {
        try {
            let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
            let allEconomyScale = JSON.parse(sessionStorage.getItem('allEconomyScale'));

            let typeExpense = '1';

            if (anual_expense == '1' && flag_expense_anual == '1')
                typeExpense = sessionStorage.getItem('selectTypeExpense');
 
            unit = 1;
            cant = 1;

            // Definir una función asíncrona para manejar cada iteración del ciclo
            const handleIteration = async (arr) => {
                // Costos Variables
                let economyScale = allEconomyScale.find(item => item.id_product == arr.id_product);

                let totalVariableCost = economyScale.variableCost * unit;

                let costFixed = economyScale.costFixed;
                let real_price = parseFloat(arr.real_price);

                typeExpense == '2' ? costFixed = economyScale.costFixedAnual : costFixed;
                typeExpense == '2' ? real_price = parseFloat(arr.real_price_anual) : real_price;

                if (real_price <= 0) {
                    if (arr.sale_price <= 0)
                        real_price = arr.price;
                    else
                        real_price = arr.sale_price;
                };

                // Total Costos y Gastos
                let totalCostsAndExpense = costFixed + totalVariableCost;
 
                // Calculo Total Ingresos
                let totalRevenue = unit * real_price;

                // Calculo Costo x Unidad
                let unityCost = parseFloat(totalCostsAndExpense) / unit;

                // Calculo Utilidad x Unidad
                let unitUtility = real_price - unityCost;

                // Calculo Utilidad Neta
                let netUtility = unitUtility * unit;

                // Porcentaje
                let percentage = (netUtility / totalRevenue) * 100;

                if (profitability <= percentage)
                    return unit;
                
                percentage > 0 ? cant += 2 : cant = 1;

                let division = Math.ceil((totalCostsAndExpense / real_price) + cant);

                if (division > 10000000) {
                    return { unit: unit };
                } 

                var endTime = performance.now();
                var mSeconds = endTime - startTime;
                var seconds = mSeconds / 1000;

                if (seconds > 5) {
                    return { unit: unit };
                } else {
                    await new Promise(resolve => setTimeout(resolve, 0)); 
                    unit = division;
                    return 0;
                }
                // } 
            };

            var startTime = performance.now();

            // Iterar sobre cada índice 
            for (let i = 0; i < dataProducts.length; i++) {
                let product_unit = await handleIteration(dataProducts[i]);

                dataProducts[i].profitability = profitability;

                if (typeof product_unit === 'object' && !Array.isArray(product_unit)) {
                    typeExpense == '2' ? product_unit.unit = parseInt(product_unit.unit) / 12 : product_unit.unit;                     
                    dataProducts[i].unit_sold = product_unit.unit;
                    dataProducts[i].error = 'true';

                    let dataCPts = JSON.parse(JSON.stringify(dataProducts));
                
                    if (flag_currency_usd == '1' || flag_currency_eur == '1') {
                        let arr = await setCurrency([dataCPts[i]]);
                        dataCPts[i] = arr[0];
                    }

                    dataSO.push(dataCPts[i]);
                    loadTblProducts(dataSO, 1);

                    cant = 1;
                    unit = 1;
                    startTime = performance.now(); 
                } else {

                    if (product_unit == 0) {
                        i = i - 1;
                    } else {
                        typeExpense == '2' ? product_unit.unit = parseInt(product_unit.unit) / 12 : product_unit.unit;
                        dataProducts[i].unit_sold = product_unit;
                        dataProducts[i].error = 'false';

                        let dataCPts = JSON.parse(JSON.stringify(dataProducts));
                
                        if (flag_currency_usd == '1' || flag_currency_eur == '1') {
                            let arr = await setCurrency([dataCPts[i]]);
                            dataCPts[i] = arr[0];
                        }

                        dataSO.push(dataCPts[i]);
                        loadTblProducts(dataSO, 1);

                        cant = 1;
                        unit = 1;
                        startTime = performance.now();
                    }
                }
            }
 
            sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));
            saveSaleObjectives(dataProducts);
        } catch (error) {
            console.log(error);
        }
    }; 

    // Guardar datos objetivos de ventas
    const saveSaleObjectives = (data) => {
        $.ajax({
            type: "POST",
            url: "/api/saveSaleObjectives",
            data: { products: data },
            success: function (resp) {
                dataSO = [];
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
            }
        });
    };

    $('#btnExportSObjectives').click(function (e) {
        e.preventDefault();

        let wb = XLSX.utils.book_new();
        let data = [];

        /* Productos */
        let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts')); 

        if (dataProducts.length > 0) { 
            for (i = 0; i < dataProducts.length; i++) {
                let percentage = (parseInt(dataProducts[i].units_sold) / parseInt(dataProducts[i].unit_sold)) * 100;
                isNaN(percentage) || !isFinite(percentage) ? percentage = 0 : percentage;
                
                data.push({
                    referencia: dataProducts[i].reference,
                    producto: dataProducts[i].product,
                    unidades_vendidas: parseFloat(dataProducts[i].units_sold),
                    unidades: `${isNaN(parseFloat(dataProducts[i].unit_sold)) ? 0 : parseFloat(dataProducts[i].unit_sold)}`,
                    cumplimiento: percentage, 
                });
            }

            let ws = XLSX.utils.json_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, 'Productos');
        }
        XLSX.writeFile(wb, 'Objetivos_Ventas.xlsx');
        
    }); 

    $(document).on('click', '.warningUnit', function () {
        toastr.error('Precios muy por debajo de lo requerido. Si se sigue calculando automáticamente generará números demasiado grandes');
    });
});