$(document).ready(function () {
  var chartGeneralDashboard;

  /* Rentabilidad actual */
  $(document).on('click', '.cardActualProfitability', function () {
    $('#generalDashboardName').html('');
    $('.cardExpenseByCount').hide();

    let products = [];
    let product = [];
    let profitability = [];
    let data = dataDetailsPrices;

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);

      if (isFinite(dataCost.actualProfitability)) {
        products.push({
          name: data[i].product,
          profitability: dataCost.actualProfitability,
        });
      }
    }

    products.sort(function (a, b) {
      return b["profitability"] - a["profitability"];
    });

    /* Guardar datos para grafica */

    products.length > length ? (count = length) : (count = products.length);

    for (i = 0; i < count; i++) {
      product.push(products[i].name);
      profitability.push(products[i].profitability);
    }

    chartGeneralDashboard ? chartGeneralDashboard.destroy() : chartGeneralDashboard;

    const cmc = document.getElementById("chartGeneralDashboard");
    chartGeneralDashboard = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "bar",
      data: {
        labels: product,
        //labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: profitability,
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
          },
        },
        //plugins: [ChartDataLabels],
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: "end",
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
    
    $('#generalDashboardName').html(`Rentabilidad Actual (Porcentaje)`);
    $('#modalGeneralDashboard').modal('show'); 
  });

  /* Productos con mayor rentabilidad */
  $('#btnGraphicProducts').click(function (e) {
    e.preventDefault();

    $('#generalDashboardName').html('');
    $('.cardExpenseByCount').hide();

    let products = [];
    let product = [];
    let profitability = [];
    let data = dataDetailsPrices;

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);

      if (typePrice == '1')
        products.push({
          name: data[i].product,
          profitability: data[i].profitability,
        });
      else {
        if (isFinite(dataCost.actualProfitability)) {
          products.push({
            name: data[i].product,
            profitability: dataCost.actualProfitability,
          });
        }
      }
    }

    products.sort(function (a, b) {
      return b["profitability"] - a["profitability"];
    });

    /* Guardar datos para grafica */

    products.length > length ? (count = length) : (count = products.length);

    for (i = 0; i < count; i++) {
      product.push(products[i].name);
      profitability.push(products[i].profitability);
    }

    chartGeneralDashboard ? chartGeneralDashboard.destroy() : chartGeneralDashboard;

    const cmc = document.getElementById("chartGeneralDashboard");
    chartGeneralDashboard = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "bar",
      data: {
        labels: product,
        //labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: profitability,
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
          },
        },
        //plugins: [ChartDataLabels],
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: "end",
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
    
    $('#generalDashboardName').html(`Productos con mayor rentabilidad (${typePrice == '1' ? 'Sugerida' : 'Actual'})`);
    $('#modalGeneralDashboard').modal('show');
  });
  
  /* Grafico Gastos */
  loadModalExpenses = (label, value) => {
    $('#generalDashboardName').html('');
    $('.cardExpenseByCount').show();
    $('#totalExpenseByCount').html(`$ ${value.toLocaleString('es-ES')}`);

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
    /*switch (label) {
      case 'Operacionales de administración':
        puc = '51';
        break;

      case 'Gastos de Ventas':
        puc = '52';
        break;
      case 'No operacionales':
        puc = '53';
        break;
    } */

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
    $('#modalGeneralDashboard').modal('show');
  };


});
