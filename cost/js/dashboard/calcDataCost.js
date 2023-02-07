$(document).ready(function () {
  getDataCost = (data) => {
    cost =
      parseFloat(data.cost_materials) +
      parseFloat(data.cost_workforce) +
      parseFloat(data.cost_indirect_cost) +
      parseFloat(data.services);

    costTotal = cost / (1 - data.expense_recover / 100);

    data.expense_recover == 0
      ? (expense = data.assignable_expense)
      : (expense = costTotal * (data.expense_recover / 100));

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
