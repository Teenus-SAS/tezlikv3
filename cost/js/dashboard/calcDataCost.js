$(document).ready(function () {
  getDataCost = (data) => {
    let profitability = 0;
    let profitability2 = 0;
    let costProfitability = 0;
    let costCommissionSale = 0;
    let costActualProfitability = 0;
    let price2 = 0;
    let cost_materials = 0;
    let cost_workforce = 0;
    let cost_indirect_cost = 0;
    let services = 0;
    let expense = data.expense;
    let sale_price = 0;
    let turnover = 0;

    // let typePrice = sessionStorage.getItem('typePrice');
    
    // if (typePrice == '1' || !typePrice) {
    //   cost_materials = parseFloat(data.cost_materials);
    //   cost_workforce = parseFloat(data.cost_workforce);
    //   cost_indirect_cost = parseFloat(data.cost_indirect_cost);
    //   services = parseFloat(data.services);
    //   expense = parseFloat(data.assignable_expense);
    //   sale_price = parseFloat(data.sale_price);
    //   turnover = parseFloat(data.turnover);
    // } else {
    //   cost_materials = (parseFloat(data.cost_materials) / parseFloat(coverage));
    //   cost_workforce = (parseFloat(data.cost_workforce) / parseFloat(coverage));
    //   cost_indirect_cost = (parseFloat(data.cost_indirect_cost) / parseFloat(coverage));
    //   services = (parseFloat(data.services) / parseFloat(coverage));
    //   expense = (parseFloat(data.assignable_expense) / parseFloat(coverage));
    //   sale_price = parseFloat(data.sale_price_usd);
    //   turnover = (parseFloat(data.turnover) / parseFloat(coverage));
    // }

    cost = data.cost_materials + data.cost_workforce + data.cost_indirect_cost + data.services;

    if (flag_expense == 0 || flag_expense == 1) {
      costTotal = cost + data.expense;
    } else if (flag_expense == 2) {
      costTotal = cost / (1 - parseFloat(data.expense_recover) / 100);
      expense = costTotal * (parseFloat(data.expense_recover) / 100);
    }

    pPrice = costTotal / (1 - parseFloat(data.profitability) / 100);
    price = pPrice / (1 - parseFloat(data.commission_sale) / 100);

    costProfitability = pPrice * (parseFloat(data.profitability) / 100);

    costCommissionSale = price * (parseFloat(data.commission_sale) / 100);
    let recomendedPrice = data.turnover / parseFloat(data.units_sold);

    if (flag_expense === "2" || flag_expense_distribution === "2") {
      price2 = 0;

      // profitability = ((data.sale_price - costTotal) / data.sale_price) * 100;
      // profitability2 = ((data.sale_price - costTotal) / data.sale_price) * 100;
      profitability = ((recomendedPrice - costTotal) / recomendedPrice) * 100;
      profitability2 = ((recomendedPrice - costTotal) / recomendedPrice) * 100;
      costActualProfitability = parseFloat(data.sale_price) * (profitability / 100);

    } else if ((data.units_sold > 0 && data.turnover > 0) || flag_expense === "1") {
      price2 = costTotal * parseFloat(data.units_sold);
      profitability = ((data.turnover - price2) / price2) * 100;
      // profitability2 = (((parseFloat(data.sale_price) - costTotal) / parseFloat(data.sale_price)) * 100); 
      profitability2 = (((recomendedPrice - costTotal) / recomendedPrice) * 100);
      costActualProfitability = data.turnover - price2;
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
