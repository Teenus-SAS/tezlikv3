getDataCost = (data) => {
  let {
    assignable_expense: expense,
    cost_materials,
    cost_workforce,
    cost_indirect_cost,
    services,
    expense_recover,
    profitability,
    commission_sale,
    turnover,
    units_sold,
    sale_price
  } = data;

  expense = parseFloat(expense);

  let cost = parseFloat(cost_materials) + parseFloat(cost_workforce) + parseFloat(cost_indirect_cost) + parseFloat(services);
  let costTotal;

  if (flag_expense === '0' || flag_expense === '1') {
    costTotal = cost + expense;
  } else if (flag_expense === '2') {
    costTotal = cost / (1 - parseFloat(expense_recover) / 100);
    expense = costTotal * (parseFloat(expense_recover) / 100);
  }

  const pPrice = costTotal / (1 - parseFloat(profitability) / 100);
  const price = pPrice / (1 - parseFloat(commission_sale) / 100);
  const costProfitability = pPrice * (parseFloat(profitability) / 100);
  const costCommissionSale = price * (parseFloat(commission_sale) / 100);
  const recomendedPrice = parseFloat(turnover) / parseFloat(units_sold);

  let profitabilityResult = 0;
  let profitability2 = 0;
  let profitability3 = 0;
  let costActualProfitability = 0;
  let price2 = 0;

  if (flag_expense === "2" || flag_expense_distribution === "2") {
    profitabilityResult = ((parseFloat(sale_price) - costTotal) / parseFloat(sale_price)) * 100;
    profitability2 = profitabilityResult;
    profitability3 = profitabilityResult;
    costActualProfitability = parseFloat(sale_price) * (profitabilityResult / 100);
  } else if ((parseFloat(units_sold) > 0 && parseFloat(turnover) > 0) || flag_expense === "1") {
    price2 = costTotal * parseFloat(units_sold);
    profitabilityResult = ((parseFloat(turnover) - price2) / price2) * 100;
    profitability3 = ((parseFloat(sale_price) - costTotal) / parseFloat(sale_price)) * 100;
    profitability2 = ((recomendedPrice - costTotal) / recomendedPrice) * 100;
    costActualProfitability = parseFloat(turnover) - price2;
  }

  const sanitize = (value) => isNaN(value) || !isFinite(value) ? 0 : value;

  return {
    cost,
    costTotal,
    actualProfitability: sanitize(profitabilityResult),
    actualProfitability2: sanitize(profitability2),
    actualProfitability3: sanitize(profitability3),
    costCommissionSale: sanitize(costCommissionSale),
    costProfitability: sanitize(costProfitability),
    costActualProfitability: sanitize(costActualProfitability),
    expense: sanitize(expense),
    recomendedPrice: sanitize(recomendedPrice),
    price: sanitize(price),
    price2: sanitize(price2),
  };
};

// Al hacer click en el porcentaje, lo convierte en input
$(document).on('click', '#expenseRecoverDisplay', function () {
  const val = $(this).data('value') || parseFloat($(this).text());
  $(this).replaceWith(`
    <input type="number" id="expenseRecoverInput" class="form-control form-control-sm" 
           style="max-width:80px; display:inline;" min="0" max="100" step="0.1" value="${val}">
  `);
  $('#expenseRecoverInput').focus();
});

// Al salir del input, guarda el valor y actualiza cálculos
$(document).on('blur', '#expenseRecoverInput', function () {
  const val = parseFloat($(this).val()).toFixed(1);
  const newSpan = `
    <span id="expenseRecoverDisplay" style="cursor:pointer; color:#007bff;" data-value="${val}">
      ${val}%
    </span>
  `;

  $(this).replaceWith(newSpan);

  // Ejecuta el recálculo
  recalculateSalesPrice(val);
});


// Al presionar Enter o salir del input, se actualiza
$(document).on('blur', '#expenseRecoverInput', updateExpenseRecover);
$(document).on('keypress', '#expenseRecoverInput', function (e) {
  if (e.which == 13) { // Enter
    updateExpenseRecover();
  }
});

function updateExpenseRecover() {
  const val = parseFloat($('#expenseRecoverInput').val()).toFixed(1);

  $('#expenseRecoverInput').replaceWith(`
    <span id="expenseRecoverDisplay" style="cursor:pointer; color:#007bff;">
      ${val}%
    </span>
  `);

  data[0].expense_recover = parseFloat(val);

  recalculateSalesPrice();
}

function recalculateSalesPrice(expenseRecoverValue = null) {
  const payRawMaterial = getValue('#payRawMaterial');
  const payWorkforce = getValue('#payWorkforce');
  const payIndirectCost = getValue('#payIndirectCost');
  const services = getValue('#services');

  const baseCost = payRawMaterial + payWorkforce + payIndirectCost + services;

  const expensePercentage = parseFloat(expenseRecoverValue ?? $('#expenseRecoverDisplay').data('value') ?? 0);
  const commissionPercentage = 6;
  const profitPercentage = 12;

  const expensesValue = baseCost * (expensePercentage / 100);
  $('#payAssignableExpenses').text(formatCOP(expensesValue));

  const costTotal = baseCost + expensesValue;
  $('#costTotal').text(formatCOP(costTotal));

  const commission = costTotal * (commissionPercentage / 100);
  $('#commisionSale').text(formatCOP(commission));

  const profit = costTotal * (profitPercentage / 100);
  $('#profitability').text(formatCOP(profit));

  const salesPrice = costTotal + commission + profit;
  $('#salesPrice').text(formatCOP(salesPrice));
}


function getValue(selector) {
  return parseFloat($(selector).text().replace(/\$|\./g, '').replace(',', '.')) || 0;
}

function formatCOP(value) {
  return `$ ${Math.round(value).toLocaleString('es-CO')}`;
}

