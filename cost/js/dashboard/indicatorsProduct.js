$(document).ready(function () {
  let id_product = sessionStorage.getItem('idProduct');

  loadIndicatorsProducts = async (id_product) => {
    try {
      const data = await searchData(`/api/panelProducts/${id_product}`);

      sessionStorage.removeItem('imageProduct');
      $('.social-bar').hide(800);
      $('.cardSalePrice').show();
      $('.cardDistribution').show();

      let typeCurrency = '1';
      if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency')) {
        typeCurrency = sessionStorage.getItem('typeCurrency');
      }

      const convertCurrency = (data, rate, priceKey) => {
        const product = data.cost_product[0];
        product.cost_materials /= rate;
        product.cost_workforce /= rate;
        product.cost_indirect_cost /= rate;
        product.services /= rate;
        product.assignable_expense /= rate;
        product.price = parseFloat(product[priceKey]);
        product.sale_price = parseFloat(product[`sale_${priceKey}`]);
        product.turnover /= rate;

        data.cost_workforce.forEach(item => {
          item.workforce /= rate;
        });

        data.cost_materials.forEach(item => {
          item.totalCostMaterial /= rate;
        });
      };

      if (typeCurrency === '2') { // Dolares
        convertCurrency(data, parseFloat(coverage_usd), 'price_usd');
      } else if (typeCurrency === '3') { // Euros
        convertCurrency(data, parseFloat(coverage_eur), 'price_eur');
      }

      await Promise.all([
        generalIndicators(data.cost_product),
        UnitsVolSold(data.cost_product),
        totalCostData(data.cost_product),
        graphicCostExpenses(data.cost_product),
        graphicCostWorkforce(data.cost_workforce),
        graphicCostTimeProcess(data.cost_time_process),
        graphicPromTime(data.average_time_process),
        graphicCompPrices(data.cost_product),
        graphicCostMaterials(data.cost_materials)
      ]);
    } catch (error) {
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
    $('.cardAssExpense').show();
    document.getElementById('cardsIndicatorsProducts').className = 'row row-cols-1 row-cols-md-2 row-cols-xl-4';

    $('#nameProduct').html(data[0].product);

    if (data[0].img) {
      $('.social-bar').show(800);
      sessionStorage.setItem('imageProduct', data[0].img);
    }

    dataCost = getDataCost(data[0]);

    let typeCurrency = '1';

    if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency'))
      typeCurrency = sessionStorage.getItem('typeCurrency');

    // price_usd == '0' || 

    if (typeCurrency == '1')
      max = 0;
    else {
      max = 2;
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

    // if (data[0].composite == '1' && flag_composite_product == '1') {
    //   $('.cardAssExpense').hide();

    //   document.getElementById('cardsIndicatorsProducts').className = 'row row-cols-1 row-cols-md-2 row-cols-xl-3';
    // }

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
    let typeCurrency = '1';

    if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency'))
      typeCurrency = sessionStorage.getItem('typeCurrency');

    // price_usd == '0' || 
    if (typeCurrency == '1')
      max = 0;
    else {
      max = 2;
    }

    $('#unitsSold').html(data[0].units_sold.toLocaleString('es-CO'));
    $('#turnover').html(`$ ${data[0].turnover.toLocaleString('es-CO', { maximumFractionDigits: max })}`);
    let price = parseFloat(data[0].turnover) / parseFloat(data[0].units_sold);
    isNaN(price) ? price = 0 : price;

    $('.recomendedPrice').html(`$ ${price.toLocaleString('es-CO', { maximumFractionDigits: max })}`);
  };

  /* Costeo Total */
  totalCostData = (data) => {
    let typeCurrency = '1';

    if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency'))
      typeCurrency = sessionStorage.getItem('typeCurrency');

    // price_usd == '0' || 
    if (typeCurrency == '1')
      max = 0;
    else {
      max = 2;
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
    if (parseFloat(data[0].sale_price) == 0 && parseFloat(data[0].turnover) == 0 && parseFloat(data[0].units_sold) == 0) $('.actualSalePrice').hide();
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

    if (flag_expense == '2') {
      $('#expenses').html(`
        Gastos (<span id="expenseRecoverDisplay" style="cursor:pointer; color:#007bff;" data-value=${parseFloat(data[0].expense_recover)} data-change="0">
                  ${parseFloat(data[0].expense_recover).toFixed(2)}%
                </span>)
              `);
    }

    $('#payAssignableExpenses').html(
      `$ ${dataCost.expense.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: max, })}`
    );

    $('#commission').html(`
      Comisión Vtas (<span id="commissionDisplay" style="cursor:pointer; color:#007bff;" data-value="${parseFloat(data[0].commission_sale)}" data-change="0">
        ${parseFloat(data[0].commission_sale).toLocaleString('es-CO', { maximumFractionDigits: 2 })}%
      </span>)
    `);
    $('#commisionSale').html(
      `$ ${Math.round(dataCost.costCommissionSale).toLocaleString('es-CO')}`
    );

    if (id_company == '10')
      $('#profit').html(
        `Rentabilidad (${parseFloat(data[0].profitability).toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })}%)`
      );
    else
      $('#profit').html(
        `Rentabilidad (<span id="profitDisplay" style="cursor:pointer; color:#007bff;" data-value="${parseFloat(data[0].profitability)}" data-change="0">
          ${parseFloat(data[0].profitability).toLocaleString('es-CO', { maximumFractionDigits: 2, })}%
        </span>)`
      );

    $('#profitability').html(
      `$ ${Math.round(dataCost.costProfitability).toLocaleString('es-CO')}`
    );

    $('.suggestedPrice').html(
      `$ ${parseFloat(data[0].price).toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: max,
      })}`
    );

    $('#actualSalePrice').html(`$ ${parseFloat(data[0].sale_price).toLocaleString('es-CO', {
      minimumFractionDigits: 0,
      maximumFractionDigits: max,
    })}`);

    $('#minProfit').html(`${data[0].profitability.toLocaleString('es-CO', {
      maximumFractionDigits: 2,
    })}%`);

    $('#actualProfitability').html(``);

    if (data[0].sale_price > 0) {
      $('.cardTrafficLight').empty();
      let content = '';
      document.getElementById('recomendedPrice').className = 'mb-0 recomendedPrice mt-1';
      document.getElementById('actualSalePrice').className = 'mb-0 mt-1';

      if (!isFinite(dataCost.actualProfitability2))
        dataCost.actualProfitability2 = 0;

      // if (flag_expense != '2') {
      if (dataCost.actualProfitability3 <= 0) {
        content = `<div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentab'} (Lista)</span>
                          <h2 class="mb-0 mt-1 costProduct text-danger">${dataCost.actualProfitability3.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
        $('#actualSalePrice').addClass('text-danger');
      } else if (dataCost.actualProfitability3 < data[0].profitability /* dataCost.actualProfitability3 >= 1*/) {
        content = `<div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentab'} (Lista)</span>
                          <h2 class="mb-0 mt-1 costProduct text-warning">${dataCost.actualProfitability3.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
        $('#actualSalePrice').addClass('text-warning');
      } else {
        content = `<div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentab'} (Lista)</span>
                          <h2 class="mb-0 mt-1 costProduct text-success">${dataCost.actualProfitability3.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
        $('#actualSalePrice').addClass('text-success');
      }

      $('.cardTrafficLight').append(content);
    } else {
      $('.cardSalePrice').hide();
    }

    if (data[0].turnover > 0 && data[0].units_sold > 0) {
      $('.cardRecomendedPrice').empty();
      content = '';

      if (dataCost.actualProfitability2 <= 0) {
        content = `<div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentab'} (Real)</span>
                          <h2 class="mb-0 mt-1 text-danger">${dataCost.actualProfitability2.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
        $('#recomendedPrice').addClass('text-danger');

      } else if (dataCost.actualProfitability2 < data[0].profitability /*|| dataCost.actualProfitability2 >= 1*/) {
        content = `<div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentabilidad'} (Real)</span>
                          <h2 class="mb-0 mt-1 text-warning">${dataCost.actualProfitability2.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
        $('#recomendedPrice').addClass('text-warning');
      } else {
        content = `<div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentabilidad'} (Real)</span>
                          <h2 class="mb-0 mt-1 text-success">${dataCost.actualProfitability2.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
        $('#recomendedPrice').addClass('text-success');
      }

      $('.cardRecomendedPrice').append(content);
    }
    else {
      $('.cardDistribution').hide();
    }
    // };
  };

  loadIndicatorsProducts(id_product);

  $('#product').change(function (e) {
    e.preventDefault();

    id_product = this.value;

    sessionStorage.setItem('idProduct', id_product);
    loadIndicatorsProducts(id_product);
  });

  function setProduct() {
    $(`#product option[value=${id_product}]`).prop('selected', true);
  }
  setTimeout(setProduct, 2000);

  $('#imageProduct').click(function (e) {
    e.preventDefault();

    let img = sessionStorage.getItem('imageProduct');

    bootbox.alert(`<img src="${img}" class="mx-auto d-block" style="width: 500px;">`);
  });
});
