$(document).ready(function () {
  setTimeout(() => { 
    fetch(`/api/dashboardExpensesGenerals`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        generalIndicators(
          data.expense_value,
          data.expense_recover,
          data.multiproducts
        );
        averagePrices(data.details_prices);
        generalSales(data.details_prices);
        if (cost_multiproduct == 1 && plan_cost_multiproduct == 1)
          graphicMultiproducts(data.multiproducts);
        graphicTimeProcessByProduct(data.time_process);
        averagesTime(data.average_time_process);
        graphicsFactoryLoad(data.factory_load_minute_value);
        graphicWorkforce(data.process_minute_value);
        graphicGeneralCost(data.expense_value);
        graphicProductCost(data.details_prices);
        generalMaterials(data.quantity_materials);
  
        dataPucExpenes = data.expenses;
  
        dataExpenses = data.expense_value;
        dataDetailsPrices = data.details_prices;
      });
  }, 2000);

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

  /* Cantidad de materias primas */
  generalMaterials = (data) => {
    $('#materials').html(data.materials.toLocaleString('es-CO'));
  };

  /* Indicadores Generales */
  generalIndicators = (data, expenseRecover, multiproducts) => {
    // Cantidad de productos
    $('#products').html(data[0].products.toLocaleString('es-CO'));

    isNaN(multiproducts.total_units) ? total_units = 0 : total_units = multiproducts.total_units;

    $('#multiproducts').html(
      total_units.toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })
    );

    /* Gastos generales */
    let totalExpense = 0;

    if (flag_expense == 1 || flag_expense == 0) {
      for (i = 0; i < data.length; i++) {
        totalExpense = totalExpense + data[i].expenseCount;
      }
      totalExpense = `$ ${totalExpense.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })}`;
      expenses = 'Gastos Generales';
    } else {
      expenses = `Gtos Generales`;
      totalExpense = `${expenseRecover.percentageExpense.toLocaleString(
        'es-CO',
        {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        }
      )} %`;
    }
    $('#expenses').html(expenses);
    $('#generalCost').html(totalExpense);
  };

  /* Promedio rentabilidad y comision */
  averagePrices = (data) => {
    let profitability = 0;
    let commissionSale = 0;
    let actualProfitability = 0;
    let contProfitability = 0;
    
    if (data.length > 0) {
      for (let i in data) {
        profitability = profitability + data[i].profitability;
        commissionSale = commissionSale + data[i].commission_sale;

        let dataCost = getDataCost(data[i]);
        if (isFinite(dataCost.actualProfitability)) {
          contProfitability += 1;
          actualProfitability += dataCost.actualProfitability;
        }
      }
      
      let averageprofitability = profitability / data.length;
      let averagecommissionSale = commissionSale / data.length;
      let averageActualProfitability = actualProfitability / contProfitability;

      isNaN(averageActualProfitability) ? averageActualProfitability = 0 : averageActualProfitability; 

      let cardActualProfitability = document.getElementsByClassName('cardActualProfitability')[0];

      cardActualProfitability.insertAdjacentHTML('afterbegin',
      `<div class="card ${averageActualProfitability < 0 ? 'bg-danger':'bg-success'}">
        <a class="card-body" id="btnActualProfitabilityAverage" href="javascript:;">
          <div class="media text-white">
            <div class="media-body">
              <span class="text-uppercase font-size-12 font-weight-bold">Rentabilidad Actual</span>
              <h2 class="mb-0 mt-1 text-white">${averageActualProfitability.toLocaleString('es-CO', {maximumFractionDigits: 2})} %</h2>
            </div>
            <div class="align-self-center mt-1">
              <i class="bx bx-line-chart fs-xl"></i>
            </div>
          </div>
        </a>
      </div>`);


      $('#profitabilityAverage').html(
        `${averageprofitability.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} %`
      );
      $('#comissionAverage').html(
        `${averagecommissionSale.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} %`
      );
    } else {
      $('#profitabilityAverage').html(`0 %`);
      $('#comissionAverage').html(`0 %`);
    }
  };

  /* Tiempos promedio */
  averagesTime = (data) => {
    let enlistmentTime = 0;
    let operationTime = 0;

    if (data.length > 0) {
      for (let i in data) {
        enlistmentTime = enlistmentTime + data[i].enlistment_time;
        operationTime = operationTime + data[i].operation_time;
      }

      let averageEnlistment = enlistmentTime / data.length;
      let averageOperation = operationTime / data.length;
      let averageTotal = averageEnlistment + averageOperation;

      $('#enlistmentTime').html(
        `${averageEnlistment.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} min`
      );
      $('#operationTime').html(
        `${averageOperation.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} min`
      );
      $('#averageTotalTime').html(
        `${averageTotal.toLocaleString('es-CO', {
          maximumFractionDigits: 2,
        })} min`
      );
    } else {
      $('#enlistmentTime').html(`0 min`);
      $('#operationTime').html(`0 min`);
    }
  };

  /* Ventas generales */
  generalSales = (data) => {
    $('#productsSold').html(data[0].units_sold.toLocaleString('es-CO'));
    $('#salesRevenue').html(`$ ${data[0].turnover.toLocaleString('es-CO')}`);
  }; 

  loadContract = async () => {
    let data = await searchData('/api/contracts');

    if (!data.date_contract)
      bootbox.confirm({
        title: 'Contrato de Prestación de Servicios',
        message: data.content,
        buttons: {
          confirm: {
            label: 'Aceptar',
            className: 'btn-success',
          },
          cancel: {
            label: 'Cancelar',
            className: 'btn-danger',
          },
        },
        callback: function (result) {
          if (result == true) {
            $.get(
              `/api/changeDateContract`,
              function (data, textStatus, jqXHR) {
                if (data.success == true) {
                  toastr.success(data.message);
                  return false;
                } else if (data.error == true) toastr.error(data.message);
                else if (data.info == true) toastr.info(data.message);
              }
            );
          }
        },
      }).find('div.modal-content').addClass('confirmWidth');
  };

  if (contract == '1') loadContract();
});
