$(document).ready(function () {
  var chartProductsCost;
  var chartMultiproducts;
  var chartTimeProcessProducts;
  var chartWorkForceGeneral;
  var chartFactoryLoadCost;
  var chartExpensesGenerals; 

  var anchura = Math.max(
    document.documentElement.clientWidth,
    window.innerWidth || 0
  );

  anchura <= 480 ? (length = 5) : (length = 10);

  /* Punto de equilibrio */
  graphicMultiproducts = (data) => {
    let percentage = (data.total_units_sold * 100) / data.total_units;

    isNaN(percentage) ? percentage = 0 : percentage;

    $('#percentageMultiproducts').html(`${percentage.toLocaleString("es-CO", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })} %`);
    
    const cmc = document.getElementById("chartMultiproducts").getContext("2d");

    chartMultiproducts ? chartMultiproducts.destroy() : chartMultiproducts;

    chartMultiproducts = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "doughnut",
      data: {
        // labels: ['Vender'],
        // labels: [percentage.toFixed(2) + "% Hecho", (100 - percentage).toFixed(2) + "% Restante"],
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: [data.total_units_sold, data.total_units - data.total_units_sold],
            backgroundColor: getRandomColor(2),
            borderWidth: 15,
          },
        ],
      },
      options: {
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            formatter: function (value, context) {
              return '';
              // if (context.datasetIndex === 0) {
              //   return percentage + "%";
              // } else {
              //   return value;
              // }
            },
            color: "white",
            font: {
              size: "6",
              weight: "bold",
            },
          },
        },
        // maintainAspectRatio: 1,
        // animation: { duration: 2500 },
        // scales: { xAxes: [{ display: !1 }], yAxes: [{ display: !1 }] },
        // legend: { display: !1 },
        // tooltips: { enabled: !1 },
      },
    });
  };

  /* Tiempo de procesos */
  graphicTimeProcessByProduct = (data) => {
    let product = [];
    let totalTime = [];

    data.length > length ? (count = length) : (count = data.length);
    for (i = 0; i < count; i++) {
      product.push(data[i].product);
      totalTime.push(data[i].totalTime);
    }

    let maxDataValue = Math.max(...totalTime);
    let minDataValue = Math.min(...totalTime);
    let valueRange = maxDataValue - minDataValue;

    let step = Math.ceil(valueRange / 10 / 10) * 10;

    let maxYValue = Math.ceil(maxDataValue / step) * step + step;

    isNaN(maxYValue) ? maxYValue = 10 : maxYValue;

    const cmc = document.getElementById("chartTimeProcessProducts");
    chartTimeProcessProducts ? chartTimeProcessProducts.destroy() : chartTimeProcessProducts;

    chartTimeProcessProducts = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "bar",
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
            anchor: "end",
            align: 'top',
            offset: 2,
            formatter: (totalTime) => totalTime.toLocaleString("es-CO"),
            color: "black",
            font: {
              size: "12",
              weight: "normal",
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

    // price_usd == '1' && 
    if (sessionStorage.getItem('typeCurrency') == '2' && plan_cost_price_usd == '1') {
      totalCost = `$ ${totalCost.toLocaleString("es-CO", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 2,
      })} (USD)`;
    } else {
      totalCost = `$ ${totalCost.toLocaleString("es-CO", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`;      
    }

    $("#totalCostWorkforce").html(totalCost);

    const cmc = document.getElementById("chartWorkForceGeneral");
    chartWorkForceGeneral ? chartWorkForceGeneral.destroy() : chartWorkForceGeneral;

    chartWorkForceGeneral = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "doughnut",
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
              let percentage = (value * 100) / sum;
              if (percentage > 3)
                return `${percentage.toLocaleString("es-CO", {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })} %`;
              else return "";
            },
            color: "white",
            font: {
              size: "14",
              weight: "bold",
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

    $("#factoryLoadCost").html(
      totalCostMinute.toLocaleString("es-CO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );

    const cmc = document.getElementById("chartFactoryLoadCost");
    chartFactoryLoadCost ? chartFactoryLoadCost.destroy() : chartFactoryLoadCost;

    chartFactoryLoadCost = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "doughnut",
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
              let percentage = (value * 100) / sum;
              if (percentage > 3)
                return `${percentage.toLocaleString("es-CO", {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })} %`;
              else return "";
            },
            color: "white",
            font: {
              size: "14",
              weight: "bold",
            },
          },
        },
      },
    });
  };

  /* Gastos generales */

  graphicGeneralCost = (data) => {
    let expenseLabel = [];
    let expenseCount = [];
    let totalExpense = 0;

    for (i = 0; i < data.length; i++) {
      expenseLabel.push(data[i].count);
      expenseCount.push(data[i].expenseCount);
      totalExpense = totalExpense + data[i].expenseCount;
    }

    // price_usd == '1' && 
    if (sessionStorage.getItem('typeCurrency') == '2' && plan_cost_price_usd == '1') {
      totalExpense = `$ ${totalExpense.toLocaleString("es-CO", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 2,
      })} (USD)`;
    } else {
      totalExpense = `$ ${totalExpense.toLocaleString("es-CO", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`;      
    }

    $("#totalCost").html(totalExpense);

    /* Grafico */
    var canvasExpenses = document.getElementById("chartExpensesGenerals");
    chartExpensesGenerals ? chartExpensesGenerals.destroy() : chartExpensesGenerals;

    chartExpensesGenerals = new Chart(canvasExpenses, {
      plugins: [ChartDataLabels],
      type: "doughnut",
      data: {
        labels: expenseLabel,
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
          let elements = chartExpensesGenerals.getElementsAtEventForMode(
            e,
            "nearest",
            { intersect: true },
            true
          );

          if (elements && elements.length > 0) {
            let activeElement = elements[0];

            let dataIndex = activeElement.index;
            let data = chartExpensesGenerals.data.datasets[0].data[dataIndex];
            let label = chartExpensesGenerals.data.labels[dataIndex];

            loadModalExpenses(label, data);
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
              let percentage = (value * 100) / sum;
              if (percentage > 3)
                return `${percentage.toLocaleString("es-CO", {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                })} %`;
              else return "";
            },
            color: "white",
            font: {
              size: "14",
              weight: "bold",
            },
          },
        },
      },
    });
  };

  // Funcion para cargar grafica de productos con mayor rentabilidad de acuerdo al precio
  $(document).on("click", ".typePrice", function () {
    let op = this.value;
    let className = this.className;
    typePrice = op;

    // let data = await searchData("/api/dashboardExpensesGenerals");
    if (op == 1 && className.includes("btn-outline-primary")) { // Precio Sugerido
      document.getElementById("sugered").className =
        "btn btn-sm btn-primary typePrice";
      document.getElementById("actual").className =
        "btn btn-sm btn-outline-primary typePrice";
      $(".productTitle").html("Productos con mayor rentabilidad (Sugerida)");
      graphicProductCost(dataDetailsPrices);
    } else if (className.includes("btn-outline-primary")) { // Precio Actual
      document.getElementById("actual").className =
        "btn btn-sm btn-primary typePrice";
      document.getElementById("sugered").className =
        "btn btn-sm btn-outline-primary typePrice";

      $(".productTitle").html("Productos con mayor rentabilidad (Actual)");
      graphicProductActualCost(dataDetailsPrices);
    }
  });

  // Rentabilidad y precio productos (Sugerido)
  graphicProductCost = (data) => {
    let products = [];
    let product = [];
    let cost = [];
    typePrice = '1';

    /* Capturar y ordenar de mayor a menor  */
    if (flag_expense === '1' && flag_expense_distribution === '1')
      data = data.filter((item) => item.units_sold != 0 && item.turnover != 0);

    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);
      products.push({
        name: data[i].product,
        cost: dataCost.costProfitability,
      });
    }

    products.sort(function (a, b) {
      return b["cost"] - a["cost"];
    });

    /* Guardar datos para grafica */

    products.length > length ? (count = length) : (count = products.length);

    for (i = 0; i < count; i++) {
      product.push(products[i].name);
      cost.push(products[i].cost);
    }

    let maxDataValue = Math.max(...cost);
    let minDataValue = Math.min(...cost);
    let valueRange = maxDataValue - minDataValue;

    let step = Math.ceil(valueRange / 10 / 10) * 10;

    let maxYValue = Math.ceil(maxDataValue / step) * step + step;

    isNaN(maxYValue) ? maxYValue = 10 : maxYValue;

    chartProductsCost ? chartProductsCost.destroy() : chartProductsCost;

    const cmc = document.getElementById("chartProductsCost");
    chartProductsCost = new Chart(cmc, {
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
            max: maxYValue,
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
            anchor: "end",
            align: 'top',
            offset: 2,
            formatter: (cost) =>
              cost.toLocaleString("es-CO", { maximumFractionDigits: 0 }),
            color: "black",
            font: {
              size: "10",
              weight: "normal",
            },
          },
        },
      },
    });
  };

  // Rentabilidad y precio productos (Actual)
  graphicProductActualCost = (data) => {
    let products = [];
    let product = [];
    let cost = [];

    if (flag_expense === '1' && flag_expense_distribution === '1')
      data = data.filter((item) => item.units_sold != 0 && item.turnover != 0);

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);

      if (isFinite(dataCost.costActualProfitability) && dataCost.costActualProfitability > 0) {
        products.push({
          name: data[i].product,
          cost: dataCost.costActualProfitability,
        });
      }
    }

    products.sort(function (a, b) {
      return b["cost"] - a["cost"];
    });

    /* Guardar datos para grafica */

    products.length > length ? (count = length) : (count = products.length);

    for (i = 0; i < count; i++) {
      product.push(products[i].name);
      cost.push(products[i].cost);
    }

    let maxDataValue = Math.max(...cost);
    let minDataValue = Math.min(...cost);
    let valueRange = maxDataValue - minDataValue;

    let step = Math.ceil(valueRange / 10 / 10) * 10;

    let maxYValue = Math.ceil(maxDataValue / step) * step + step;

    isNaN(maxYValue) ? maxYValue = 10 : maxYValue;

    chartProductsCost ? chartProductsCost.destroy() : chartProductsCost;

    const cmc = document.getElementById("chartProductsCost");
    chartProductsCost = new Chart(cmc, {
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
            max: maxYValue,
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
            anchor: "end",
            align: 'top',
            offset: 2,
            formatter: (cost) =>
              cost.toLocaleString("es-CO", { maximumFractionDigits: 0 }),
            color: "black",
            font: {
              size: "10",
              weight: "normal",
            },
          },
        },
      },
    });
  };
});
