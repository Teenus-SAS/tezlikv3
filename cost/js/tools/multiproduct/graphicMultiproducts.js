$(document).ready(function () {
  var myChart;

  var anchura = Math.max(
    document.documentElement.clientWidth,
    window.innerWidth || 0
  );

  $('#btnShowGraphic').click(function (e) {
    e.preventDefault();

    $('.cardTblMultiproducts').hide(800);
    $('.cardTblBreakeven').hide(800);
    $('.cardGraphicMultiproducts').show(800);

    loadGraphicMultiproducts();
  });

  loadGraphicMultiproducts = () => {
    let product = [];

    anchura <= 480 ? (length = 5) : (length = data.length);

    for (let i = 0; i < data.length; i++) {
      product.push({
        name: data[i].product,
        soldUnits: data[i].soldUnit.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        }),
        unitsToSold: data[i].unitsToSold.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        }),
        percentage: data[i].percentage,
      });
    }

    product.sort(function (a, b) {
      return b['soldUnits'] - a['soldUnits'];
    });

    let nameProduct = [];
    let soldUnits = [];
    let unitsToSold = [];
    let color = [];

    data.length > length ? (count = length) : (count = 10);

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

    myChart ? myChart.destroy() : myChart;

    ctx = document.getElementById('chartMultiproducts').getContext('2d');
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
            label: 'N° de unidades vendidas',
            data: soldUnits,
            backgroundColor: color,
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
