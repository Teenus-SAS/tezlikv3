$(document).ready(function () {
  id_product = sessionStorage.getItem('idProduct');

  loadIndicatorsProducts = (id_product) => {
    fetch(`/api/dashboardPricesProducts/${id_product}`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        generalIndicators(data.cost_product);
        UnitsVolSold(data.cost_product);
        totalCostData(data.cost_product);
        graphicCostExpenses(data.cost_product);
        graphicCostWorkforce(data.cost_workforce);
        graphicCostTimeProcess(data.cost_time_process);
        graphicPromTime(data.average_time_process, data.cost_time_process);
        graphicCompPrices(data.cost_product);
        graphicCostMaterials(data.cost_materials);
      });
  };
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
    $('#nameProduct').html(data[0].product);
    $(`#product option[value=${data[0].id_product}]`).prop('selected', true);

    $('.imageProduct').html(`
      <img src="${data[0].img}" alt="" style="width:50%;border-radius:100px">
    `);

    let costTotal =
      data[0].cost_materials +
      data[0].cost_workforce +
      data[0].cost_indirect_cost +
      data[0].assignable_expense;

    $('#rawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-ES')}`
    );
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
    dataCost = getDataCost(data[0]);
    $('#recomendedPrice').html(
      `$ ${dataCost.price.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };

  /* Costeo Total */

  totalCostData = (data) => {
    dataCost = getDataCost(data[0]);
    // $('#salesPrice').html(`$ ${data[0].price.toLocaleString('es-ES')}`);
    $('#costTotal').html(
      `$ ${dataCost.costTotal.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#cost').html(
      `$ ${dataCost.cost.toLocaleString(undefined, {
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

    $('#commission').html(`Comisión Vts (${data[0].commission_sale}%)`);
    $('#commisionSale').html(
      `$ ${Math.round(dataCost.costCommissionSale).toLocaleString()}`
    );

    $('#profit').html(`Rentabilidad (${data[0].profitability}%)`);
    $('#profitability').html(
      `$ ${Math.round(dataCost.costProfitability).toLocaleString()}`
    );

    $('#salesPrice').html(
      `$ ${dataCost.price.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };

  getDataCost = (data) => {
    cost =
      parseFloat(data.cost_materials) +
      parseFloat(data.cost_workforce) +
      parseFloat(data.cost_indirect_cost);
    costTotal = cost + parseFloat(data.assignable_expense);

    costCommissionSale = data.price * (data.commission_sale / 100);
    costProfitability = data.price * (data.profitability / 100);

    price = costTotal + costCommissionSale + costProfitability;

    dataCost = {
      cost: cost,
      costTotal: costTotal,
      costCommissionSale: costCommissionSale,
      costProfitability: costProfitability,
      price: price,
    };

    return dataCost;
  };

  loadIndicatorsProducts(id_product);

  $('#product').change(function (e) {
    e.preventDefault();

    id_product = this.value;

    loadIndicatorsProducts(id_product);
  });
});
