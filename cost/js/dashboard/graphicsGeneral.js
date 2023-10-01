$(document).ready(function () {
  var chartProductsCost;
  var anchura = Math.max(
    document.documentElement.clientWidth,
    window.innerWidth || 0
  );

  anchura <= 480 ? (length = 5) : (length = 10);

  /* Punto de equilibrio */
  graphicMultiproducts = (data) => {
    /* Grafico */
    // var o = {
    //   // plugins: [ChartDataLabels],
    //   type: 'doughnut',
    //   data: {
    //     labels: ['X Vender', 'T. Unidades'],
    //     // formatter: function (value, context) {
    //     //   return context.chart.data.labels[context.dataIndex];
    //     // },
    //     datasets: [
    //       {
    //         data: [data.total_units_sold, data.total_units],
    //         backgroundColor: getRandomColor(2),
    //         // hoverBackgroundColor: [colors.success, colors.gray300],
    //         borderWidth: 1,
    //       },
    //     ],
    //   },
    //   options: {
    //     tooltips: {
    //       enabled: false,
    //     },
    //     plugins: {
    //       legend: {
    //         display: false,
    //       },
    //     },
    //     cutoutPercentage: 85,
    //     responsive: !0,
    //     maintainAspectRatio: !1,
    //     animation: { duration: 2500 },
    //     scales: { xAxes: [{ display: !1 }], yAxes: [{ display: !1 }] },
    //     legend: { display: !1 },
    //     tooltips: { enabled: !1 },
    //   },
    // };
    // new Chart(document.getElementById('chartMultiproducts'), o);
    const cmc = document.getElementById("chartMultiproducts");

    const chartWorkForce = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: "doughnut",
      data: {
        // labels: ['Vender'],
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: [data.total_units_sold],
            backgroundColor: getRandomColor(1),
            borderWidth: 1,
          },
        ],
      },
      options: {
        cutoutPercentage: 85,
        responsive: true,
        maintainAspectRatio: true,
        animation: { duration: 2500 },
                        
        tooltips: {
          enabled: false,
        },
        
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            formatter: (value, ctx) => {
              let percentage = (value * 100) / data.total_units;

              return `${percentage.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })} %`;
            },
            color: "white",
            font: {
              size: "6",
              //weight: "bold",
            },
          },
        },
        // cutoutPercentage: (data.total_units_sold / data.total_units) * 100,
        /*maintainAspectRatio: !1,
        animation: { duration: 2500 },
        scales: { xAxes: [{ display: !1 }], yAxes: [{ display: !1 }] },
        legend: { display: !1 },
        tooltips: { enabled: !1 }, */
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
    const cmc = document.getElementById("chartTimeProcessProducts");
    const chartTimeProcessProducts = new Chart(cmc, {
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

    $("#totalCostWorkforce").html(
      `$ ${totalCost.toLocaleString("es-CO", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
      })}`
    );

    const cmc = document.getElementById("chartWorkForceGeneral");

    const chartWorkForce = new Chart(cmc, {
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
    const chartFactoryLoadCost = new Chart(cmc, {
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
    $("#totalCost").html(`$ ${totalExpense.toLocaleString("es-ES")}`);

    /* Grafico */
    var canvasExpenses = document.getElementById("chartExpensesGenerals");
    var chartExpensesGenerals = new Chart(canvasExpenses, {
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

  $(document).on("click", ".typePrice", async function () {
    let op = this.value;
    let className = this.className;

    let data = await searchData("/api/dashboardExpensesGenerals");
    if (op == 1 && className.includes("btn-outline-primary")) {
      document.getElementById("sugered").className =
        "btn btn-sm btn-primary typePrice";
      document.getElementById("actual").className =
        "btn btn-sm btn-outline-primary typePrice";
      $(".productTitle").html("Productos con mayor rentabilidad (Sugerida)");
      graphicProductCost(data.details_prices);
    } else if (className.includes("btn-outline-primary")) {
      document.getElementById("actual").className =
        "btn btn-sm btn-primary typePrice";
      document.getElementById("sugered").className =
        "btn btn-sm btn-outline-primary typePrice";

      $(".productTitle").html("Productos con mayor rentabilidad (Actual)");
      graphicProductActualCost(data.details_prices);
    }
  });

  // Rentabilidad y precio productos (Sugerido)
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
      return b["cost"] - a["cost"];
    });

    /* Guardar datos para grafica */

    products.length > length ? (count = length) : (count = products.length);

    for (i = 0; i < count; i++) {
      product.push(products[i].name);
      cost.push(products[i].cost);
    }

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
            formatter: (cost) =>
              cost.toLocaleString("es-CO", { maximumFractionDigits: 0 }),
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
  // Rentabilidad y precio productos (Actual)
  graphicProductActualCost = (data) => {
    let products = [];
    let product = [];
    let cost = [];

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < data.length; i++) {
      let dataCost = getDataCost(data[i]);

      if (isFinite(dataCost.costActualProfitability)) {
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
            formatter: (cost) =>
              cost.toLocaleString("es-CO", { maximumFractionDigits: 0 }),
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
});
