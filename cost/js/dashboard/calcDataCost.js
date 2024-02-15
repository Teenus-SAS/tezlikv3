$(document).ready(function () {
  getDataCost = (data) => {
    cost =
      parseFloat(data.cost_materials) +
      parseFloat(data.cost_workforce) +
      parseFloat(data.cost_indirect_cost) +
      parseFloat(data.services);

    if (flag_expense == 0 || flag_expense == 1) {
      expense = data.assignable_expense;
      costTotal = cost + data.assignable_expense;
    } else if (flag_expense == 2) {
      costTotal = cost / (1 - data.expense_recover / 100);
      expense = costTotal * (data.expense_recover / 100);
    }

    pPrice = costTotal / (1 - data.profitability / 100);
    price = pPrice / (1 - data.commission_sale / 100);

    costProfitability = pPrice * (data.profitability / 100);

    costCommissionSale = price * (data.commission_sale / 100);

    if (data.units_sold == 0 || data.turnover == 0)
      profitability = (((data.sale_price * (1 - (data.commission_sale / 100))) - costTotal) / data.sale_price) * 100;
    else {
      // profitability = ((data.sale_price - costTotal) / data.sale_price) * 100;
      price2 = data.turnover / data.units_sold;
      profitability = (((price2 - costTotal) / costTotal) * data.units_sold);
      profitability2 = (((data.sale_price - costTotal) / costTotal) * 100);
    }

    costActualProfitability = data.sale_price * (profitability / 100);

    isNaN(profitability) ? profitability = 0 : profitability;
    isNaN(costProfitability) ? costProfitability = 0 : costProfitability;
    isNaN(costCommissionSale) ? costCommissionSale = 0 : costCommissionSale;
    isNaN(costActualProfitability) ? costActualProfitability = 0 : costActualProfitability;

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
    };

    return dataCost;
  };
});
