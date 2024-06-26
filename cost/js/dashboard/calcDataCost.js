$(document).ready(function () {
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

});
