$(document).ready(function () {
  /* Cargar nombre de producto */
  $('#product').change(async function (e) {
    e.preventDefault();

    let data = await searchData(`/api/dashboardPricesSimulator/${this.value}`);

    await setDataDashboard(data.products[0]);

    await loadTblSimulatorProducts(data.products);
    await loadTblSimulatorMachines(data.productsProcess);
    await loadTblSimulatorMaterials(data.materials);
    await loadTblSimulatorProductsMaterials(data.materials);
    await loadTblSimulatorProductsProcess(data.productsProcess);
    await loadTblSimulatorFactoryLoad(data.factoryLoad);
    await loadTblSimulatorExternalServices(data.externalServices);
    // await loadTblSimulatorPayroll(data.payroll);

    // if (flag_expense == 1 || flag_expense == 0)
    //   await loadTblSimulatorExpenseDistribution(data.expensesDistribution);
    // else await loadTblSimulatorExpenseRecover(data.expenseRecover);
  });

  setDataDashboard = (data) => {
    $('#nameProduct').html(data.product);

    $('.imageProduct').html(`
      <img src="${data.img}" class="mx-auto d-block" style="width:60px;border-radius:100px">
    `);

    dataCost = getDataCost(data);

    $('.rawMaterial').html(
      `$ ${data.cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.workforce').html(
      `$ ${data.cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.indirectCost').html(
      `$ ${data.cost_indirect_cost.toLocaleString('es-CO', {
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

    percentRawMaterial = (data.cost_materials / dataCost.costTotal) * 100;
    percentWorkforce = (data.cost_workforce / dataCost.costTotal) * 100;
    percentIndirectCost = (data.cost_indirect_cost / dataCost.costTotal) * 100;
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
      `$ ${data.cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.payWorkforce').html(
      `$ ${data.cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
    $('.payIndirectCost').html(
      `$ ${data.cost_indirect_cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('.services').html(
      `$ ${data.services.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    if (flag_expense == 2)
      $('.expenses').html(`Gastos (${data.expense_recover}%)`);

    $('.payAssignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('.commission').html(`Comisión Vts (${data.commission_sale}%)`);
    $('.commisionSale').html(
      `$ ${Math.round(dataCost.costCommissionSale).toLocaleString('es-CO')}`
    );

    $('.profit').html(`Rentabilidad (${data.profitability}%)`);
    $('.profitability').html(
      `$ ${Math.round(dataCost.costProfitability).toLocaleString('es-CO')}`
    );

    $('.salesPrice').html(
      `$ ${data.price.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };
});
