$(document).ready(function () {
  var anchura = Math.max(
    document.documentElement.clientWidth,
    window.innerWidth || 0
  );

  anchura <= 480 ? (length = 5) : (length = 10);

  /* Tiempo de procesos */
  graphicTimeProcessByProduct = (data) => {
    let product = [];
    let totalTime = [];

    data.length > length ? (count = length) : (count = data.length);
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
            formatter: (totalTime) => totalTime.toLocaleString('es-CO'),
            color: 'black',
            font: {
              size: '12',
              weight: 'normal',
            },
          },
        },
      },
    });
  };

  /* Mano de obra */
  graphicWorkforce = (data) => {
    let process = [];
    let minuteValue = [];
    let totalCost = 0;

    for (let i in data) {
      process.push(data[i].process);
      minuteValue.push(data[i].minute_value);
      totalCost = totalCost + minuteValue[i];
    }

    $('#totalCostWorkforce').html(
      `$ ${totalCost.toLocaleString('es-CO', {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
      })}`
    );

    const cmc = document.getElementById('chartWorkForceGeneral');

    const chartWorkForce = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: process,
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: minuteValue,
            backgroundColor: getRandomColor(data.length),
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
              let percentage =
                ((value * 100) / sum).toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                }) + '%';
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
    let machine = [];
    let costMinute = [];
    let totalCostMinute = 0;

    for (let i in data) {
      machine.push(data[i].machine);
      costMinute.push(data[i].totalCostMinute);
      totalCostMinute = totalCostMinute + costMinute[i];
    }

    $('#factoryLoadCost').html(
      totalCostMinute.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );

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
              let percentage =
                ((value * 100) / sum).toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                }) + '%';
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
    let expenseCount = [];
    let totalExpense = 0;

    for (i = 0; i < 3; i++) {
      expenseCount.push(data[i].expenseCount);
      totalExpense = totalExpense + data[i].expenseCount;
    }
    $('#totalCost').html(`$ ${totalExpense.toLocaleString('es-ES')}`);

    /* Grafico */
    var canvasExpenses = document.getElementById('chartExpensesGenerals');
    var chartExpensesGenerals = new Chart(canvasExpenses, {
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
        onClick: function (e) {
          var elements = chartExpensesGenerals.getElementsAtEventForMode(
            e,
            'nearest',
            { intersect: true },
            true
          );

          if (elements && elements.length > 0) {
            // Obtener el primer elemento seleccionado
            var activeElement = elements[0];

            var datasetIndex = activeElement.datasetIndex;
            var dataset = chartExpensesGenerals.data.datasets[datasetIndex];

            // Verificar si el conjunto de datos tiene datos
            if (dataset && dataset.data) {
              // Obtener el índice de los datos seleccionados
              var dataIndex = activeElement.index;

              // Verificar si el índice es válido
              if (dataIndex >= 0 && dataIndex < dataset.data.length) {
                // Obtener los datos de la parte seleccionada
                var data = dataset.data[dataIndex];

                // Mostrar los datos de la parte seleccionada
                console.log('Datos seleccionados:', data);
                return; // Salir de la función para evitar el error adicional
              }
            }
          }
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
              let percentage =
                ((value * 100) / sum).toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                }) + '%';
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

  // Rentabilidad y precio productos
  graphicProductCost = (data) => {
    let products = [];
    let product = [];
    let cost = [];

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);
      products.push({
        name: data[i].product,
        cost: dataCost.costProfitability,
      });
    }

    products.sort(function (a, b) {
      return b['cost'] - a['cost'];
    });

    /* Guardar datos para grafica */

    products.length > length ? (count = length) : (count = products.length);

    for (i = 0; i < count; i++) {
      product.push(products[i].name);
      cost.push(products[i].cost);
    }

    const cmc = document.getElementById('chartProductsCost');
    const chartProductsCost = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: product,
        //labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
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
          x: {
            display: false,
          },
        },
        //plugins: [ChartDataLabels],
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: 'end',
            formatter: (cost) =>
              cost.toLocaleString('es-CO', { maximumFractionDigits: 0 }),
            color: 'black',
            font: {
              size: '12',
              weight: 'normal',
            },
          },
        },
      },
    });
  };
});
