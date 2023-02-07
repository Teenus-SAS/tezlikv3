$(document).ready(function () {
  /* Calculo */
  $(document).on('blur', '.totalRevenue', function () {
    let idProduct = $('#refProduct').val();

    if (!idProduct || idProduct == 0) {
      toastr.error('Seleccione un producto');
      return false;
    }

    let value = this.value;
    let id = this.id;
    let row = parseInt(id.slice(6, 7));

    if (!value || value == 0) {
      $(`#totalRevenue-${row}`).html('');
      $(`#variableCosts-${row}`).html('');
      $(`#totalCostsAndExpenses-${row}`).html('');
      $(`#unityCost-${row}`).html('');
      $(`#unitUtility-${row}`).html('');
      $(`#netUtility-${row}`).html('');
      $(`#percentage-${row}`).html('');

      toastr.error('Ingrese un valor mayor a cero');

      // loadDataProduct(idProduct);
      return false;
    }

    if (id == 'unity-1') {
      this.value == '' ? (unity = '0') : (unity = this.value);
      unity = decimalNumber(unity);
      unity = unity.replace('$ ', '');

      $('#unity-1').val(unity.toLocaleString('es-CO'));

      let data = {
        unity150: unity * 2,
        unity200: unity * 3,
        unity300: unity * 4,
        unity500: unity * 6,
      };
      let i = 2;

      $.each(data, (index, value) => {
        $(`#unity-${i}`).val(value.toLocaleString('es-CO'));
        i++;
      });
    } else {
      let price = $(`#price-${row}`).val();
      for (let i = row; i < 5; i++) {
        $(`#price-${i + 1}`).val(price.toLocaleString('es-CO'));
      }
    }

    generalCalc();
  });

  generalCalc = () => {
    for (i = 0; i < 5; i++) {
      let unit = $(`#unity-${i + 1}`).val();
      if (unit == 0 || !unit) return false;

      let price = $(`#price-${i + 1}`).val();
      if (price == 0 || !price) return false;

      /* Costos Variables */
      unit = decimalNumber(unit);

      let totalVariableCost = variableCost * unit;
      $(`#variableCosts-${i + 1}`).html(
        `$ ${totalVariableCost.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}`
      );

      /* Total Costos y Gastos */
      $(`#totalCostsAndExpenses-${i + 1}`).html(
        `$ ${(fixedCost + totalVariableCost).toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}`
      );

      /* Calculo Total Ingresos */
      price = decimalNumber(price);

      totalRevenue = unit * price;

      $(`#totalRevenue-${i + 1}`).html(
        `$ ${Math.round(totalRevenue).toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}`
      );

      /* Calculo Costo x Unidad */
      let totalCostsAndExpense = $(`#totalCostsAndExpenses-${i + 1}`).html();
      totalCostsAndExpense = decimalNumber(totalCostsAndExpense);
      totalCostsAndExpense = totalCostsAndExpense.replace('$ ', '');

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

      /* Porcentaje */
      percentage = (netUtility / (totalRevenue - commission)) * 100;

      $(`#percentage-${i + 1}`).html(
        `${percentage.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })} %`
      );
    }
  };
});
