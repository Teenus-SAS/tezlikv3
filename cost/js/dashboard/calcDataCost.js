$(document).ready(function () {
  getDataCost = (data) => {
    let profitability = 0;
    let profitability2 = 0;
    let costProfitability = 0;
    let costCommissionSale = 0;
    let costActualProfitability = 0;
    let price2 = 0;

    cost =
      parseFloat(data.cost_materials) +
      parseFloat(data.cost_workforce) +
      parseFloat(data.cost_indirect_cost) +
      parseFloat(data.services);

    if (flag_expense == 0 || flag_expense == 1) {
      expense = parseFloat(data.assignable_expense);
      costTotal = cost + parseFloat(data.assignable_expense);
    } else if (flag_expense == 2) {
      costTotal = cost / (1 - parseFloat(data.expense_recover) / 100);
      expense = costTotal * (parseFloat(data.expense_recover) / 100);
    }

    pPrice = costTotal / (1 - parseFloat(data.profitability) / 100);
    price = pPrice / (1 - parseFloat(data.commission_sale) / 100);

    costProfitability = pPrice * (parseFloat(data.profitability) / 100);

    costCommissionSale = price * (parseFloat(data.commission_sale) / 100);

    if (flag_expense === "2" || flag_expense_distribution === "2") {
      // profitability = (((data.sale_price * (1 - (data.commission_sale / 100))) - costTotal) / data.sale_price) * 100;
      price2 = 0;
      profitability = ((data.sale_price - costTotal) / data.sale_price) * 100;
      profitability2 = ((data.sale_price - costTotal) / data.sale_price) * 100;
      costActualProfitability =
        parseFloat(data.sale_price) * (profitability / 100);
    } else if (data.units_sold > 0 && data.turnover > 0) {
      // profitability = ((data.sale_price - costTotal) / data.sale_price) * 100;
      price2 = costTotal * parseFloat(data.units_sold);
      profitability = ((parseFloat(data.turnover) - price2) / price2) * 100;
      profitability2 = (((parseFloat(data.sale_price) - costTotal) / parseFloat(data.sale_price)) * 100);
      // profitability2 = (((parseFloat(data.sale_price) - costTotal) / costTotal) * 100);
      costActualProfitability = parseFloat(data.turnover) - price2;
      // costActualProfitability = parseFloat(data.turnover) - price2;
      // profitability = (((parseFloat(data.turnover) - price2) / price2) * parseFloat(data.units_sold));
    }

    isNaN(profitability) ? (profitability = 0) : profitability;
    isNaN(profitability2) ? (profitability2 = 0) : profitability2;
    isNaN(costProfitability) ? (costProfitability = 0) : costProfitability;
    isNaN(costCommissionSale) ? (costCommissionSale = 0) : costCommissionSale;
    isNaN(costActualProfitability)
      ? (costActualProfitability = 0)
      : costActualProfitability;

    dataCost = {
      cost: cost,
      costTotal: costTotal,
      actualProfitability: profitability,
      actualProfitability2: profitability2,
      costCommissionSale: costCommissionSale,
      costProfitability: costProfitability,
      costActualProfitability: costActualProfitability,
      expense: expense,
      price: price,
      price2: price2,
    };

    return dataCost;
  };
});
