$(document).ready(function () {
  getDataCost = (data) => {
    let profitability = 0;
    let profitability2 = 0;
    let costProfitability = 0;
    let costCommissionSale = 0;
    let costActualProfitability = 0;
    let price2 = 0; 
    let expense = parseFloat(data.assignable_expense); 

    // Calcular costo
    cost = parseFloat(data.cost_materials) + parseFloat(data.cost_workforce) + parseFloat(data.cost_indirect_cost) + parseFloat(data.services);

    // Calcular Costo total dependiendo de el acceso activo
    if (flag_expense === '0' || flag_expense === '1') { // Distribucion
      costTotal = cost + expense;
    } else if (flag_expense === '2') { // Recuperacion
      costTotal = cost / (1 - parseFloat(data.expense_recover) / 100);
      expense = costTotal * (parseFloat(data.expense_recover) / 100);
    }

    // Calcular precio parcial 
    pPrice = costTotal / (1 - parseFloat(data.profitability) / 100);

    // Calcular precio con precio parcial (calculado)
    price = pPrice / (1 - parseFloat(data.commission_sale) / 100);

    // Calcular costo de rentabilidad con el precio parcial
    costProfitability = pPrice * (parseFloat(data.profitability) / 100);

    // Calcular comision con precio
    costCommissionSale = price * (parseFloat(data.commission_sale) / 100);

    // Calcular precio real
    let recomendedPrice = parseFloat(data.turnover) / parseFloat(data.units_sold);

    // Calcular rentabilidades de acuedo al acceso activo
    if (flag_expense === "2" || flag_expense_distribution === "2") { // Acceso distribucion por familia o Recuperacion 
      price2 = 0;

      // profitability = ((data.sale_price - costTotal) / data.sale_price) * 100;

      // Rentabilidad de precio actual
      // profitability = ((parseFloat(data.sale_price) - costTotal) / parseFloat(data.sale_price)) * 100;
      // profitability2 = ((parseFloat(data.sale_price) - costTotal) / parseFloat(data.sale_price)) * 100;
      profitability3 = ((parseFloat(data.sale_price) - costTotal) / parseFloat(data.sale_price)) * 100;
      // profitability3 = ((parseFloat(data.sale_price) - costTotal) / parseFloat(data.sale_price)) * 100;

      // Rentabilidad de precio real
      // profitability = 0;
      // profitability = ((recomendedPrice - costTotal) / recomendedPrice) * 100;
      // profitability3 = ((recomendedPrice - costTotal) / recomendedPrice) * 100;

      // Costo de rentabilidad del precio actual
      costActualProfitability = parseFloat(data.sale_price) * (profitability / 100);

    } else if ((parseFloat(data.units_sold) > 0 && parseFloat(data.turnover) > 0) || flag_expense === "1") { // Acceso distribucion por producto

      // Calcular precio parcial para realizar calculos despues
      price2 = costTotal * parseFloat(data.units_sold);

      // Rentabilidad con precio parcial (calculado)
      profitability = ((parseFloat(data.turnover) - price2) / price2) * 100;

      // Rentabilidad con precio actual
      profitability3 = (((parseFloat(data.sale_price) - costTotal) / parseFloat(data.sale_price)) * 100); 

      // Rentabilidad con precio real
      profitability2 = (((recomendedPrice - costTotal) / recomendedPrice) * 100); 

      // Costo de rentabilidad de precio parcial (calculado)
      costActualProfitability = parseFloat(data.turnover) - price2; 
    }

    // Validar si alguno de los calculos anteriores es de formato NaN que devuelva cero (0) 
    isNaN(profitability) ? (profitability = 0) : profitability;
    isNaN(profitability2) ? (profitability2 = 0) : profitability2;
    isNaN(profitability3) ? (profitability3 = 0) : profitability3;
    isNaN(costProfitability) ? (costProfitability = 0) : costProfitability;
    isNaN(costCommissionSale) ? (costCommissionSale = 0) : costCommissionSale;
    isNaN(costActualProfitability)
      ? (costActualProfitability = 0)
      : costActualProfitability;
    
    // Convertir valores calculados a array 
    dataCost = {
      cost: cost,
      costTotal: costTotal,
      actualProfitability: profitability,
      actualProfitability2: profitability2,
      actualProfitability3: profitability3,
      costCommissionSale: costCommissionSale,
      costProfitability: costProfitability,
      costActualProfitability: costActualProfitability,
      expense: expense,
      price: price,
      price2: price2,
    };

    // Retornar
    return dataCost;
  };
});
