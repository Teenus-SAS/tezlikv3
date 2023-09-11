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

    profitability = (((data.sale_price * (1 - (data.commission_sale / 100))) - costTotal) / data.sale_price) * 100; 

    dataCost = {
      cost: cost,
      costTotal: costTotal,
      actualProfitability: profitability,
      costCommissionSale: costCommissionSale,
      costProfitability: costProfitability,
      expense: expense,
      price: price,
    };

    return dataCost;
  };
});
