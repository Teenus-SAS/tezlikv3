$(document).ready(function () {
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
      graphicMultiproducts(data.multiproducts);
      graphicTimeProcessByProduct(data.time_process);
      averagesTime(data.average_time_process);
      graphicsFactoryLoad(data.factory_load_minute_value);
      graphicWorkforce(data.process_minute_value);
      graphicGeneralCost(data.expense_value);
      graphicProductCost(data.details_prices);
      generalMaterials(data.quantity_materials);

      dataPucExpenes = data.expenses;

      dataExpenses = data.expense_value;
      dataDetailsPrices = data.details_prices;
    });

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
    $('#multiproducts').html(
      multiproducts.total_units.toLocaleString('es-CO', {
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
    let contProfitability = 0;
    
    if (data.length > 0) {
      for (let i in data) {
        profitability = profitability + data[i].profitability;
        commissionSale = commissionSale + data[i].commission_sale;

        let dataCost = getDataCost(data[i]);
        if (isFinite(dataCost.actualProfitability)) {
          contProfitability += 1;
          actualProfitability += dataCost.actualProfitability;
        }
      }
      
      let averageprofitability = profitability / data.length;
      let averagecommissionSale = commissionSale / data.length;
      let averageActualProfitability = actualProfitability / contProfitability;

      $('#actualProfitabilityAverage').html(
        `${averageActualProfitability.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} %`
      );
      $('#profitabilityAverage').html(
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
    $('#productsSold').html(data[0].units_sold.toLocaleString('es-CO'));
    $('#salesRevenue').html(`$ ${data[0].turnover.toLocaleString('es-CO')}`);
  };
});
