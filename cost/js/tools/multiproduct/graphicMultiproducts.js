$(document).ready(function () {
  var chartMultiproductsDonut;
  var chartMultiproductsBar;

  dynamicColors = () => {
    let letters = '0123456789ABCDEF'.split('');
    let color = '#';

    for (var i = 0; i < 6; i++)
      color += letters[Math.floor(Math.random() * 16)];
    return color;
  };

  getRandomColor = (a) => {
    let color = [];
    for (i = 0; i < a; i++) color.push(dynamicColors());
    return color;
  };

  var anchura = Math.max(
    document.documentElement.clientWidth,
    window.innerWidth || 0
  );

  $('#btnShowGraphic').click(function (e) {
    e.preventDefault();

    $('.cardImportMultiproducts').hide(800);
    $('.cardTblMultiproducts').hide(800);
    $('.cardTblBreakeven').hide(800);
    $('.cardGraphicMultiproducts').show(800);

    loadGraphicMultiproducts();
  });

  loadGraphicMultiproducts = () => {
    let totalSoldUnits = 0;
    let totalUnitsToSol = 0;
    let product = [];

    /* Grafica de donut */
    for (let i = 0; i < multiproducts.length; i++) {
      totalSoldUnits += parseFloat(multiproducts[i].soldUnit);
      totalUnitsToSol += parseFloat(multiproducts[i].unitsToSold);
    }

    product.push(totalSoldUnits);
    product.push(totalUnitsToSol);

    chartMultiproductsDonut
      ? chartMultiproductsDonut.destroy()
      : chartMultiproductsDonut;

    cmo = document.getElementById('chartMultiproductsDonut');
    chartMultiproductsDonut = new Chart(cmo, {
      plugins: [ChartDataLabels],
      type: 'doughnut',
      data: {
        labels: ['N° de unidades Vendidas', 'N° de Unidades Por Vender'],
        datasets: [
          {
            data: product,
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
              size: '9',
              weight: 'bold',
            },
          },
        },
      },
    });

    /* Grafica de barras */
    product = [];

    for (let i = 0; i < multiproducts.length; i++) {
      product.push({
        name: multiproducts[i].product,
        soldUnits: multiproducts[i].soldUnit.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        }),
        unitsToSold: multiproducts[i].unitsToSold.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        }),
        percentage: multiproducts[i].percentage,
      });
    }

    product.sort(function (a, b) {
      return b['soldUnits'] - a['soldUnits'];
    });

    let nameProduct = [];
    let soldUnits = [];
    let unitsToSold = [];
    let color = [];

    anchura <= 480 ? (length = 5) : (length = 10);
    multiproducts.length >= length
      ? (count = length)
      : (count = multiproducts.length);

    for (let i = 0; i < count; i++) {
      nameProduct.push(product[i].name);
      soldUnits.push(product[i].soldUnits);
      unitsToSold.push(product[i].unitsToSold);

      if (product[i].percentage >= 1 && product[i].percentage <= 50)
        color.push('red');
      else if (product[i].percentage > 50 && product[i].percentage <= 80)
        color.push('yellow');
      else if (product[i].percentage > 80 && product[i].percentage <= 90)
        color.push('blue');
      else color.push('green');
    }

    chartMultiproductsBar
      ? chartMultiproductsBar.destroy()
      : chartMultiproductsBar;

    ctx = document.getElementById('chartMultiproductsBar').getContext('2d');
    chartMultiproductsBar = new Chart(ctx, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: nameProduct,
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            label: 'N° de unidades vendidas',
            data: soldUnits,
            backgroundColor: getRandomColor(count),
            borderWidth: 1,
          },
          {
            label: 'Unidades a vender',
            data: unitsToSold,
            backgroundColor: 'orange',
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
        plugins: {
          responsive: true,
          legend: {
            display: false,
          },
          datalabels: {
            anchor: 'end',
            formatter: (soldUnits) =>
              soldUnits.toLocaleString('es-CO', {
                maximumFractionDigits: 0,
              }),
            formatter: (unitsToSold) =>
              unitsToSold.toLocaleString('es-CO', {
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
