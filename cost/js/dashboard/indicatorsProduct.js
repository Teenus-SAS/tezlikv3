$(document).ready(function () {
  let id_product = sessionStorage.getItem('idProduct');

  loadIndicatorsProducts = async (id_product) => {
    let data = await searchData(`/api/dashboardPricesProducts/${id_product}`);
    await generalIndicators(data.cost_product);
    await UnitsVolSold(data.cost_product);
    await totalCostData(data.cost_product);
    await graphicCostExpenses(data.cost_product);
    await graphicCostWorkforce(data.cost_workforce);
    await graphicCostTimeProcess(data.cost_time_process);
    await graphicPromTime(data.average_time_process);
    await graphicCompPrices(data.cost_product);

    if (data.cost_materials.length > 10) {
      data = await searchData(`/api/rawMaterials/${id_product}`);
      data = data['80RawMaterials'];
    }
    else
      data = data.cost_materials;
    
    await graphicCostMaterials(data); 
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

    !data[0].img
      ? (txt = '')
      : (txt = ` <img src="${data[0].img}" class="mx-auto d-block" style="width:60px;border-radius:100px"> `);

    $('.imageProduct').html(txt);

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

    if (data[0].cost_materials == 0) $('.payRawMaterial').hide();
    else $('.payRawMaterial').show();
    if (data[0].cost_workforce == 0) $('.payWorkforce').hide();
    else $('.payWorkforce').show();
    if (data[0].cost_indirect_cost == 0) $('.payIndirectCost').hide();
    else $('.payIndirectCost').show();
    if (data[0].services == 0) $('.services').hide();
    else $('.services').show();
    if (dataCost.expense == 0) $('.payAssignableExpenses').hide();
    else $('.payAssignableExpenses').show();
    if (dataCost.costCommissionSale == 0) $('.commission').hide();
    else $('.commission').show();
    if (dataCost.costProfitability == 0) $('.profit').hide();
    else $('.profit').show();
    if (data[0].sale_price == 0) $('.actualSalePrice').hide();
    else $('.actualSalePrice').show();

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

    if (flag_expense == 2)
      $('#expenses').html(`Gastos (${data[0].expense_recover}%)`);

    $('#payAssignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('#commission').html(
      `Comisión Vts (${data[0].commission_sale.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })}%)`
    );
    $('#commisionSale').html(
      `$ ${Math.round(dataCost.costCommissionSale).toLocaleString('es-CO')}`
    );

    $('#profit').html(
      `Rentabilidad (${data[0].profitability.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })}%)`
    );
    $('#profitability').html(
      `$ ${Math.round(dataCost.costProfitability).toLocaleString('es-CO')}`
    );

    $('.suggestedPrice').html(
      `$ ${data[0].price.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );

    $('#actualSalePrice').html(`$ ${data[0].sale_price.toLocaleString('es-CO', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    })}`);

    $('#minProfit').html(`${data[0].profitability.toLocaleString('es-CO', {
      maximumFractionDigits: 2,
    })}%`);
    

    $('#actualProfitability').html(``); 

    $('.cardTrafficLight').empty();
    let content = '';
    
    if (dataCost.actualProfitability == data[0].profitability)
      content = `<div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-warning">${dataCost.actualProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                        <div class="text-center">
                          <span class="text-warning font-weight-bold" style="font-size:large">
                            <i style="font-style: initial;"><i class="bx bxs-no-entry" style="font-size: xxx-large;color:green"></i></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>`;
    else if (dataCost.actualProfitability > data[0].profitability)
      content = `<div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-success">${dataCost.actualProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                        <div class="text-center">
                          <span class="text-success font-weight-bold" style="font-size:large">
                            <i style="font-style: initial;"><i class="bx bxs-check-circle" style="font-size: xxx-large;color:green"></i></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>`;
    else
      content = `<div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-danger">${dataCost.actualProfitability.toLocaleString('es-CO', {maximumFractionDigits: 2,})} %</h2>
                        </div>
                        <div class="text-center">
                          <span class="text-danger font-weight-bold" style="font-size:large">
                            <i style="font-style: initial;"><i class="bx bxs-x-circle" style="font-size: xxx-large;color:red"></i></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>`;
    
    $('.cardTrafficLight').append(content);
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
