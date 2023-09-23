$(document).ready(function () {
  $('#refProduct').change(function (e) {
    e.preventDefault();

    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id, 2);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id, 2);
  });

  loadDataProduct = async (id, op) => {
    $('.general').val('');
    $('.general').html('');

    data = await searchData(`/api/calcEconomyScale/${id}`);

    $('#unity-0').val(1);
    unitys = [1];

    commission = data.commission;

    op == 1 ? price = data.sale_price : price = data.price;

    // Regla de tres rentabilidad
    profitability = (price * data.profitability) / price;

    /* Precios */
    $('.price').val(
      price.toLocaleString('es-CO', { maximumFractionDigits: 0 })
    );
    prices = [
      price,
      price,
      price,
      price,
      price,
      price,
    ];

    /* Costos Fijos */
    fixedCost = data.fixedCost;

    variableCost = data.variableCost;

    // Costos Fijos
    $(`.fixedCosts`).html(
      `$ ${fixedCost.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`
    );

    // Total Costos y Gastos
    $(`.totalCostsAndExpenses`).html(
      `$ ${(fixedCost + variableCost).toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })}`
    );

    generalCalc(0);
  };

  $('#typePrice').change(function (e) {
    e.preventDefault();
    
  });
});
