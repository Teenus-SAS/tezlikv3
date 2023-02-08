$(document).ready(function () {
  let id_product = sessionStorage.getItem('idProduct');

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
    // $(`#product option[value=${data[0].id_product}]`).prop('selected', true);

    $('.imageProduct').html(`
      <img src="${data[0].img}" class="mx-auto d-block" style="width:60px;border-radius:100px">
    `);

    dataCost = getDataCost(data[0]);

    $('#rawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#workforce').html(
      `$ ${data[0].cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#indirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('#assignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    percentRawMaterial = (data[0].cost_materials / dataCost.costTotal) * 100;
    percentWorkforce = (data[0].cost_workforce / dataCost.costTotal) * 100;
    percentIndirectCost =
      (data[0].cost_indirect_cost / dataCost.costTotal) * 100;
    percentAssignableExpenses = (dataCost.expense / dataCost.costTotal) * 100;

    $('#percentRawMaterial').html(`${percentRawMaterial.toFixed(0)} %`);
    $('#percentWorkforce').html(`${percentWorkforce.toFixed(0)} %`);
    $('#percentIndirectCost').html(`${percentIndirectCost.toFixed(0)} %`);
    $('#percentAssignableExpenses').html(
      `${percentAssignableExpenses.toFixed(0)} %`
    );
  };

  /* Ventas */

  UnitsVolSold = (data) => {
    $('#unitsSold').html(data[0].units_sold.toLocaleString('es-CO'));
    $('#turnover').html(`$ ${data[0].turnover.toLocaleString('es-CO')}`);
    $('#recomendedPrice').html(
      `$ ${data[0].price.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };

  /* Costeo Total */

  totalCostData = (data) => {
    dataCost = getDataCost(data[0]);
    $('#costTotal').html(
      `$ ${dataCost.costTotal.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#cost').html(
      `$ ${dataCost.cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#payRawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#payWorkforce').html(
      `$ ${data[0].cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('#payIndirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('#services').html(
      `$ ${data[0].services.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('#expenses').html(`Gastos (${data[0].expense_recover}%)`);
    $('#payAssignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('#commission').html(`Comisión Vts (${data[0].commission_sale}%)`);
    $('#commisionSale').html(
      `$ ${Math.round(dataCost.costCommissionSale).toLocaleString('es-CO')}`
    );

    $('#profit').html(`Rentabilidad (${data[0].profitability}%)`);
    $('#profitability').html(
      `$ ${Math.round(dataCost.costProfitability).toLocaleString('es-CO')}`
    );

    $('#salesPrice').html(
      `$ ${data[0].price.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };

  loadIndicatorsProducts(id_product);

  $('#product').change(function (e) {
    e.preventDefault();

    id_product = this.value;

    loadIndicatorsProducts(id_product);
  });

  function setProduct() {
    $(`#product option[value=${id_product}]`).prop('selected', true);
  }
  setTimeout(setProduct, 5000);
});
