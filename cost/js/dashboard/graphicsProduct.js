/* DashboardProducts */
$(document).ready(function () {
  var myChart;
  var chartWorkForce;
  var chartTimeProcess;
  var chartTotalTime;
  var charCompPrice;
  var chartMaterials;

  /* Costo del producto */

  graphicCostExpenses = (data) => {
    let costExpenses = [];

    costExpenses.push(data[0].cost_workforce);
    costExpenses.push(data[0].cost_materials);
    costExpenses.push(data[0].cost_indirect_cost);

    let dataCost = getDataCost(data[0]);

    costExpenses.push(dataCost.expense);

    /* Ordenar el array */

    myChart ? myChart.destroy() : myChart;

    ctx = document.getElementById('chartProductCosts').getContext('2d');
    myChart = new Chart(ctx, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: [
          'Mano de Obra',
          'Materia Prima',
          'Costos Indirectos',
          'Gastos Generales',
        ],
        datasets: [
          {
            data: costExpenses,
            backgroundColor: getRandomColor(4),
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: 'end',
            formatter: (costExpenses) =>
              costExpenses.toLocaleString('es-CO', {
                maximumFractionDigits: 0,
              }),
            color: 'black',
            font: {
              size: '14',
              weight: 'normal',
            },
          },
        },
      },
    });
  };

  /* Mano de Obra */

  graphicCostWorkforce = (data) => {
    let process = [];
    let workforce = [];
    let totalCost = 0;

    for (let i in data) {
      process.push(data[i].process);
      workforce.push(data[i].workforce);
      totalCost = totalCost + workforce[i];
    }

    $('#totalCostWorkforceEsp').html(
      `$ ${totalCost.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`
    );

    chartWorkForce ? chartWorkForce.destroy() : chartWorkForce;

    cmo = document.getElementById('chartWorkForce');
    chartWorkForce = new Chart(cmo, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: process,
        datasets: [
          {
            data: workforce,
            backgroundColor: getRandomColor(data.length),
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

              sum > 0
                ? (percentage = ((value * 100) / sum).toFixed(2) + '%')
                : (percentage = 0);

              return percentage;
            },
            color: 'white',
            font: {
              size: '14',
              weight: 'bold',
            },
          },
        },
      },
    });
  };

  /* Tiempo de Proceso del producto */

  graphicCostTimeProcess = (data) => {
    let process = [];
    let totalTime = [];
    let total = 0;

    for (let i in data) {
      process.push(data[i].process);
      totalTime.push(data[i].totalTime);
      total = total + totalTime[i];
    }

    $('#totalTimeProcess').html(
      `${total.toLocaleString('es-CO', { maximumFractionDigits: 0 })} min`
    );

    chartTimeProcess ? chartTimeProcess.destroy() : chartWorkForce;

    cmo = document.getElementById('chartTimeProcess');
    chartTimeProcess = new Chart(cmo, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: process,
        datasets: [
          {
            data: totalTime,
            backgroundColor: getRandomColor(data.length),
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
              let percentage = ((value * 100) / sum).toFixed(2) + '%';
              return percentage;
            },
            color: 'white',
            font: {
              size: '14',
              weight: 'bold',
            },
          },
        },
      },
    });
  };

  /* Total Tiempos */
  graphicPromTime = (dataAvTime, dataCostTime) => {
    let timeData = [];

    let totalTime = 0;
    let totalTimeProm = 0;

    for (let i in dataAvTime) {
      totalTimeProm += dataAvTime[i].enlistment_time;
      totalTimeProm += dataAvTime[i].operation_time;
    }

    for (let i in dataCostTime) {
      totalTime += dataCostTime[i].totalTime;
    }
    timeData.push(totalTime);
    timeData.push(totalTimeProm);

    let total = totalTime + totalTimeProm;

    $('#manufactPromTime').html(
      `${total.toLocaleString('es-CO', { maximumFractionDigits: 0 })} min`
    );

    chartTotalTime ? chartTotalTime.destroy() : chartTotalTime;

    cmo = document.getElementById('chartManufactTime');
    chartTotalTime = new Chart(cmo, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: ['Total Tiempo Procesos', 'Total Tiempo Promedio'],
        datasets: [
          {
            data: timeData,
            backgroundColor: getRandomColor(2),
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
              let percentage = ((value * 100) / sum).toFixed(2) + '%';
              return percentage;
            },
            color: 'white',
            font: {
              size: '14',
              weight: 'bold',
            },
          },
        },
      },
    });
  };

  /* Composición Precio */
  graphicCompPrices = (data) => {
    dataCost = getDataCost(data[0]);

    let costs = [
      dataCost.cost,
      dataCost.costCommissionSale,
      dataCost.costProfitability,
      dataCost.expense,
    ];

    let percentages = [
      (dataCost.cost / data[0].price) * 100,
      data[0].commission_sale,
      data[0].profitability,
      data[0].expense_recover,
    ];

    let product = [costs, percentages];

    $('#totalPricesComp').html(
      `$ ${data[0].price.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`
    );

    charCompPrice ? charCompPrice.destroy() : charCompPrice;

    cmo = document.getElementById('chartPrice');
    charCompPrice = new Chart(cmo, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: ['Costos', 'Comisión Venta', 'Rentabilidad', 'Gastos'],
        datasets: [
          {
            // label: product[1],
            data: product[0],
            backgroundColor: getRandomColor(5),
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
              let percentage = ((value * 100) / sum).toFixed(2) + '%';
              return percentage;
            },
            color: 'white',
            font: {
              size: '14',
              weight: 'bold',
            },
          },
        },
      },
    });
  };

  /* Costos de la materia prima */
  graphicCostMaterials = (data) => {
    let material = [];
    let totalMaterial = [];

    for (let i in data) {
      material.push(data[i].material);
      totalMaterial.push(data[i].totalCostMaterial);
    }

    chartMaterials ? chartMaterials.destroy() : chartMaterials;

    cmc = document.getElementById('chartMaterialsCosts').getContext('2d');
    chartMaterials = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: material,
        datasets: [
          {
            data: totalMaterial,
            backgroundColor: getRandomColor(data.length),
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: 'end',
            formatter: (totalMaterial) => totalMaterial.toLocaleString('es-CO'),
            color: 'black',
            font: {
              size: '10',
              weight: 'light',
            },
          },
        },
      },
    });
  };
});
