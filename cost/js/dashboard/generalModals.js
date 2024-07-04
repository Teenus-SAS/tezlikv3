$(document).ready(function () {
  var chartGeneralDashboard;

  /* Rentabilidad actual */
  $(document).on('click', '#btnActualProfitabilityAverage', function () {
    $('#generalDashboardName').html('');
    $('.cardGeneralDashboard').empty();
    $('.cardGeneralDashboard').append(`<div class="chart-container"> 
                                          <a href="javascript:;" 
                                            <i id="loadPreviousButton" class="bi bi-arrow-left-square-fill" data-toggle='tooltip' title='Anterior' style="color: black;"></i>
                                          </a>
                                          <a href="javascript:;" 
                                            <i id="loadNextButton" class="bi bi-arrow-right-square-fill" data-toggle='tooltip' title='Siguiente' style="color: black;"></i>
                                          </a>
                                          <canvas id="chartGeneralDashboard"></canvas>
                                      </div>`);

    let products = [];
    let product = [];
    let profitability = [];
    let data = dataDetailsPrices;

    data = data.filter((item) => item.profitability > 0);

    if (flag_expense === '1' && flag_expense_distribution === '1')
      data = data.filter((item) => item.units_sold != 0 && item.turnover != 0);

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);

      isFinite(dataCost.actualProfitability2) ? actualProfitability = dataCost.actualProfitability2 : actualProfitability = 0;

      products.push({
        name: `${data[i].reference} - ${data[i].product}`,
        profitability: actualProfitability,
      });
    }

    products.sort(function (a, b) {
      return b["profitability"] - a["profitability"];
    });

    /* Guardar datos para grafica */
    for (i = 0; i < products.length; i++) {
      product.push(products[i].name);
      profitability.push(products[i].profitability);
    }
    const numToShow = 10;
    let startIndex = 0;

    let maxDataValue = Math.max(...profitability);
    let minDataValue = Math.min(...profitability);
    let valueRange = maxDataValue;

    let maxYValue;

    (maxDataValue - minDataValue) != 0 ? valueRange = maxDataValue - minDataValue : valueRange;

    if (Math.abs(valueRange) < 1) {
      maxYValue = 1;
    } else {
      let step = Math.ceil(valueRange / 10 / 10) * 10;

      maxYValue = Math.ceil(maxDataValue / step) * step + step;

      isNaN(maxYValue) ? maxYValue = 10 : maxYValue;
    } 

    chartGeneralDashboard ? chartGeneralDashboard.destroy() : chartGeneralDashboard;

    const cmc = document.getElementById("chartGeneralDashboard");
    chartGeneralDashboard = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "bar",
      data: {
        labels: product.slice(startIndex, startIndex + numToShow), 
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          { 
            data: profitability.slice(startIndex, startIndex + numToShow),
            backgroundColor: getRandomColor(numToShow),
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          x: {
            stacked: true,
            display: false,
          },
          y: {
            max: maxYValue,
            stacked: true,
          },
        }, 
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: "end",
            align: 'top',
            offset: 2,
            formatter: (profitability) => `${profitability.toLocaleString("es-CO", { maximumFractionDigits: 2 })} %`,
            color: "black",
            font: {
              size: "12",
              weight: "normal",
            },
          },
        },
      },
    });

    // Función para cargar los datos anteriores
    function loadPreviousData() {
      if (startIndex > 0) {
        startIndex -= numToShow;
        updateChartData();
      }
    }

    // Función para cargar los datos siguientes
    function loadNextData() {
      if (startIndex + numToShow < product.length) {
        startIndex += numToShow;
        updateChartData();
      }
    }

    function updateChartData() {
      chartGeneralDashboard.data.labels = product.slice(startIndex, startIndex + numToShow);
      chartGeneralDashboard.data.datasets[0].data = profitability.slice(startIndex, startIndex + numToShow);
      chartGeneralDashboard.data.datasets[0].backgroundColor = getRandomColor(numToShow);
      chartGeneralDashboard.update();
    }

    const loadPreviousButton = document.getElementById("loadPreviousButton");
    const loadNextButton = document.getElementById("loadNextButton");

    loadPreviousButton.addEventListener("click", loadPreviousData);
    loadNextButton.addEventListener("click", loadNextData);

    loadPreviousButton.disabled = startIndex === 0;
    loadNextButton.disabled = startIndex + numToShow >= product.length;

    $('#generalDashboardName').html(`${id_company == '10' ? 'Margen' : 'Rentabilidad'} Actual (Porcentaje)`);
    document.getElementById('modalGHeader').className = 'modal-dialog modal-xl';
    $('#modalGeneralDashboard').modal('show');
  });

  /* Productos con mayor rentabilidad */
  $('#btnGraphicProducts').click(function (e) {
    e.preventDefault();

    $('#generalDashboardName').html('');
    $('.cardGeneralDashboard').empty();
    $('.cardGeneralDashboard').append(`<div class="chart-container"> 
                                          <a href="javascript:;" 
                                            <i id="loadPreviousButton" class="bi bi-arrow-left-square-fill" data-toggle='tooltip' title='Anterior' style="color: black;"></i>
                                          </a>
                                          <a href="javascript:;" 
                                            <i id="loadNextButton" class="bi bi-arrow-right-square-fill" data-toggle='tooltip' title='Siguiente' style="color: black;"></i>
                                          </a>
                                          <canvas id="chartGeneralDashboard"></canvas>
                                      </div>`);

    let products = [];
    let product = [];
    let cost = [];
    let data = dataDetailsPrices;

    data = data.filter((item) => item.profitability > 0);

    if (flag_expense === '1' && flag_expense_distribution === '1')
      data = data.filter((item) => item.units_sold != 0 && item.turnover != 0);

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);

      if (typePrice == '1')
        products.push({
          name: `${data[i].reference} - ${data[i].product}`,
          cost: dataCost.costProfitability,
        });
      else {
        isFinite(dataCost.costActualProfitability) ? costActualProfitability = dataCost.costActualProfitability : costActualProfitability = 0;

        products.push({
          name: `${data[i].reference} - ${data[i].product}`,
          cost: costActualProfitability,
        });
        
      }
    }

    products.sort(function (a, b) {
      return b["cost"] - a["cost"];
    });

    /* Guardar datos para grafica */

    for (i = 0; i < products.length; i++) {
      product.push(products[i].name);
      cost.push(products[i].cost);
    }
    const numToShow = 10;
    let startIndex = 0;

    let maxDataValue = Math.max(...cost);
    let minDataValue = Math.min(...cost);
    let valueRange = maxDataValue;

    let maxYValue;

    (maxDataValue - minDataValue) != 0 ? valueRange = maxDataValue - minDataValue : valueRange;

    if (Math.abs(valueRange) < 1) {
      maxYValue = 1;
    } else {
      let step = Math.ceil(valueRange / 10 / 10) * 10;

      maxYValue = Math.ceil(maxDataValue / step) * step + step;

      isNaN(maxYValue) ? maxYValue = 10 : maxYValue;
    } 

    chartGeneralDashboard ? chartGeneralDashboard.destroy() : chartGeneralDashboard;

    const cmc = document.getElementById("chartGeneralDashboard");
    chartGeneralDashboard = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "bar",
      data: {
        labels: product.slice(startIndex, startIndex + numToShow),
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: cost.slice(startIndex, startIndex + numToShow),
            backgroundColor: getRandomColor(count),
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          x: {
            stacked: true,
            display: false,
          },
          y: {
            stacked: true,
            max: maxYValue,
          },
        }, 
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: "end",
            align: 'top',
            offset: 2,
            formatter: (profitability) =>
              profitability.toLocaleString("es-CO", { maximumFractionDigits: 2 }),
            color: "black",
            font: {
              size: "12",
              weight: "normal",
            },
          },
        },
      },
    });
    
    // Función para cargar los datos anteriores
    function loadPreviousData() {
      if (startIndex > 0) {
        startIndex -= numToShow;
        updateChartData();
      }
    }

    // Función para cargar los datos siguientes
    function loadNextData() {
      if (startIndex + numToShow < product.length) {
        startIndex += numToShow;
        updateChartData();
      }
    }

    function updateChartData() {
      chartGeneralDashboard.data.labels = product.slice(startIndex, startIndex + numToShow);
      chartGeneralDashboard.data.datasets[0].data = cost.slice(startIndex, startIndex + numToShow);
      chartGeneralDashboard.data.datasets[0].backgroundColor = getRandomColor(numToShow);
      chartGeneralDashboard.update();
    }

    const loadPreviousButton = document.getElementById("loadPreviousButton");
    const loadNextButton = document.getElementById("loadNextButton");

    loadPreviousButton.addEventListener("click", loadPreviousData);
    loadNextButton.addEventListener("click", loadNextData);

    loadPreviousButton.disabled = startIndex === 0;
    loadNextButton.disabled = startIndex + numToShow >= product.length;


    $('#generalDashboardName').html(`Productos con mayor rentabilidad (${typePrice == '1' ? 'Sugerida' : 'Actual'})`);
    document.getElementById('modalGHeader').className = 'modal-dialog modal-xl';

    $('#modalGeneralDashboard').modal('show');
  });
  
  /* Grafico Gastos */
  loadModalExpenses = (label, value) => { 
    $('#generalDashboardName').html('');
    $('.cardGeneralDashboard').empty();

    let typeCurrency = '1';
    
    if(flag_currency_usd == '1' || flag_currency_eur == '1')
      typeCurrency = sessionStorage.getItem('typeCurrency');
 
    switch (typeCurrency) {
      case '2': // Dolares
        value = `$ ${value.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (USD)`;
        break;
    
      case '3': // Euros
        value = `$ ${value.toLocaleString('es-CO', { maximumFractionDigits: 2 })} (EUR)`;
        break;
    
      default:// Pesos COP
        value = `$ ${value.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`;
        break;
    } 

    $('.cardGeneralDashboard').append(`<div class="chart-container">
                                          <canvas id="chartGeneralDashboard"></canvas> 
                                          <div class="center-text cardExpenseByCount">
                                              <p class="text-muted mb-1 font-weight-600">Total Gasto </p>
                                              <h4 class="mb-0 font-weight-bold">${value}</h4>
                                          </div>
                                      </div>`);

    let expenses = [];
    let expense = [];
    let expense_value = [];
    let puc;

    for (let i = 0; i < dataExpenses.length; i++) {
      if (dataExpenses[i].count == label) {
        puc = dataExpenses[i].number_count;
        break;
      }
    } 

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < dataPucExpenes.length; i++) {
      let number_count = dataPucExpenes[i].number_count.toString();

      if (number_count.startsWith(puc))
        expenses.push({
          number_count: `N° - ${dataPucExpenes[i].number_count} (${dataPucExpenes[i].count})`,
          expense_value: dataPucExpenes[i].expense_value,
        });
    }

    expenses.sort(function (a, b) {
      return b['expense_value'] - a['expense_value'];
    });

    /* Guardar datos para grafica */

    expenses.length > length ? (count = length) : (count = expenses.length);

    for (i = 0; i < count; i++) {
      expense.push(expenses[i].number_count);
      expense_value.push(expenses[i].expense_value);
    }

    const cmc = document.getElementById('chartGeneralDashboard');

    chartGeneralDashboard ? chartGeneralDashboard.destroy() : chartGeneralDashboard;

    chartGeneralDashboard = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: expense,
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: expense_value,
            backgroundColor: getRandomColor(count),
            borderWidth: 1,
          },
        ],
      },
      options: {
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            formatter: (value, ctx) => {
              let sum = 0;
              let dataArr = ctx.chart.data.datasets[0].data;
              dataArr.map((data) => {
                sum += data;
              });
              let percentage =
                ((value * 100) / sum).toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                }) + '%';
              return percentage;
            },
            color: 'white',
            font: {
              size: '12',
              weight: 'normal',
            },
          },
        },
      },
    });

    $('#generalDashboardName').html(`${puc} - ${label}`);
    document.getElementById('modalGHeader').className = 'modal-dialog';
    $('#modalGeneralDashboard').modal('show');
  };
});
