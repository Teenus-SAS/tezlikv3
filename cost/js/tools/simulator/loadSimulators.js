$(document).ready(function () {
  /* Cargar nombre de producto */
  $('#product').change(async function (e) {
    e.preventDefault();

    let data = await searchData(`/api/dashboardPricesSimulator/${this.value}`);

    $('#nameProduct').html(data[0].product);

    $('.imageProduct').html(`
      <img src="${data[0].img}" class="mx-auto d-block" style="width:60px;border-radius:100px">
    `);

    dataCost = getDataCost(data[0]);

    $('.rawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.workforce').html(
      `$ ${data[0].cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.indirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('.assignableExpenses').html(
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

    $('.percentRawMaterial').html(`${percentRawMaterial.toFixed(0)} %`);
    $('.percentWorkforce').html(`${percentWorkforce.toFixed(0)} %`);
    $('.percentIndirectCost').html(`${percentIndirectCost.toFixed(0)} %`);
    $('.percentAssignableExpenses').html(
      `${percentAssignableExpenses.toFixed(0)} %`
    );

    $('.costTotal').html(
      `$ ${dataCost.costTotal.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.cost').html(
      `$ ${dataCost.cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.payRawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.payWorkforce').html(
      `$ ${data[0].cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.payIndirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('.services').html(
      `$ ${data[0].services.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    if (flag_expense == 2)
      $('.expenses').html(`Gastos (${data[0].expense_recover}%)`);

    $('.payAssignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('.commission').html(`Comisión Vts (${data[0].commission_sale}%)`);
    $('.commisionSale').html(
      `$ ${Math.round(dataCost.costCommissionSale).toLocaleString('es-CO')}`
    );

    $('.profit').html(`Rentabilidad (${data[0].profitability}%)`);
    $('.profitability').html(
      `$ ${Math.round(dataCost.costProfitability).toLocaleString('es-CO')}`
    );

    $('.salesPrice').html(
      `$ ${data[0].price.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  });
});
