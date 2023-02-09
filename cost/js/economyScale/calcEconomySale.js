$(document).ready(function () {
  /* Calculo */
  $(document).on('blur', '.totalRevenue', function () {
    try {
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

      if (id.includes('unity')) {
        this.value == '' ? (unity = '0') : (unity = this.value);
        unity = parseInt(strReplaceNumber(unity));

        let percentage = { 2: 1, 3: 0.5, 4: 0.333333333333333, 5: 0.5 };
        unitys = [1];

        unitys[row] = unity;

        for (let i = row; i < 5; i++) {
          unity = unity * (1 + percentage[i + 1]);

          $(`#unity-${i + 1}`).val(
            unity.toLocaleString('es-CO', { maximumFractionDigits: 0 })
          );

          unitys[i + 1] = unity;
        }
      } else {
        let price = parseFloat(strReplaceNumber(this.value));

        prices = [0, price];
        for (let i = row; i < 5; i++) {
          prices[i + 1] = price;
          $(`#price-${i + 1}`).val(price.toLocaleString('es-CO'));
        }
      }
    } catch (error) {
      console.log(error);
    }

    generalCalc(1);
  });

  generalCalc = (op) => {
    try {
      op == 0 ? (count = 0) : (count = 5);

      for (i = op; i <= count; i++) {
        let unit = unitys[i];
        let price = prices[i];

        if (unit > 0 && price > 0) {
          /* Costos Variables */

          let totalVariableCost = variableCost * unit;
          $(`#variableCosts-${i}`).html(
            `$ ${totalVariableCost.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          /* Total Costos y Gastos */
          let totalCostsAndExpense = fixedCost + totalVariableCost;

          $(`#totalCostsAndExpenses-${i}`).html(
            `$ ${totalCostsAndExpense.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          /* Calculo Total Ingresos */
          totalRevenue = unit * price;

          $(`#totalRevenue-${i}`).html(
            `$ ${totalRevenue.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          /* Calculo Costo x Unidad */
          unityCost = parseFloat(totalCostsAndExpense) / parseFloat(unit);

          $(`#unityCost-${i}`).html(
            `$ ${unityCost.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          /* Calculo Utilidad x Unidad */
          let unitUtility = price - unityCost;
          $(`#unitUtility-${i}`).html(
            `$ ${unitUtility.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          /* Calculo Utilidad Neta */
          let netUtility = unitUtility * unit;

          netUtility < 0
            ? $(`#netUtility-${i}`).css('color', 'red')
            : $(`#netUtility-${i}`).css('color', 'black');

          $(`#netUtility-${i}`).html(
            `$ ${netUtility.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          /* Porcentaje */
          percentage = (netUtility / (totalRevenue - commission)) * 100;

          $(`#percentage-${i}`).html(
            `${percentage.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })} %`
          );
        }
      }
    } catch (error) {
      console.log(error);
    }
  };
});
