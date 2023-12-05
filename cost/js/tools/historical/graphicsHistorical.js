/* DashboardProducts */
$(document).ready(function () {
  var myChart; 
 
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
            max:maxYValue,
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
});
