$(document).ready(function () {
  id_product = sessionStorage.getItem('idProduct');
  fetch(`/api/dashboardPricesProducts/${id_product}`)
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);
      generalIndicators(data.cost_product);
      UnitsVolSold(data.cost_product);
      totalCost(data.cost_product);
      graphicCostExpenses(data.cost_product);
      graphicCostWorkforce(data.cost_workforce);
      graphicCostTimeProcess(data.cost_time_process);
      graphicPromTime(data.average_time_process, data.cost_time_process);
      graphicCompPrices(data.cost_product);
      graphicCostMaterials(data.cost_materials);
    });

  /* Colors */

  dynamicColors = () => {
    let letters = '0123456789ABCDEF'.split('');
    let color = '#';

    for (var i = 0; i < 6; i++)
      color += letters[Math.floor(Math.random() * 16)];
    return color;
  };

  getRandomColor = (a) => {
    let color = [];
    for (i = 0; i < a; i++) color.push(dynamicColors());
    return color;
  };

  /* Indicadores Generales */

  generalIndicators = (data) => {
    $('#product').html(data[0].product);

    let costTotal =
      data[0].cost_materials +
      data[0].cost_workforce +
      data[0].cost_indirect_cost +
      data[0].assignable_expense;

    $('#rawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-ES')}`
    );
    // $('#workforce').html(`$ ${data[0].cost_workforce.toLocaleString('es-ES')}`);
    $('#workforce').html(
      `$ ${data[0].cost_workforce.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#indirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );
    $('#assignableExpenses').html(
      `$ ${data[0].assignable_expense.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );

    percentRawMaterial = (data[0].cost_materials / costTotal) * 100;
    percentWorkforce = (data[0].cost_workforce / costTotal) * 100;
    percentIndirectCost = (data[0].cost_indirect_cost / costTotal) * 100;
    percentAssignableExpenses = (data[0].assignable_expense / costTotal) * 100;

    $('#percentRawMaterial').html(`${percentRawMaterial.toFixed(2)} %`);
    $('#percentWorkforce').html(`${percentWorkforce.toFixed(2)} %`);
    $('#percentIndirectCost').html(`${percentIndirectCost.toFixed(2)} %`);
    $('#percentAssignableExpenses').html(
      `${percentAssignableExpenses.toFixed(2)} %`
    );
  };

  /* Ventas */

  UnitsVolSold = (data) => {
    $('#unitsSold').html(data[0].units_sold.toLocaleString('es-ES'));
    $('#turnover').html(`$ ${data[0].turnover.toLocaleString('es-ES')}`);
    $('#recomendedPrice').html(
      `$ ${data[0].price.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };

  /* Costeo Total */

  totalCost = (data) => {
    cost =
      parseFloat(data[0].cost_materials) +
      parseFloat(data[0].cost_workforce) +
      parseFloat(data[0].cost_indirect_cost);
    costTotal = cost + parseFloat(data[0].assignable_expense);

    // $('#salesPrice').html(`$ ${data[0].price.toLocaleString('es-ES')}`);
    $('#costTotal').html(
      `$ ${costTotal.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#cost').html(
      `$ ${cost.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#payRawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-ES')}`
    );
    $('#payWorkforce').html(
      `$ ${data[0].cost_workforce.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#payIndirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );
    $('#payAssignableExpenses').html(
      `$ ${data[0].assignable_expense.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );

    costCommissionSale = data[0].price * (data[0].commission_sale / 100);
    $('#commission').html(`Comisión Vts (${data[0].commission_sale}%)`);
    $('#commisionSale').html(
      `$ ${Math.round(costCommissionSale).toLocaleString()}`
    );

    costProfitability = data[0].price * (data[0].profitability / 100);
    $('#profit').html(`Rentabilidad (${data[0].profitability}%)`);
    $('#profitability').html(
      `$ ${Math.round(costProfitability).toLocaleString()}`
    );

    price = costTotal + costCommissionSale + costProfitability;

    $('#salesPrice').html(
      `$ ${price.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };
});
