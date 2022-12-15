$(document).ready(function () {
  let quantity;
  let currentPrice;
  let negotiatePrice;
  let currentCost;
  let projectedCost;

  // Calcular
  $(document).on('keyup', '.negotiatePrice', function (e) {
    negotiatePrice = this.value;
    line = this.id;

    if (negotiatePrice == '') {
      negotiatePrice = 0;
    } else {
      negotiatePrice = decimalNumber(negotiatePrice);
      negotiatePrice = negotiatePrice.replace(',', '.');
      negotiatePrice = parseFloat(negotiatePrice);
    }

    currentPrice = $(`#currentPrice-${line}`).html();
    currentPrice = decimalNumber(currentPrice);
    currentPrice = currentPrice.replace(',', '.').replace('$', '');
    currentPrice = parseFloat(currentPrice);

    // Calcular porcentaje
    calculatePercent(negotiatePrice, currentPrice);

    if (isNaN(unitsmanufacturated)) {
      toastr.error('Ingrese las unidades a fabricar');
      $(`#projectedCost-${line}`).html('');
      return false;
    } else {
      // Calcular costo proyectado
      calculateProjectedCost(line);
      $('#monthlySavings').css('border-color', 'mediumseagreen');
      $('#annualSavings').css('border-color', 'blue');
    }
  });

  /* Calcular porcentaje */
  calculatePercent = (negotiatePrice, currentPrice) => {
    percentage = 100 - (negotiatePrice / currentPrice) * 100;

    if (isNaN(negotiatePrice)) $(`#percentage-${line}`).html('');
    else
      $(`#percentage-${line}`).html(
        `${percentage.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })} %`
      );

    if (percentage < 0) {
      $(`#percentage-${line}`).css('color', 'red');
      $(`#monthlySavings`).css('color', 'red');
      $(`#annualSavings`).css('color', 'red');
    } else {
      $(`#percentage-${line}`).css('color', '#505d69');
      $(`#monthlySavings`).css('color', 'gray');
      $(`#annualSavings`).css('color', 'gray');
    }
  };

  $(document).on('keyup', '#unitsmanufacturated', function (e) {
    unitsmanufacturated = this.value;

    // Eliminar miles
    unitsmanufacturated = decimalNumber(unitsmanufacturated);
    unitsmanufacturated = unitsmanufacturated.replace(',', '.');
    unitsmanufacturated = parseFloat(unitsmanufacturated);

    isNaN(unitsmanufacturated)
      ? (unitsmanufacturated = 0)
      : unitsmanufacturated;

    // Calcular costo total
    calculateCostMaterial(unitsmanufacturated, i);

    //calcula el costo proyectado
    calculateProjectedCost(i);

    // Calcular ahorro mensual
    savingsMontly();

    // Calcular ahorro Anual
    savingsAnnual();
  });

  /* Calcula el costo de los materiales */
  calculateCostMaterial = () => {
    for (i = 1; i < count + 1; i++) {
      unityCost = $(`#unityCost-${i}`).html();
      quantity = $(`#quantity-${i}`).html();

      // Eliminar miles
      unityCost = decimalNumber(unityCost);
      unityCost = unityCost.replace('$', '').replace(',', '.');

      // Eliminar miles
      quantity = decimalNumber(quantity);
      quantity = quantity.replace(',', '.');

      unityCost = parseFloat(unityCost);
      quantity = parseFloat(quantity);

      totalCost = unitsmanufacturated * unityCost;

      $(`#totalCost-${i}`).html(
        parseInt(totalCost.toFixed()).toLocaleString('es-CO')
      );
    }
  };

  /* Calcula el costo proyectado */
  calculateProjectedCost = (i) => {
    for (i = 1; i < count + 1; i++) {
      quantity = $(`#quantity-${i}`).html();

      quantity = decimalNumber(quantity);
      quantity = quantity.replace(',', '.');

      quantity = parseFloat(quantity);

      let negotiatePrice = $(`#${i}`).val();

      if (negotiatePrice == '') return false;

      negotiatePrice = negotiatePrice.replace('$', '');
      // Eliminar miles
      negotiatePrice = decimalNumber(negotiatePrice);
      negotiatePrice = negotiatePrice.replace(',', '.');
      negotiatePrice = parseFloat(negotiatePrice);

      if (negotiatePrice) {
        projectedCost = quantity * negotiatePrice * unitsmanufacturated;

        if (isNaN(projectedCost)) $(`#projectedCost-${i}`).html();
        else
          $(`#projectedCost-${i}`).html(
            `$ ${parseInt(projectedCost.toFixed()).toLocaleString('es-CO')}`
          );
      }
      savingsMontly();
      savingsAnnual();
    }
  };

  /* Calcula el ahorro mensual */
  savingsMontly = () => {
    totalMonthlySavings = 0;

    for (i = 1; i < count + 1; i++) {
      let projectedCost = $(`#projectedCost-${i}`).html();

      if (projectedCost == '') return false;

      // Eliminar miles
      projectedCost = decimalNumber(projectedCost);
      projectedCost = projectedCost.replace('$', '').replace(',', '.');
      projectedCost = parseFloat(projectedCost);

      let currentCost = $(`#totalCost-${i}`).html();
      // Eliminar miles
      currentCost = decimalNumber(currentCost);
      currentCost = currentCost.replace('$', '').replace(',', '.');
      currentCost = parseFloat(currentCost);

      monthlySavingsRow = currentCost - projectedCost;

      isNaN(monthlySavingsRow) ? (monthlySavingsRow = 0) : monthlySavingsRow;

      totalMonthlySavings = totalMonthlySavings + monthlySavingsRow;

      $(`#monthlySavings`).html(
        `$ ${totalMonthlySavings.toLocaleString('es-CO')}`
      );
    }
  };

  /* Calcula el costo Anual */
  savingsAnnual = () => {
    if (isNaN(totalMonthlySavings)) {
      $('#monthlySavings').val('');
      $('#annualSavings').val('');
    } else {
      $('#monthlySavings').val(
        `$ ${totalMonthlySavings.toLocaleString('es-CO')}`
      );
      // Calcular ahorro anual
      annualSavings = totalMonthlySavings * 12;
      $('#annualSavings').val(`$ ${annualSavings.toLocaleString('es-CO')}`);
    }
  };

  /* Eliminar puntos miles */
  decimalNumber = (num) => {
    if (num.includes('.'))
      while (num.includes('.')) {
        num = num.replace('.', '');
      }
    return num;
  };
});
