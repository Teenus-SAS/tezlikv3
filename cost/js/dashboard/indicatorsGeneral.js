$(document).ready(function () {
  loadAllData = async () => {
    try {
      let indirect = sessionStorage.getItem('indirect');
      const [data, indirectCost] = await Promise.all([
        searchData('/api/dashboardExpensesGenerals'),
        !indirect ? searchData('/api/calcAllIndirectCost') : '',
      ]);

      sessionStorage.setItem('indirect', 1);

      // Validar el tipo de moneda y convertir si es necesario
      let typeCurrency = '1';
      if (flag_currency_usd == '1' || flag_currency_eur == '1') {
        typeCurrency = sessionStorage.getItem('typeCurrency');
      }

      const convertCurrency = (data, rate, priceKey) => {
        data.details_prices.forEach(item => {
          item.cost_workforce /= rate;
          item.cost_materials /= rate;
          item.cost_indirect_cost /= rate;
          item.price = item[priceKey];
          item.sale_price = item[`sale_${priceKey}`];
          item.turnover /= rate;
          item.assignable_expense /= rate;
          item.services /= rate;
        });

        data.expense_value.forEach(item => {
          item.expenseCount /= rate;
        });

        data.expenses.forEach(item => {
          item.expense_value /= rate;
        });

        data.factory_load_minute_value.forEach(item => {
          item.totalCostMinute /= rate;
        });

        data.process_minute_value.forEach(item => {
          item.minute_value /= rate;
        });
      };

      if (typeCurrency === '2') { // Dolares
        convertCurrency(data, parseFloat(coverage_usd), 'price_usd');
      } else if (typeCurrency === '3') { // Euros
        convertCurrency(data, parseFloat(coverage_eur), 'price_eur');
      }

      generalIndicators(
        data.expense_value,
        data.expense_recover,
        data.multiproducts
      );
      averagePrices(data.details_prices);
      generalSales(data.details_prices);

      // Si el acceso de multiproductos está activo cargar gráfica
      if (cost_multiproduct == 1 && plan_cost_multiproduct == 1) {
        graphicMultiproducts(data.multiproducts);
      }

      graphicTimeProcessByProduct(data.time_process);
      averagesTime(data.average_time_process);
      graphicsFactoryLoad(data.factory_load_minute_value);
      graphicWorkforce(data.process_minute_value);
      graphicGeneralCost(data.expense_value);

      // Validar si el acceso de distribución está activo y está por producto
      if (flag_expense === '1' && flag_expense_distribution === '1') {
        // Recargar gráfico de productos con mayor rentabilidad con el precio actual
        typePrice = '2';
        document.getElementById("actual").className =
          "btn btn-sm btn-primary typePrice";
        document.getElementById("sugered").className =
          "btn btn-sm btn-outline-primary typePrice";

        $(".productTitle").html("Productos con mayor rentabilidad (Actual)");
        graphicProductActualCost(data.details_prices);
      } else {
        graphicProductCost(data.details_prices);
      }

      generalMaterials(data.quantity_materials);

      // Dejar variables array globales para llamarlos después  
      dataPucExpenes = data.expenses;
      dataExpenses = data.expense_value;
      dataDetailsPrices = data.details_prices;
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  // loadAllData = async () => {
  //   try {
  //     let indirect = sessionStorage.getItem('indirect');
  //     const [data, indirectCost] = await Promise.all([
  //       searchData('/api/dashboardExpensesGenerals'),
  //       !indirect ? searchData('/api/calcAllIndirectCost') : '',
  //     ]);
 
  //     sessionStorage.setItem('indirect', 1);

  //     // Validar que el tipo de valor del precio esta en dolares o pesos, si no tiene ningun acceso de cambio de moneda activo queda por defecto COP
  //     let typeCurrency = '1';
    
  //     if (flag_currency_usd == '1' || flag_currency_eur == '1')
  //       typeCurrency = sessionStorage.getItem('typeCurrency');

  //     switch (typeCurrency) {
  //       case '2':// Dolares
  //         // Convertir costos a dolares de acuerdo al dolar de cobertura
  //         for (let i = 0; i < data.details_prices.length; i++) {
  //           data.details_prices[i].cost_workforce = parseFloat(data.details_prices[i].cost_workforce) / parseFloat(coverage_usd);
  //           data.details_prices[i].cost_materials = parseFloat(data.details_prices[i].cost_materials) / parseFloat(coverage_usd);
  //           data.details_prices[i].cost_indirect_cost = parseFloat(data.details_prices[i].cost_indirect_cost) / parseFloat(coverage_usd);
  //           data.details_prices[i].price = data.details_prices[i].price_usd;
  //           data.details_prices[i].sale_price = data.details_prices[i].sale_price_usd;
  //           data.details_prices[i].turnover = parseFloat(data.details_prices[i].turnover) / parseFloat(coverage_usd);
  //           data.details_prices[i].assignable_expense = parseFloat(data.details_prices[i].assignable_expense) / parseFloat(coverage_usd);
  //           data.details_prices[i].services = parseFloat(data.details_prices[i].services) / parseFloat(coverage_usd);
  //         }

  //         for (let i = 0; i < data.expense_value.length; i++) {
  //           data.expense_value[i].expenseCount = parseFloat(data.expense_value[i].expenseCount) / parseFloat(coverage_usd);
  //         }
          
  //         for (let i = 0; i < data.expenses.length; i++) {
  //           data.expenses[i].expense_value = parseFloat(data.expenses[i].expense_value) / parseFloat(coverage_usd);
  //         }
          
  //         for (let i = 0; i < data.factory_load_minute_value.length; i++) {
  //           data.factory_load_minute_value[i].totalCostMinute = parseFloat(data.factory_load_minute_value[i].totalCostMinute) / parseFloat(coverage_usd);
  //         }

  //         for (let i = 0; i < data.process_minute_value.length; i++) {
  //           data.process_minute_value[i].minute_value = parseFloat(data.process_minute_value[i].minute_value) / parseFloat(coverage_usd);
  //         }
  //         break;
  //       case '3':// Euros
  //         // Convertir costos a euros de acuerdo al euro de cobertura
  //         for (let i = 0; i < data.details_prices.length; i++) {
  //           data.details_prices[i].cost_workforce = parseFloat(data.details_prices[i].cost_workforce) / parseFloat(coverage_eur);
  //           data.details_prices[i].cost_materials = parseFloat(data.details_prices[i].cost_materials) / parseFloat(coverage_eur);
  //           data.details_prices[i].cost_indirect_cost = parseFloat(data.details_prices[i].cost_indirect_cost) / parseFloat(coverage_eur);
  //           data.details_prices[i].price = data.details_prices[i].price_eur;
  //           data.details_prices[i].sale_price = data.details_prices[i].sale_price_eur;
  //           data.details_prices[i].turnover = parseFloat(data.details_prices[i].turnover) / parseFloat(coverage_eur);
  //           data.details_prices[i].assignable_expense = parseFloat(data.details_prices[i].assignable_expense) / parseFloat(coverage_eur);
  //           data.details_prices[i].services = parseFloat(data.details_prices[i].services) / parseFloat(coverage_eur);
  //         }

  //         for (let i = 0; i < data.expense_value.length; i++) {
  //           data.expense_value[i].expenseCount = parseFloat(data.expense_value[i].expenseCount) / parseFloat(coverage_eur);
  //         }
          
  //         for (let i = 0; i < data.expenses.length; i++) {
  //           data.expenses[i].expense_value = parseFloat(data.expenses[i].expense_value) / parseFloat(coverage_eur);
  //         }
          
  //         for (let i = 0; i < data.factory_load_minute_value.length; i++) {
  //           data.factory_load_minute_value[i].totalCostMinute = parseFloat(data.factory_load_minute_value[i].totalCostMinute) / parseFloat(coverage_eur);
  //         }

  //         for (let i = 0; i < data.process_minute_value.length; i++) {
  //           data.process_minute_value[i].minute_value = parseFloat(data.process_minute_value[i].minute_value) / parseFloat(coverage_eur);
  //         }
  //         break;
      
  //       default:
  //         break;
  //     }

  //     generalIndicators(
  //       data.expense_value,
  //       data.expense_recover,
  //       data.multiproducts
  //     );
  //     averagePrices(data.details_prices);
  //     generalSales(data.details_prices);

  //     // Si el accesos de multiproductos esta activo cargar grafica
  //     if (cost_multiproduct == 1 && plan_cost_multiproduct == 1)
  //       graphicMultiproducts(data.multiproducts);

  //     graphicTimeProcessByProduct(data.time_process);
  //     averagesTime(data.average_time_process);
  //     graphicsFactoryLoad(data.factory_load_minute_value);
  //     graphicWorkforce(data.process_minute_value);
  //     graphicGeneralCost(data.expense_value);

  //     // Validar si el acceso de distribucion esta activo y esta por producto
  //     if (flag_expense === '1' && flag_expense_distribution === '1') {
  //       // Recargar grafico de productos con mayor rentabilidad con el precio actual
  //       typePrice = '2';
  //       document.getElementById("actual").className =
  //         "btn btn-sm btn-primary typePrice";
  //       document.getElementById("sugered").className =
  //         "btn btn-sm btn-outline-primary typePrice";

  //       $(".productTitle").html("Productos con mayor rentabilidad (Actual)");
  //       graphicProductActualCost(data.details_prices);
  //     }
  //     else //
  //       graphicProductCost(data.details_prices);

  //     generalMaterials(data.quantity_materials);

  //     // Dejar variables array globales para llamarlos despues  
  //     dataPucExpenes = data.expenses;
  //     dataExpenses = data.expense_value;
  //     dataDetailsPrices = data.details_prices;
  //   } catch (error) {
  //     console.error('Error loading data:', error);
  //   }
  // };
  /* Colors */
  dynamicColors = () => {
    let letters = '0123456789ABCDEF'.split('');
    let color = '#';

    for (var i = 0; i < 6; i++)
      color += letters[Math.floor(Math.random() * 16)];
    return color;
  };

  getRandomColor = (a) => {
    let color = [];
    for (i = 0; i < a; i++) color.push(dynamicColors());
    return color;
  };

  /* Cantidad de materias primas */
  generalMaterials = (data) => {
    $('#materials').html(data.materials.toLocaleString('es-CO'));
  };

  /* Indicadores Generales */
  generalIndicators = (data, expenseRecover, multiproducts) => {
    // Cantidad de productos
    $('#products').html(data[0].products.toLocaleString('es-CO'));

    isNaN(multiproducts.total_units) ? total_units = 0 : total_units = multiproducts.total_units;

    $('#multiproducts').html(
      total_units.toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })
    );

    /* Gastos generales */
    let totalExpense = 0;

    if (flag_expense === '1' || flag_expense === '0') {
      for (i = 0; i < data.length; i++) {
        totalExpense = totalExpense + data[i].expenseCount;
      }
      totalExpense = `$ ${totalExpense.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })}`;
      let typeCurrency = sessionStorage.getItem('typeCurrency');

      // price_usd == '0' || 
      typeCurrency == '1' || !typeCurrency || flag_currency_usd == '0' || flag_currency_eur == '0' ?
        expenses = 'Gastos Generales' :
        typeCurrency == '2' ?
          expenses = 'Gastos Generales (USD)' :
          expenses = 'Gastos Generales (EUR)';
      
    } else {
      expenses = `Gtos Generales`;
      totalExpense = `${expenseRecover.percentageExpense.toLocaleString(
        'es-CO',
        {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        }
      )} %`;
    }
    $('#expenses1').html(expenses);
    $('#generalCost').html(totalExpense);
  };

  /* Promedio rentabilidad y comision */
  averagePrices = (data) => {
    let profitability = 0;
    let commissionSale = 0;
    let actualProfitability = 0;
    let price2 = 0;
    let total_units = 0;
    let turnover = 0;
    let contProfitability = 0;

    data = data.filter((item) => item.profitability > 0);
    
    if (flag_expense === '1' && flag_expense_distribution === '1')
      data = data.filter((item) => item.units_sold != 0 && item.turnover != 0);

    if (data.length > 0) {
      for (let i in data) {
        profitability = profitability + data[i].profitability;
        commissionSale = commissionSale + data[i].commission_sale;
        total_units += data[i].units_sold;
        turnover += data[i].turnover;

        let dataCost = getDataCost(data[i]);
        if (isFinite(dataCost.actualProfitability2)) {
          contProfitability += 1;
          price2 += dataCost.price2;
          actualProfitability += dataCost.actualProfitability2;
        }
      }       
      let averageprofitability = profitability / data.length;
      let averagecommissionSale = commissionSale / data.length;

      if (flag_expense === '2' || flag_expense_distribution === '2' ) 
        averageActualProfitability = actualProfitability / contProfitability;
      // let averageActualProfitability = (actualProfitability / total_units) * 100;
      else
        averageActualProfitability = ((turnover - price2) / price2) * 100;

      isNaN(averageActualProfitability) ? averageActualProfitability = 0 : averageActualProfitability; 

      let cardActualProfitability = document.getElementsByClassName('cardActualProfitability')[0];

      $('.cardActualProfitability').empty();

      cardActualProfitability.insertAdjacentHTML('beforeend',
      `<div class="card btnActualProfitabilityAverage ${averageActualProfitability < 0 ? 'bg-danger':'bg-success'}">
        <a class="card-body" id="btnActualProfitabilityAverage" href="javascript:;">
          <div class="media text-white">
            <div class="media-body">
              <span class="text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentabilidad'} (Real)</span>
              <h2 class="mb-0 mt-1 text-white">${averageActualProfitability.toLocaleString('es-CO', {maximumFractionDigits: 2})} %</h2>
            </div>
            <div class="align-self-center mt-1">
              <i class="bx bx-line-chart fs-xl"></i>
            </div>
          </div>
        </a>
      </div>`);

      let typeCurrency = '1';
    
      if(flag_currency_usd == '1' || flag_currency_eur == '1')
        typeCurrency = sessionStorage.getItem('typeCurrency');
      
      switch (typeCurrency) {
        case '2': // Dolares
          price2 = `$ ${price2.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (USD)`;
          break;
        case '3': // Euros
          price2 = `$ ${price2.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (EUR)`;
          break;
        default: // Pesos COP
          price2 = `$ ${price2.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`; 
          break;
      }
      // price_usd == '1' && 
      // if (typeCurrency == '2' && flag_currency_usd == '1') {
      //   price2 = `$ ${price2.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (USD)`;
      // } else {
      //   price2 = `$ ${price2.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`;
      // }

      $('#totalCostED').html(price2);

      $('#minProfitabilityAverage').html(
        `${averageprofitability.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} %`
      );
      $('#comissionAverage').html(
        `${averagecommissionSale.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} %`
      );
    } else {
      $('#profitabilityAverage').html(`0 %`);
      $('#minProfitabilityAverage').html(`0 %`);
      $('#comissionAverage').html(`0 %`);
    }
  };

  /* Tiempos promedio */
  averagesTime = (data) => {
    let enlistmentTime = 0;
    let operationTime = 0;

    if (data.length > 0) {
      for (let i in data) {
        enlistmentTime = enlistmentTime + data[i].enlistment_time;
        operationTime = operationTime + data[i].operation_time;
      }

      let averageEnlistment = enlistmentTime / data.length;
      let averageOperation = operationTime / data.length;
      let averageTotal = averageEnlistment + averageOperation;

      $('#enlistmentTime').html(
        `${averageEnlistment.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} min`
      );
      $('#operationTime').html(
        `${averageOperation.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} min`
      );
      $('#averageTotalTime').html(
        `${averageTotal.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} min`
      );
    } else {
      $('#enlistmentTime').html(`0 min`);
      $('#operationTime').html(`0 min`);
    }
  };

  /* Ventas generales */
  generalSales = (data) => {
    let units_sold = 0;
    let turnover = 0;
    let sale_price = 0;

    data.forEach(item => {
      units_sold += item.units_sold;
      turnover += item.turnover;
      sale_price += item.sale_price;
    });

    if (sale_price === 0)
      $('.btnActualProfitabilityAverage').hide();

    let typeCurrency = '1';

    if(flag_currency_usd == '1' || flag_currency_eur == '1')
      typeCurrency = sessionStorage.getItem('typeCurrency');

    switch (typeCurrency) {
      case '2': // Dolares
        turnover = `$ ${turnover.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (USD)`;
        break;
      case '3': // Euros
        turnover = `$ ${turnover.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (EUR)`;
        break;
      default:// Pesos COP
        turnover = `$ ${turnover.toLocaleString('es-CO')}`;
        break;
    }
    // price_usd == '1' && 
    // if (typeCurrency == '2' && flag_currency_usd == '1') {
    //   turnover = `$ ${turnover.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (USD)`;
    // } else {
    //   turnover = `$ ${turnover.toLocaleString('es-CO')}`;      
    // }    

    $('#productsSold').html(units_sold.toLocaleString('es-CO'));
    $('#salesRevenue').html(turnover);
  };  

  loadAllData();
});
