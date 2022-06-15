/* DashboardProducts */
$(document).ready(function () {
  /* Costo del producto */

  graphicCostExpenses = (data) => {
    costExpenses = [];

    costExpenses.push(data[0].cost_workforce);
    costExpenses.push(data[0].cost_materials);
    costExpenses.push(data[0].cost_indirect_cost);
    costExpenses.push(data[0].assignable_expense);

    /* Ordenar el array */
    //costExpenses.sort();

    const ctx = document.getElementById('chartProductCosts').getContext('2d');
    const myChart = new Chart(ctx, {
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
    process = [];
    workforce = [];
    totalCost = 0;

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

    const cmo = document.getElementById('chartWorkForce');
    const chartWorkForce = new Chart(cmo, {
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
              return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
            },
            color: 'black',
            font: {
              size: '11',
              weight: 'normal',
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
    process = [];
    totalTime = [];
    total = 0;

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

    var cmo = document.getElementById('chartTimeProcess');
    var chartWorkForce = new Chart(cmo, {
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
              return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
            },
            color: 'black',
            font: {
              size: '11',
              weight: 'normal',
            },
          },
        },
      },
    });
  };

  /* Total Tiempos */

  graphicPromTime = (dataAvTime, dataCostTime) => {
    timeData = [];

    totalTime = 0;
    totalTimeProm = 0;

    for (let i in dataAvTime) {
      totalTimeProm += dataAvTime[i].enlistment_time;
      totalTimeProm += dataAvTime[i].operation_time;
    }

    for (let i in dataCostTime) {
      totalTime += dataCostTime[i].totalTime;
    }
    timeData.push(totalTime);
    timeData.push(totalTimeProm);

    total = totalTime + totalTimeProm;
    total = new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(total);

    $('#manufactPromTime').html(`${total} min`);

    var cmo = document.getElementById('chartManufactTime');
    var chartTotalTime = new Chart(cmo, {
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
              return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
            },
            color: 'black',
            font: {
              size: '11',
              weight: 'normal',
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

    var cmo = document.getElementById('chartPrice');
    var charCompPrice = new Chart(cmo, {
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
              return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage;
            },
            color: 'black',
            font: {
              size: '11',
              weight: 'normal',
            },
          },
        },
      },
    });
  };

  /* Costos de la materia prima */

  graphicCostMaterials = (data) => {
    material = [];
    totalMaterial = [];

    for (let i in data) {
      material.push(data[i].material);
      totalMaterial.push(data[i].totalCostMaterial);
    }

    const cmc = document.getElementById('chartMaterialsCosts').getContext('2d');
    const chartMaterials = new Chart(cmc, {
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
