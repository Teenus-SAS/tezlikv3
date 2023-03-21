$(document).ready(function () {
  getDataCost = (data) => {
    cost =
      parseFloat(data.cost_materials) +
      parseFloat(data.cost_workforce) +
      parseFloat(data.cost_indirect_cost) +
      parseFloat(data.services);

    if (flag_expense == 0 || flag_expense == 1) {
      expense = data.assignable_expense;
      costTotal = cost + data.expense_recover;
    } else if (flag_expense == 2) {
      costTotal = cost / (1 - data.expense_recover / 100);
      expense = costTotal * (data.expense_recover / 100);
    }

    pPrice = costTotal / (1 - data.profitability / 100);

    costProfitability = pPrice * (data.profitability / 100);

    costCommissionSale = data.price * (data.commission_sale / 100);

    dataCost = {
      cost: cost,
      costTotal: costTotal,
      costCommissionSale: costCommissionSale,
      costProfitability: costProfitability,
      expense: expense,
    };

    return dataCost;
  };
});
