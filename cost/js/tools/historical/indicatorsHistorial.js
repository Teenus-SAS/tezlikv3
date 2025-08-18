$(document).ready(function () {
    id_historic = localStorage.getItem('idHistoric');

    loadIndicatorsProducts = async (id_historic) => {
        let data = await searchData(`/api/historicalData/historical/${id_historic}`);
        await generalIndicators(data);
        await UnitsVolSold(data);
        await totalCostData(data);
        await graphicCostExpenses(data);
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
        $('#nameProduct').html(data.product);

        !data.img
            ? (txt = '')
            : (txt = ` <img src="${data.img}" class="mx-auto d-block" style="width:60px;border-radius:100px"> `);

        $('.imageProduct').html(txt);

        dataCost = getDataCost(data);

        $('#rawMaterial').html(
            `$ ${data.cost_material.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );
        $('#workforce').html(
            `$ ${data.cost_workforce.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );
        $('#indirectCost').html(
            `$ ${data.cost_indirect.toLocaleString('es-CO', {
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

        percentRawMaterial = (data.cost_material / dataCost.costTotal) * 100;
        percentWorkforce = (data.cost_workforce / dataCost.costTotal) * 100;
        percentIndirectCost =
            (data.cost_indirect / dataCost.costTotal) * 100;
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
        $('#unitsSold').html(data.units_sold.toLocaleString('es-CO'));
        $('#turnover').html(`$ ${data.turnover.toLocaleString('es-CO')}`);

        let price = parseFloat(data.turnover) / parseFloat(data.units_sold);
        isNaN(price) ? price = 0 : price;

        $('.recomendedPrice').html(`$ ${price.toLocaleString('es-CO', { maximumFractionDigits: 2 })}`);
    };

    /* Costeo Total */
    totalCostData = (data) => {
        dataCost = getDataCost(data);

        if (data.cost_material == 0) $('.payRawMaterial').hide();
        else $('.payRawMaterial').show();
        if (data.cost_workforce == 0) $('.payWorkforce').hide();
        else $('.payWorkforce').show();
        if (data.cost_indirect == 0) $('.payIndirectCost').hide();
        else $('.payIndirectCost').show();
        if (data.external_services == 0) $('.services').hide();
        else $('.services').show();
        if (dataCost.expense == 0) $('.payAssignableExpenses').hide();
        else $('.payAssignableExpenses').show();
        if (dataCost.costCommissionSale == 0) $('.commission').hide();
        else $('.commission').show();
        if (dataCost.costProfitability == 0) $('.profit').hide();
        else $('.profit').show();
        if (data.sale_price == 0) $('.actualSalePrice').hide();
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
            `$ ${data.cost_material.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );
        $('#payWorkforce').html(
            `$ ${data.cost_workforce.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );
        $('#payIndirectCost').html(
            `$ ${data.cost_indirect.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );

        $('#services').html(
            `$ ${data.external_services.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );

        if (flag_expense == 2)
            $('#expenses').html(`Gastos (${data.expense_recover}%)`);

        $('#payAssignableExpenses').html(
            `$ ${dataCost.expense.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );

        $('#commission').html(
            `Comisión Vts (${data.commision_sale.toLocaleString('es-CO', {
                maximumFractionDigits: 2,
            })}%)`
        );
        $('#commisionSale').html(
            `$ ${Math.round(dataCost.costCommissionSale).toLocaleString('es-CO')}`
        );

        $('#profit').html(
            `Rentabilidad (${data.profitability.toLocaleString('es-CO', {
                maximumFractionDigits: 2,
            })}%)`
        );
        $('#profitability').html(
            `$ ${Math.round(dataCost.costProfitability).toLocaleString('es-CO')}`
        );

        $('.suggestedPrice').html(
            `$ ${data.price.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );

        $('#actualSalePrice').html(`$ ${data.sale_price.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        })}`);

        $('#minProfit').html(`${data.profitability.toLocaleString('es-CO', {
            maximumFractionDigits: 2,
        })}%`);


        $('#actualProfitability').html(``);

        if (data.sale_price > 0) {
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
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentabilidad'} (Lista)</span>
                          <h2 class="mb-0 mt-1 costProduct text-danger">${dataCost.actualProfitability3.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
                $('#actualSalePrice').addClass('text-danger');
            } else if (dataCost.actualProfitability3 < data.profitability) {
                content = `<div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentabilidad'} (Lista)</span>
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
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentabilidad'} (Lista)</span>
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

        if (data.turnover > 0 && data.units_sold > 0) {
            $('.cardRecomendedPrice').empty();
            content = '';

            if (dataCost.actualProfitability2 <= 0) {
                content = `<div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">${id_company == '10' ? 'Margen' : 'Rentabilidad'} (Real)</span>
                          <h2 class="mb-0 mt-1 text-danger">${dataCost.actualProfitability2.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                      </div>
                    </div>
                  </div>`;
                $('#recomendedPrice').addClass('text-danger');

            } else if (dataCost.actualProfitability2 < data.profitability) {
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
    };

    loadIndicatorsProducts(id_historic);
});