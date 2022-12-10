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
    costExpenses.push(data[0].assignable_expense);

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
            //borderColor: [getRandomColor()],
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
            formatter: (costExpenses) => costExpenses.toLocaleString(),
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

  // cambios 17/05/22
  // add totalCost = 0;
  // add totalCost = totalCost + workforce[i];
  // add Intl.NumberFormat
  // add $('#totalCostWorkforceEsp').html(`$ ${totalCost}`);

  graphicCostWorkforce = (data) => {
    let process = [];
    let workforce = [];
    let totalCost = 0;

    for (let i in data) {
      process.push(data[i].process);
      workforce.push(data[i].workforce);
      totalCost = totalCost + workforce[i];
    }

    totalCost = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(totalCost);

    $('#totalCostWorkforceEsp').html(`$ ${totalCost}`);

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
            //borderColor: [],
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
              // return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
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

  // cambios 17/05/22
  // add total = 0;
  // add total = total + totalTime[i];
  // add Intl.NumberFormat
  // add $('#totalTimeProcess').html(`${total} min`);

  graphicCostTimeProcess = (data) => {
    let process = [];
    let totalTime = [];
    let total = 0;

    for (let i in data) {
      process.push(data[i].process);
      totalTime.push(data[i].totalTime);
      total = total + totalTime[i];
    }

    total = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(total);

    $('#totalTimeProcess').html(`${total} min`);

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
            //borderColor: [],
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
              // return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
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
    total = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(total);

    $('#manufactPromTime').html(`${total} min`);

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
            //borderColor: [],
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
              // return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
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
    let total = 0;
    let product = {
      costs:
        data[0].cost_materials +
        data[0].cost_workforce +
        data[0].cost_indirect_cost,
      commSale: (data[0].price * data[0].commission_sale) / 100,
      profitability: (data[0].price * data[0].profitability) / 100,
      assignableExpense: data[0].assignable_expense,
    };

    for (let i in product) {
      total += product[i];
    }

    total = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(total);

    $('#totalPricesComp').html(`$ ${total}`);

    charCompPrice ? charCompPrice.destroy() : charCompPrice;

    cmo = document.getElementById('chartPrice');
    charCompPrice = new Chart(cmo, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: ['Costos', 'Comisión Venta', 'Rentabilidad', 'Gastos'],
        datasets: [
          {
            data: Object.values(product),
            backgroundColor: getRandomColor(5),
            //borderColor: [],
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
              // return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
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
            //borderColor: [],
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
            formatter: (totalMaterial) => totalMaterial.toLocaleString(),
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
