$(document).ready(function () {
  $('#refProduct').change(function (e) {
    e.preventDefault();

    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  loadDataProduct = async (id) => {
    $('.general').val('');
    $('.general').html('');

    data = await searchData(`/api/calcEconomyScale/${id}`);

    commission = data.commission;

    /* Precios */
    $('.price').val(data.price.toLocaleString('es-CO'));

    /* Costos Fijos */
    fixedCost = data.fixedCost;

    variableCost = data.variableCost;
    for (i = 0; i < 5; i++) {
      /* Costos Fijos */
      $(`#fixedCosts-${i + 1}`).html(`$ ${fixedCost.toLocaleString('es-CO')}`);

      /* Costos Variables */
      $(`#variableCosts-${i + 1}`).html(
        `$ ${variableCost.toLocaleString('es-CO')}`
      );

      /* Total Costos y Gastos */
      $(`#totalCostsAndExpenses-${i + 1}`).html(
        `$ ${(fixedCost + variableCost).toLocaleString('es-CO')}`
      );
    }
  };
});
