$(document).ready(function () {
  $('#btnShowGraphic').click(function (e) {
    e.preventDefault();

    $('.cardTblMultiproducts').hide(800);
    $('.cardGraphicMultiproducts').show(800);

    loadGraphicMultiproducts();
  });

  loadGraphicMultiproducts = () => {
    let product = [];

    data.length > 10 ? (count = 10) : (count = data.length);

    for (let i = 0; i < count; i++) {
      data[i].soldUnit == undefined || !data[i].soldUnit
        ? (soldUnits = 0)
        : (soldUnits = data[i].soldUnit);

      data[i].unitsToSold == undefined || !data[i].unitsToSold
        ? (unitsToSold = 0)
        : (unitsToSold = data[i].unitsToSold);

      product.push({
        name: data[i].product,
        soldUnits: soldUnits,
        unitsToSold: unitsToSold,
      });
    }

    product.sort(function (a, b) {
      return b['soldUnits'] - a['soldUnits'];
    });

    let nameProduct = [];
    let soldUnits = [];
    let unitsToSold = [];

    for (let i = 0; i < count; i++) {
      nameProduct.push(product[i].name);
      soldUnits.push(product[i].soldUnits);
      unitsToSold.push(product[i].unitsToSold);
    }

    ctx = document.getElementById('chartMultiproducts').getContext('2d');
    myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: nameProduct,
        datasets: [
          {
            label: 'N° de unidades vendidas',
            data: soldUnits,
            backgroundColor: 'red',
            borderWidth: 1,
          },
          {
            label: 'Unidades a vender',
            data: unitsToSold,
            backgroundColor: 'blue',
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
