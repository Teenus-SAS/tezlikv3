/* Tiempo de procesos */
graphicTimeProcessByProduct = (data) => {
  product = [];
  totalTime = [];

  data.length > 10 ? (count = 10) : (count = data.length);
  for (i = 0; i < count; i++) {
    product.push(data[i].product);
    totalTime.push(data[i].totalTime);
  }
  const cmc = document.getElementById('chartTimeProcessProducts');
  const chartTimeProcessProducts = new Chart(cmc, {
    plugins: [ChartDataLabels],
    type: 'bar',
    data: {
      labels: product,
      formatter: function (value, context) {
        return context.chart.data.labels[context.dataIndex];
      },
      datasets: [
        {
          label: product,
          data: totalTime,
          backgroundColor: getRandomColor(count),
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
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
          formatter: (totalTime) => totalTime.toLocaleString(),
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

/* Mano de obra */
graphicWorkforce = (data) => {
  process = [];
  minuteValue = [];
  totalCost = 0;

  for (let i in data) {
    process.push(data[i].process);
    minuteValue.push(data[i].minute_value);
    totalCost = totalCost + minuteValue[i];
  }

  $('#totalCostWorkforce').html(`$ ${totalCost.toFixed(2)}`);

  const cmc = document.getElementById('chartWorkForceGeneral');
  const chartWorkForceGeneral = new Chart(cmc, {
    plugins: [ChartDataLabels],
    type: 'doughnut',
    data: {
      labels: process,
      formatter: function (value, context) {
        return context.chart.data.labels[context.dataIndex];
      },
      datasets: [
        {
          //label: process,
          data: minuteValue,
          backgroundColor: getRandomColor(data.length),
          //borderColor: [],
          borderWidth: 1,
        },
      ],
    },
    options: {
      tooltips: {
        enabled: false,
      },
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

/* Costo carga fabril */

graphicsFactoryLoad = (data) => {
  machine = [];
  costMinute = [];
  totalCostMinute = 0;

  for (let i in data) {
    machine.push(data[i].machine);
    costMinute.push(data[i].totalCostMinute);
    totalCostMinute = totalCostMinute + costMinute[i];
  }

  $('#factoryLoadCost').html(totalCostMinute.toFixed(2));

  const cmc = document.getElementById('chartFactoryLoadCost');
  const chartFactoryLoadCost = new Chart(cmc, {
    plugins: [ChartDataLabels],
    type: 'doughnut',
    data: {
      labels: machine,
      formatter: function (value, context) {
        return context.chart.data.labels[context.dataIndex];
      },
      datasets: [
        {
          data: costMinute,
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

/* Gastos generales */

graphicGeneralCost = (data) => {
  expenseCount = [];
  totalExpense = 0;

  for (i = 0; i < 3; i++) {
    expenseCount.push(data[i].expenseCount);
    totalExpense = totalExpense + data[i].expenseCount;
  }
  $('#totalCost').html(`$ ${totalExpense.toLocaleString('es-ES')}`);

  /* Grafico */
  var cmo = document.getElementById('chartExpensesGenerals');
  var chartExpensesGenerals = new Chart(cmo, {
    plugins: [ChartDataLabels],
    type: 'doughnut',
    data: {
      labels: [
        'Operacionales de administración',
        'Gastos de Ventas',
        'No operacionales',
      ],
      datasets: [
        {
          data: expenseCount,
          backgroundColor: getRandomColor(3),
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

/* Indicadores globales 

graphicProfit = (data) => {
  const cmo = document.getElementById('chartExpensesGenerals');
  const chartExpensesGenerals = new Chart(cmo, {
    type: 'doughnut',
    data: {
      labels: ['Utilidad'],
      datasets: [
        {
          data: [
            data['expenseCount51'],
            data['expenseCount52'],
            data['expenseCount53'],
          ],

          backgroundColor: getRandomColor(3),
          //borderColor: [],
          borderWidth: 1,
        },
        options: {
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
      },
    },
  });
};*/

// Rentabilidad y precio productos
graphicProductCost = (data) => {
  product = [];
  profitability = [];
  price = [];
  cost = [];

  data.length > 10 ? (count = 10) : (count = data.length);

  for (i = 0; i < count; i++) {
    product.push(data[i].product);
    cost[i] = data[i].price / data[i].profitability;
  }

  const cmc = document.getElementById('chartProductsCost');
  const chartProductsCost = new Chart(cmc, {
    plugins: [ChartDataLabels],
    type: 'bar',
    data: {
      labels: product,
      formatter: function (value, context) {
        return context.chart.data.labels[context.dataIndex];
      },
      datasets: [
        {
          data: cost,
          backgroundColor: getRandomColor(count),
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
      //plugins: [ChartDataLabels],
      plugins: {
        legend: {
          display: false,
        },
        datalabels: {
          anchor: 'end',
          formatter: (cost) => cost.toLocaleString(),
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
