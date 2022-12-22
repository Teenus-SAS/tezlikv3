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
    data = await searchData(`/api/calcEconomyScale/${id}`);

    /* Precios */
    $('.price').val(data.price.cost.toLocaleString('es-CO'));

    /* Costos Fijos */
    let i = 1;
    let fixedCosts = data.fixedCost;
    let dataCalcFCost = [];

    $.each(fixedCosts, (index, value) => {
      $(`#fixedCosts-${i}`).html(value.toLocaleString('es-CO'));
      i++;
      dataCalcFCost.push(value);
    });

    /* Costos Variables */
    i = 1;
    let variableCosts = data.variableCost;
    let dataCalcVCost = [];

    $.each(variableCosts, (index, value) => {
      $(`#variableCosts-${i}`).html(value.toLocaleString('es-CO'));
      i++;
      dataCalcVCost.push(value);
    });

    /* Total Costos y Gastos */

    for (i = 0; i < 5; i++) {
      $(`#totalCostsAndExpenses-${i + 1}`).html(
        (dataCalcFCost[i] + dataCalcVCost[i]).toLocaleString('es-CO')
      );
    }
  };

  /* Calculo */
  $(document).on('blur', '.totalRevenue', function () {
    let idProduct = $('#refProduct').val();

    if (!idProduct || idProduct == 0) {
      toastr.error('Seleccione un producto');
      return false;
    }

    let id = this.id;

    if (id == 'unity-1') {
      this.value == '' ? (unity = '0') : (unity = this.value);
      unity = replaceNumber(unity);

      $('#unity-1').val(unity.toLocaleString('es-CO'));

      let data = {
        unity150: unity * 1.5,
        unity200: unity * 2.0,
        unity300: unity * 3.0,
        unity500: unity * 5.0,
      };
      let i = 2;

      $.each(data, (index, value) => {
        $(`#unity-${i}`).val(value.toLocaleString('es-CO'));
        i++;
      });
    }

    for (i = 0; i < 5; i++) {
      let unit = $(`#unity-${i + 1}`).val();
      let price = $(`#price-${i + 1}`).val();

      /* Calculo Total Ingresos */
      unit = replaceNumber(unit);
      price = replaceNumber(price);

      totalRevenue = unit * price;

      $(`#totalRevenue-${i + 1}`).html(
        Math.round(totalRevenue).toLocaleString('es-ES')
      );

      /* Calculo Costo x Unidad */
      let totalCostsAndExpense = $(`#totalCostsAndExpenses-${i + 1}`).html();
      totalCostsAndExpense = replaceNumber(totalCostsAndExpense);

      unityCost = parseFloat(totalCostsAndExpense) / parseFloat(unit);

      $(`#unityCost-${i + 1}`).html(
        Math.round(unityCost).toLocaleString('es-ES')
      );

      /* Calculo Utilidad x Unidad */
      let unitUtility = price - unityCost;
      $(`#unitUtility-${i + 1}`).html(
        Math.round(unitUtility).toLocaleString('es-ES')
      );

      /* Calculo Utilidad Neta */
      let netUtility = unitUtility * unit;
      $(`#netUtility-${i + 1}`).html(
        Math.round(netUtility).toLocaleString('es-ES')
      );
    }
  });

  replaceNumber = (number) => {
    while (number.includes('.')) {
      if (number.includes('.')) number = number.replace('.', '');
    }
    if (number.includes(',')) number = number.replace(',', '.');
    return number;
  };
});
