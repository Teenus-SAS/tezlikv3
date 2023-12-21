$(document).ready(function () {     
    let id_historic = sessionStorage.getItem('idHistoric');

    loadIndicatorsProducts = async (id_historic) => {  
        let data = await searchData(`/api/historical/${id_historic}`);
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
        $('#recomendedPrice').html(
            `$ ${data.price.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })}`
        );
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

        $('.cardTrafficLight').empty();
        let content = '';
        $('#actualSalePrice').removeClass('text-warning');
        $('#actualSalePrice').removeClass('text-success');
        $('#actualSalePrice').removeClass('text-danger');
    
        if (dataCost.actualProfitability == data.profitability) {
            content = `<div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-warning">${dataCost.actualProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                        <div class="text-center">
                          <span class="text-warning font-weight-bold" style="font-size:large">
                            <i style="font-style: initial;"><i class="bx bxs-no-entry" style="font-size: xxx-large;color:orange"></i></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>`;
            $('#actualSalePrice').addClass('text-warning');
        }
        else if (dataCost.actualProfitability > data.profitability) {
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
            $('#actualSalePrice').addClass('text-success');
        }
        else {
            content = `<div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="text-muted text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
                          <h2 class="mb-0 mt-1 costProduct text-danger">${dataCost.actualProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2, })} %</h2>
                        </div>
                        <div class="text-center">
                          <span class="text-danger font-weight-bold" style="font-size:large">
                            <i style="font-style: initial;"><i class="bx bxs-x-circle" style="font-size: xxx-large;color:red"></i></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>`;
            $('#actualSalePrice').addClass('text-danger');
        }
    
        $('.cardTrafficLight').append(content);
    };

    loadIndicatorsProducts(id_historic);
});