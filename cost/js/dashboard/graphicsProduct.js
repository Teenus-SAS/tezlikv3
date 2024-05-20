/* DashboardProducts */
$(document).ready(function () {
  var myChart;
  var chartWorkForce;
  var chartTimeProcess;
  var chartTotalTime;
  var charCompPrice;
  var chartMaterials;

  var anchura = Math.max(
    document.documentElement.clientWidth,
    window.innerWidth || 0
  );

  /* Costo del producto */

  graphicCostExpenses = (data) => {
    let product = [];

    let dataCost = getDataCost(data[0]);
    product.push(
      { name: 'Mano de Obra', cost: data[0].cost_workforce },
      { name: 'Materia Prima', cost: data[0].cost_materials },
      { name: 'Costos Indirectos', cost: data[0].cost_indirect_cost },
      { name: 'Gastos Generales', cost: dataCost.expense }
    );

    product.sort(function (a, b) {
      return b['cost'] - a['cost'];
    });

    let costExpenses = [];
    let nameProduct = [];

    for (i = 0; i < 4; i++) {
      nameProduct.push(product[i].name);
      costExpenses.push(product[i].cost);
    }
    let maxDataValue = Math.max(...costExpenses);
    let minDataValue = Math.min(...costExpenses);
    let valueRange = maxDataValue - minDataValue;

    let step = Math.ceil(valueRange / 10 / 10) * 10;

    let maxYValue = Math.ceil(maxDataValue / step) * step + step;

    isNaN(maxYValue) ? maxYValue = 10 : maxYValue;

    /* Ordenar el array */

    myChart ? myChart.destroy() : myChart;

    ctx = document.getElementById('chartProductCosts').getContext('2d');
    myChart = new Chart(ctx, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: nameProduct,
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
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
            max: maxYValue,
          },
          x: {
            display: false,
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: 'end',
            align: 'top',
            offset: 2,
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

    let typeCurrency = sessionStorage.getItem('typeCurrency');

    // price_usd == '1' && 
    typeCurrency == '2' && plan_cost_price_usd == '1' ? max = 2 : max = 0;

    $('#totalCostWorkforceEsp').html(
      `$ ${totalCost.toLocaleString('es-CO', { maximumFractionDigits: max })}`
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

              let percentage = (value * 100) / sum;
              if (percentage > 3)
                return `${percentage.toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })} %`;
              else return '';
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
              let percentage = (value * 100) / sum;
              if (percentage > 3)
                return `${percentage.toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })} %`;
              else return '';
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
  graphicPromTime = (dataAvTime) => {
    let timeData = [];

    timeData.push(dataAvTime.enlistment_time, dataAvTime.operation_time);

    let total = dataAvTime.enlistment_time + dataAvTime.operation_time;

    $('#manufactPromTime').html(
      `${total.toLocaleString('es-CO', { maximumFractionDigits: 2 })} min`
    );

    chartTotalTime ? chartTotalTime.destroy() : chartTotalTime;

    cmo = document.getElementById('chartManufactTime');
    chartTotalTime = new Chart(cmo, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: ['Total Tiempo Alistamiento', 'Total Tiempo Operacion'],
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
              let percentage = (value * 100) / sum;
              if (percentage > 3)
                return `${percentage.toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })} %`;
              else return '';
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

    let typeCurrency = sessionStorage.getItem('typeCurrency');

    // price_usd == '1' && 
    typeCurrency == '2' && plan_cost_price_usd == '1' ? max = 2 : max = 0;

    $('#totalPricesComp').html(
      `$ ${data[0].price.toLocaleString('es-CO', { maximumFractionDigits: max })}`
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
              let percentage = (value * 100) / sum;
              if (percentage > 3)
                return `${percentage.toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })} %`;
              else return '';
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

    anchura <= 480 ? (length = 5) : (length = data.length);
    data.length > length ? (count = length) : (count = data.length);

    for (i = 0; i < count; i++) {
      material.push(data[i].material);
      totalMaterial.push(data[i].totalCostMaterial);
    }

    if (totalMaterial.length > 1) {
      let maxDataValue = Math.max(...totalMaterial);
      let minDataValue = Math.min(...totalMaterial);
      let valueRange = maxDataValue - minDataValue;

      let step = Math.ceil(valueRange / 10 / 10) * 10;

      maxYValue = Math.ceil(maxDataValue / step) * step + step;
    } else {
      maxYValue = Math.max(...totalMaterial);
    }

    chartMaterials ? chartMaterials.destroy() : chartMaterials;

    cmc = document.getElementById('chartMaterialsCosts').getContext('2d');
    chartMaterials = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: material,
        // formatter: function (value, context) {
        //   return context.chart.data.labels[context.dataIndex];
        // },
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
            max: maxYValue,
          },
          x: {
            display: false,
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: 'end',
            align: 'top',
            offset: 2,
            formatter: (value, ctx) => {
              let sum = 0;
              let dataArr = ctx.chart.data.datasets[0].data;
              dataArr.map((data) => {
                sum += data;
              });
              let percentage = (value * 100) / sum;
              isNaN(percentage) ? (percentage = 0) : percentage;
              return `${percentage.toLocaleString('es-CO', {
                maximumFractionDigits: 2,
              })} %`;
            },
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
