
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

// Mostrar input editable para GASTOS
$(document).on('click', '#expenseRecoverDisplay', function () {
  const val = $(this).data('value') || parseFloat($(this).text());
  $(this).replaceWith(`
    <input type="number" id="expenseRecoverInput" class="form-control form-control-sm" 
           style="max-width:80px; display:inline;" min="0" max="100" step="0.1" value="${val}">
  `);
  $('#expenseRecoverInput').focus();
});

// Guardar cambios al salir del input de GASTOS
$(document).on('blur', '#expenseRecoverInput', updateExpenseRecover);
$(document).on('keypress', '#expenseRecoverInput', function (e) {
  if (e.which == 13) updateExpenseRecover();
});

function updateExpenseRecover() {
  const val = parseFloat($('#expenseRecoverInput').val()).toFixed(1);
  $('#expenseRecoverInput').replaceWith(`
    <span id="expenseRecoverDisplay" style="cursor:pointer; color:#007bff;" data-value="${val}" data-change="1">
      ${val}%
    </span>
  `);
  recalculateSalesPrice();
}

// Mostrar input editable para COMISIÓN
$(document).on('click', '#commissionDisplay', function () {
  const val = $(this).data('value') || parseFloat($(this).text());
  $(this).replaceWith(`
    <input type="number" id="commissionInput" class="form-control form-control-sm"
           style="max-width:60px; display:inline;" min="0" max="100" step="0.1" value="${val}">
  `);
  $('#commissionInput').focus();
});

// Guardar cambios al salir del input de COMISIÓN
$(document).on('blur', '#commissionInput', updateCommission);
$(document).on('keypress', '#commissionInput', function (e) {
  if (e.which == 13) updateCommission();
});

function updateCommission() {
  const val = parseFloat($('#commissionInput').val()).toFixed(1);
  $('#commissionInput').replaceWith(`
    <span id="commissionDisplay" style="cursor:pointer; color:#007bff;" data-value="${val}" data-change="1">
      ${val}%
    </span>
  `);
  recalculateSalesPrice();
}

// Mostrar input editable para RENTABILIDAD
$(document).on('click', '#profitDisplay', function () {
  const val = $(this).data('value') || parseFloat($(this).text());
  $(this).replaceWith(`
    <input type="number" id="profitInput" class="form-control form-control-sm"
           style="max-width:60px; display:inline;" min="0" max="100" step="0.1" value="${val}" data-change="1">
  `);
  $('#profitInput').focus();
});

// Guardar cambios al salir del input de RENTABILIDAD
$(document).on('blur', '#profitInput', updateProfit);
$(document).on('keypress', '#profitInput', function (e) {
  if (e.which == 13) updateProfit();
});

function updateProfit() {
  const val = parseFloat($('#profitInput').val()).toFixed(1);
  $('#profitInput').replaceWith(`
    <span id="profitDisplay" style="cursor:pointer; color:#007bff;" data-value="${val}" data-change="1">
      ${val}%
    </span>
  `);
  recalculateSalesPrice();
}

// ============================
// 🧮 FUNCIÓN DE RECÁLCULO
// ============================
function recalculateSalesPrice() {
  const payRawMaterial = getValue('#payRawMaterial');
  const payWorkforce = getValue('#payWorkforce');
  const payIndirectCost = getValue('#payIndirectCost');
  const services = getValue('#services');

  $('#saveContainer').removeClass('d-none').hide().fadeIn(800);

  const expensePercentage = parseFloat(
    $('#expenseRecoverInput').length ? $('#expenseRecoverInput').val() : $('#expenseRecoverDisplay').data('value')
  ) || 0;

  const commissionPercentage = parseFloat(
    $('#commissionInput').length ? $('#commissionInput').val() : $('#commissionDisplay').data('value')
  ) || 0;

  const profitPercentage = parseFloat(
    $('#profitInput').length ? $('#profitInput').val() : $('#profitDisplay').data('value')
  ) || 0;

  const baseCost = payRawMaterial + payWorkforce + payIndirectCost + services;

  let expenses = 0;
  let salesPrice = 0;
  let commission = 0;
  let profit = 0;
  let costTotal = 0;

  if (expensePercentage === 0) {
    // 🟨 Gasto fijo, se toma desde el texto
    expenses = parseValueFromText('#payAssignableExpenses');

    // 🧮 Primero se calcula el precio sin comisión
    const partialPrice = (baseCost + expenses) / (1 - profitPercentage / 100);

    // 🧮 Luego se aplica la comisión
    salesPrice = partialPrice / (1 - commissionPercentage / 100);

    // Comisión real sobre PV
    commission = salesPrice * (commissionPercentage / 100);

    // Utilidad real sobre precio después de comisión
    profit = (salesPrice - commission - baseCost - expenses);
  } else {
    // 🧮 Gasto como porcentaje (cálculo encadenado)
    const netoFactor = (1 - expensePercentage / 100) * (1 - profitPercentage / 100) * (1 - commissionPercentage / 100);
    salesPrice = baseCost / netoFactor;

    commission = salesPrice * (commissionPercentage / 100);
    const valueAfterCommission = salesPrice - commission;
    profit = valueAfterCommission * (profitPercentage / 100);
    const valueAfterProfit = valueAfterCommission - profit;
    expenses = valueAfterProfit * (expensePercentage / 100);
  }

  $('.saveChanges').fadeIn();
  costTotal = baseCost + expenses;

  // 🖨️ Mostrar resultados
  $('#payAssignableExpenses').text(formatCOP(expenses));
  $('#costTotal').text(formatCOP(costTotal));
  $('#commisionSale').text(formatCOP(commission));
  $('#profitability').text(formatCOP(profit));
  $('#salesPrice').text(formatCOP(salesPrice));
}






// ============================
// 🔧 UTILIDADES
// ============================
function getValue(selector) {
  return parseFloat($(selector).text().replace(/\$|\./g, '').replace(',', '.')) || 0;
}

function formatCOP(value) {
  return `$ ${Math.round(value).toLocaleString('es-CO')}`;
}

function parseValueFromText(selector) {
  const text = $(selector).text().replace(/[^\d,.-]/g, '').replace(',', '.');
  return parseFloat(text) || 0;
}
