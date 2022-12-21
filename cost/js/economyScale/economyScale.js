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

    $.each(fixedCosts, (index, value) => {
      $(`#fixedCosts-${i}`).html(value.toLocaleString('es-CO'));
      i++;
    });

    /* Costos Variables */
    i = 1;
    variableCosts = data.variableCost;

    $.each(variableCosts, (index, value) => {
      $(`#variableCosts-${i}`).html(value.toLocaleString('es-CO'));
      i++;
    });

    /* Total Costos y Gastos */
    $('#totalCostsAndExpenses-1').html(
      (
        data.fixedCost.fixedCost100 + data.variableCost.variableCost100
      ).toLocaleString('es-CO')
    );
    $('#totalCostsAndExpenses-2').html(
      (
        data.fixedCost.fixedCost150 + data.variableCost.variableCost150
      ).toLocaleString('es-CO')
    );
    $('#totalCostsAndExpenses-3').html(
      (
        data.fixedCost.fixedCost200 + data.variableCost.variableCost200
      ).toLocaleString('es-CO')
    );
    $('#totalCostsAndExpenses-4').html(
      (
        data.fixedCost.fixedCost300 + data.variableCost.variableCost300
      ).toLocaleString('es-CO')
    );
    $('#totalCostsAndExpenses-5').html(
      (
        data.fixedCost.fixedCost500 + data.variableCost.variableCost500
      ).toLocaleString('es-CO')
    );
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

    calcTotalRevenue();
  });

  calcTotalRevenue = () => {
    let units = $('.unity');
    let prices = $('.price');

    for (i = 0; i < 5; i++) {
      unit = replaceNumber(units[i].value);
      price = replaceNumber(prices[i].value);

      totalRevenue = unit * price;

      $(`#totalRevenue-${i + 1}`).html(totalRevenue.toLocaleString('es-ES'));
    }
  };

  replaceNumber = (number) => {
    while (number.includes('.')) {
      if (number.includes('.')) number = number.replace('.', '');
    }
    if (number.includes(',')) number = number.replace(',', '.');
    return number;
  };
});
