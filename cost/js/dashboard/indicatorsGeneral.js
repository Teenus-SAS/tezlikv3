fetch(`/api/dashboardExpensesGenerals`)
  .then((response) => response.text())
  .then((data) => {
    data = JSON.parse(data);
    generalIndicators(data.expense_value);
    averagePrices(data.details_prices);
    generalSales(data.details_prices);
    graphicTimeProcessByProduct(data.time_process);
    averagesTime(data.average_time_process);
    graphicsFactoryLoad(data.factory_load_minute_value);
    graphicWorkforce(data.process_minute_value);
    graphicGeneralCost(data.expense_value);
    graphicProductCost(data.details_prices);
    generalMaterials(data.quantity_materials);
    //graphicProfit(data)
  });

/* Colors */
dynamicColors = () => {
  let letters = '0123456789ABCDEF'.split('');
  let color = '#';

  for (var i = 0; i < 6; i++) color += letters[Math.floor(Math.random() * 16)];
  return color;
};

getRandomColor = (a) => {
  let color = [];
  for (i = 0; i < a; i++) color.push(dynamicColors());
  return color;
};

/* Cantidad de materias primas */
generalMaterials = (data) => {
  $('#materials').html(data.materials.toLocaleString('es-ES'));
};

/* Indicadores Generales */
generalIndicators = (data) => {
  // Cantidad de productos
  $('#products').html(data.products.toLocaleString('es-ES'));

  /* Gastos generales */
  totalExpense = 0;
  for (i = 0; i < 3; i++) {
    totalExpense = totalExpense + data[i].expenseCount;
  }
  $('#generalCost').html(`$ ${totalExpense.toLocaleString('es-ES')}`);
};

/* Promedio rentabilidad y comision */
averagePrices = (data) => {
  profitability = 0;
  commissionSale = 0;

  if (data.length > 0) {
    for (let i in data) {
      profitability = profitability + data[i].profitability;
      commissionSale = commissionSale + data[i].commission_sale;
    }

    averageprofitability = profitability / data.length;
    averagecommissionSale = commissionSale / data.length;

    $('#profitabilityAverage').html(`${averageprofitability.toFixed(2)} %`);
    $('#comissionAverage').html(`${averagecommissionSale.toFixed(2)} %`);
  } else {
    $('#profitabilityAverage').html(`0 %`);
    $('#comissionAverage').html(`0 %`);
  }
};

/* Tiempos promedio */
averagesTime = (data) => {
  enlistmentTime = 0;
  operationTime = 0;

  if (data.length > 0) {
    for (let i in data) {
      enlistmentTime = enlistmentTime + data[i].enlistment_time;
      operationTime = operationTime + data[i].operation_time;
    }

    averageEnlistment = enlistmentTime / data.length;
    averageOperation = operationTime / data.length;
    averageTotal = averageEnlistment + averageOperation;

    //Formato Alistamiento (. miles , 2 decimales)
    averageEnlistment = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(averageEnlistment);

    //Formato Operación (. miles , 2 decimales)
    averageOperation = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(averageOperation);

    //Formato Tiempo Total Prom (. miles , 2 decimales)
    averageTotal = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(averageTotal);

    $('#enlistmentTime').html(`${averageEnlistment} min`);
    $('#operationTime').html(`${averageOperation} min`);
    $('#averageTotalTime').html(`${averageTotal} min`);
  } else {
    $('#enlistmentTime').html(`0 min`);
    $('#operationTime').html(`0 min`);
  }
};

/* Ventas generales */
generalSales = (data) => {
  unitsSold = 0;
  turnover = 0;

  if (data.length > 0) {
    for (let i in data) {
      unitsSold = unitsSold + data[i].units_sold;
      turnover = turnover + data[i].turnover;
    }

    $('#productsSold').html(unitsSold.toLocaleString('es-ES'));
    $('#salesRevenue').html(`$ ${turnover.toLocaleString('es-ES')}`);
  } else {
    $('#productsSold').html('0');
    $('#salesRevenue').html(`$ 0`);
  }
};
