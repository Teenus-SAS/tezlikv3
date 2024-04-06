$(document).ready(function () {
  let id_product = sessionStorage.getItem('idProduct');
    
  loadIndicatorsProducts = async (id_product) => {
    try {
      let data = await searchData(`/api/dashboardPricesProducts/${id_product}`);

      let typePrice = sessionStorage.getItem('typePrice');
    
      if (typePrice == '2') {
        data.cost_product[0].cost_materials = (parseFloat(data.cost_product[0].cost_materials) / parseFloat(coverage));
        data.cost_product[0].cost_workforce = (parseFloat(data.cost_product[0].cost_workforce) / parseFloat(coverage));
        data.cost_product[0].cost_indirect_cost = (parseFloat(data.cost_product[0].cost_indirect_cost) / parseFloat(coverage));
        data.cost_product[0].services = (parseFloat(data.cost_product[0].services) / parseFloat(coverage));
        data.cost_product[0].assignable_expense = (parseFloat(data.cost_product[0].assignable_expense) / parseFloat(coverage));
        data.cost_product[0].price = parseFloat(data.cost_product[0].price_usd);
        data.cost_product[0].sale_price = parseFloat(data.cost_product[0].sale_price_usd);
        data.cost_product[0].turnover = (parseFloat(data.cost_product[0].turnover) / parseFloat(coverage));
      }

      await generalIndicators(data.cost_product);
      await UnitsVolSold(data.cost_product);
      await totalCostData(data.cost_product);
      await graphicCostExpenses(data.cost_product);

      if (typePrice == '2') {
        for (let i = 0; i < data.cost_workforce.length; i++) {
          data.cost_workforce[i].workforce = parseFloat(data.cost_workforce[i].workforce) / parseFloat(coverage);
        }
      }

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

      if (typePrice == '2') {
        for (let i = 0; i < data.length; i++) {
          data[i].totalCostMaterial = parseFloat(data[i].totalCostMaterial) / parseFloat(coverage);
        }
      }
    
      await graphicCostMaterials(data);
    }
    catch (error) {
      console.error('Error loading data:', error);
    }
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

    let typePrice = sessionStorage.getItem('typePrice');

    if (typePrice == '1' || !typePrice)
      max = 0
    else {
      max = 2
    }

    $('#rawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );
    $('#workforce').html(
      `$ ${data[0].cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );
    $('#indirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );

    $('#assignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );

    percentRawMaterial = (data[0].cost_materials / dataCost.costTotal) * 100;
    percentWorkforce = (data[0].cost_workforce / dataCost.costTotal) * 100;
    percentIndirectCost = (data[0].cost_indirect_cost / dataCost.costTotal) * 100;
    percentAssignableExpenses = (dataCost.expense / dataCost.costTotal) * 100;

    !isFinite(percentRawMaterial) ? percentRawMaterial = 0 : percentRawMaterial;
    !isFinite(percentWorkforce) ? percentWorkforce = 0 : percentWorkforce;
    !isFinite(percentIndirectCost) ? percentIndirectCost = 0 : percentIndirectCost;
    !isFinite(percentAssignableExpenses) ? percentAssignableExpenses = 0 : percentAssignableExpenses;

    percentRawMaterial >= 0 && percentRawMaterial < 1 ? percentRawMaterial = percentRawMaterial.toFixed(2) : percentRawMaterial = percentRawMaterial.toFixed(0);
    percentWorkforce >= 0 && percentWorkforce < 1 ? percentWorkforce = percentWorkforce.toFixed(2) : percentWorkforce = percentWorkforce.toFixed(0);
    percentIndirectCost >= 0 && percentIndirectCost < 1 ? percentIndirectCost = percentIndirectCost.toFixed(2) : percentIndirectCost = percentIndirectCost.toFixed(0);
    percentAssignableExpenses >= 0 && percentAssignableExpenses < 1 ? percentAssignableExpenses = percentAssignableExpenses.toFixed(2) : percentAssignableExpenses = percentAssignableExpenses.toFixed(0);

    $('#percentRawMaterial').html(`${percentRawMaterial} %`);
    $('#percentWorkforce').html(`${percentWorkforce} %`);
    $('#percentIndirectCost').html(`${percentIndirectCost} %`);
    $('#percentAssignableExpenses').html(`${percentAssignableExpenses} %`);
  };

  /* Ventas */

  UnitsVolSold = (data) => {
    let typePrice = sessionStorage.getItem('typePrice');

    if (typePrice == '1' || !typePrice)
      max = 0
    else {
      max = 2
    }

    $('#unitsSold').html(data[0].units_sold.toLocaleString('es-CO'));
    $('#turnover').html(`$ ${data[0].turnover.toLocaleString('es-CO', { maximumFractionDigits: max })}`);

    let price = parseFloat(data[0].turnover) / parseFloat(data[0].units_sold);
    isNaN(price) ? price = 0 : price;

    let element = document.getElementsByClassName('recomendedPrice');
    
    for (let i = 0; i < element.length; i++) {
      element[i].innerHTML = `$ ${price.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`;

      if (price < data[0].sale_price)
        element[i].className = 'mb-0 text-danger recomendedPrice';
      else if (price == data[0].price)
        element[i].className = 'mb-0 text-warning recomendedPrice';
      else
        element[i].className = 'mb-0 recomendedPrice';
    }
  };

  /* Costeo Total */
  totalCostData = (data) => {
    let typePrice = sessionStorage.getItem('typePrice');

    if (typePrice == '1' || !typePrice)
      max = 0
    else {
      max = 2
    }

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
        maximumFractionDigits: max,
      })}`
    );
    $('#cost').html(
      `$ ${dataCost.cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );
    $('#payRawMaterial').html(
      `$ ${data[0].cost_materials.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );
    $('#payWorkforce').html(
      `$ ${data[0].cost_workforce.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );
    $('#payIndirectCost').html(
      `$ ${data[0].cost_indirect_cost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );

    $('#services').html(
      `$ ${data[0].services.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );

    if (flag_expense == 2)
      $('#expenses').html(`Gastos (${data[0].expense_recover}%)`);

    $('#payAssignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
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
        maximumFractionDigits: max,
      })}`
    );

    $('#actualSalePrice').html(`$ ${data[0].sale_price.toLocaleString('es-CO', {
      minimumFractionDigits: 0,
      maximumFractionDigits: max,
    })}`);

    $('#minProfit').html(`${data[0].profitability.toLocaleString('es-CO', {
      maximumFractionDigits: 2,
    })}%`);
    
    $('#actualProfitability').html(``);

    $('.cardTrafficLight').empty();
    let content = '';
    $('#actualSalePrice').removeClass('text-warning');
    $('#actualSalePrice').removeClass('text-success');
    $('#actualSalePrice').removeClass('text-danger');

    /* if (dataCost.actualProfitability == data[0].profitability) { border-warning 
       else if (dataCost.actualProfitability > data[0].profitability) { border-success */
    if (!isFinite(dataCost.actualProfitability2))
      dataCost.actualProfitability2 = 0;
     
    if (dataCost.actualProfitability3 < data[0].profitability && dataCost.actualProfitability3 > 0 && data[0].sale_price > 0) {
      /*
        <div class="text-center">
                          <span class="text-warning font-weight-bold" style="font-size:large">
                            <i style="font-style: initial;"><i class="bx bxs-no-entry" style="font-size: xxx-large;color:orange"></i></i>
                          </span>
                        </div>
      */
      content = `<div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-warning">${dataCost.actualProfitability3.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
      $('#actualSalePrice').addClass('text-warning');
    }
    else if (dataCost.actualProfitability2 < data[0].profitability && data[0].sale_price > 0) {
      content = `<div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-danger">${dataCost.actualProfitability3.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
      $('#actualSalePrice').addClass('text-danger');
    }
    else {
      /*
      <div class="text-center">
                          <span class="text-success font-weight-bold" style="font-size:large">
                            <i style="font-style: initial;"><i class="bx bxs-check-circle" style="font-size: xxx-large;color:green"></i></i>
                          </span>
                        </div>
       */
      content = `<div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-success">${dataCost.actualProfitability3.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
      $('#actualSalePrice').addClass('text-success');
    }
    
    $('.cardTrafficLight').append(content);

    let price = parseFloat(data[0].turnover) / parseFloat(data[0].units_sold);
    $('.cardRecomendedPrice').empty();
    content = '';

    if (price < data[0].sale_price) {
      content = `<div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Real</span>
                          <h2 class="mb-0 mt-1 text-danger">${dataCost.actualProfitability2.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
    } else if (price == data[0].sale_price) {
      content = `<div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Real</span>
                          <h2 class="mb-0 mt-1 text-warning">${dataCost.actualProfitability2.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
    } else {
      content = `<div class="card radius-10 border-start border-0 border-3">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Real</span>
                          <h2 class="mb-0 mt-1">${dataCost.actualProfitability2.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
      
    }

    $('.cardRecomendedPrice').append(content);
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
  setTimeout(setProduct, 2000);
});
