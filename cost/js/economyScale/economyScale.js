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

    /* Precios */
    $('.price').val(data.price.toLocaleString('es-CO'));

    /* Costos Fijos */
    let i = 1;
    let fixedCosts = data.fixedCost;
    dataCalcFCost = [];

    $.each(fixedCosts, (index, value) => {
      $(`#fixedCosts-${i}`).html(`$ ${value.toLocaleString('es-CO')}`);
      /* Total Costos y Gastos */
      $(`#totalCostsAndExpenses-${i}`).html(
        `$ ${value.toLocaleString('es-CO')}`
      );
      dataCalcFCost.push(value);
      i++;
    });

    variableCost = data.variableCost;
  };

  /* Calculo */
  $(document).on('blur', '.totalRevenue', function () {
    let idProduct = $('#refProduct').val();

    if (!idProduct || idProduct == 0) {
      toastr.error('Seleccione un producto');
      return false;
    }

    let value = this.value;

    if (!value || value == 0) {
      toastr.error('Ingrese un valor mayor a cero');
      loadDataProduct(idProduct);
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

      for (i = 0; i < 5; i++) {
        /* Costos Variables */
        let unit = $(`#unity-${i + 1}`).val();
        unit = replaceNumber(unit);

        let totalVariableCost = variableCost * unit;
        $(`#variableCosts-${i + 1}`).html(
          `$ ${totalVariableCost.toLocaleString('es-CO')}`
        );

        /* Total Costos y Gastos */
        $(`#totalCostsAndExpenses-${i + 1}`).html(
          `$ ${(dataCalcFCost[i] + totalVariableCost).toLocaleString('es-CO')}`
        );
      }
    }

    for (i = 0; i < 5; i++) {
      let unit = $(`#unity-${i + 1}`).val();

      let price = $(`#price-${i + 1}`).val();

      /* Calculo Total Ingresos */
      unit = replaceNumber(unit);
      price = replaceNumber(price);

      totalRevenue = unit * price;

      $(`#totalRevenue-${i + 1}`).html(
        `$ ${Math.round(totalRevenue).toLocaleString('es-CO')}`
      );

      /* Calculo Costo x Unidad */
      let totalCostsAndExpense = $(`#totalCostsAndExpenses-${i + 1}`).html();
      totalCostsAndExpense = replaceNumber(totalCostsAndExpense);

      unityCost = parseFloat(totalCostsAndExpense) / parseFloat(unit);

      $(`#unityCost-${i + 1}`).html(
        `$ ${Math.round(unityCost).toLocaleString('es-CO')}`
      );

      /* Calculo Utilidad x Unidad */
      let unitUtility = price - unityCost;
      $(`#unitUtility-${i + 1}`).html(
        `$ ${Math.round(unitUtility).toLocaleString('es-CO')}`
      );

      /* Calculo Utilidad Neta */
      let netUtility = unitUtility * unit;

      netUtility < 0
        ? $(`#netUtility-${i + 1}`).css('color', 'red')
        : $(`#netUtility-${i + 1}`).css('color', 'black');

      $(`#netUtility-${i + 1}`).html(
        `$ ${Math.round(netUtility).toLocaleString('es-CO')}`
      );
    }
  });

  replaceNumber = (number) => {
    while (number.includes('.')) {
      if (number.includes('.')) number = number.replace('.', '');
    }
    if (number.includes(',')) number = number.replace(',', '.');
    number = number.replace('$ ', ' ');

    return number;
  };
});
