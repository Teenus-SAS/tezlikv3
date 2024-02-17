$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardExpensesGenerals`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        generalIndicators(
          data.expense_value,
          data.expense_recover,
          data.multiproducts
        );
        averagePrices(data.details_prices);
        generalSales(data.details_prices);
        if (cost_multiproduct == 1 && plan_cost_multiproduct == 1)
          graphicMultiproducts(data.multiproducts);
        graphicTimeProcessByProduct(data.time_process);
        averagesTime(data.average_time_process);
        graphicsFactoryLoad(data.factory_load_minute_value);
        graphicWorkforce(data.process_minute_value);
        graphicGeneralCost(data.expense_value);

        if (flag_expense === '1' && flag_expense_distribution === '1') {
          typePrice = '2';
          document.getElementById("actual").className =
            "btn btn-sm btn-primary typePrice";
          document.getElementById("sugered").className =
            "btn btn-sm btn-outline-primary typePrice";

          $(".productTitle").html("Productos con mayor rentabilidad (Actual)");
          graphicProductActualCost(data.details_prices);
        }
        else
          graphicProductCost(data.details_prices);

        generalMaterials(data.quantity_materials);
  
        dataPucExpenes = data.expenses;
  
        dataExpenses = data.expense_value;
        dataDetailsPrices = data.details_prices;
      });
  }, 1000);

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

    if (flag_expense == 1 || flag_expense == 0) {
      for (i = 0; i < data.length; i++) {
        totalExpense = totalExpense + data[i].expenseCount;
      }
      totalExpense = `$ ${totalExpense.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })}`;
      expenses = 'Gastos Generales';
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
    $('#expenses').html(expenses);
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
        if (isFinite(dataCost.actualProfitability)) {
          contProfitability += 1;
          price2 += dataCost.price2;
          actualProfitability += dataCost.actualProfitability;
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

      cardActualProfitability.insertAdjacentHTML('afterbegin',
      `<div class="card ${averageActualProfitability < 0 ? 'bg-danger':'bg-success'}">
        <a class="card-body" id="btnActualProfitabilityAverage" href="javascript:;">
          <div class="media text-white">
            <div class="media-body">
              <span class="text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
              <h2 class="mb-0 mt-1 text-white">${averageActualProfitability.toLocaleString('es-CO', {maximumFractionDigits: 2})} %</h2>
            </div>
            <div class="align-self-center mt-1">
              <i class="bx bx-line-chart fs-xl"></i>
            </div>
          </div>
        </a>
      </div>`);


      $('#totalCostED').html(
        `$ ${price2.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}`
      );

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

    data.forEach(item => {
      units_sold += item.units_sold;
      turnover += item.turnover;
    });

    $('#productsSold').html(units_sold.toLocaleString('es-CO'));
    $('#salesRevenue').html(`$ ${turnover.toLocaleString('es-CO')}`);
  };  
});
