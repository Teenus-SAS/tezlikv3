$(document).ready(function () {
  let quantity;
  let currentPrice;
  let negotiatePrice;
  let projectedCost;

  // Calcular
  $(document).on('keyup', '.negotiatePrice', function (e) {
    negotiatePrice = this.value;
    let id = this.id;

    row = id.slice(6, id.length);

    if (negotiatePrice == '') {
      negotiatePrice = 0;
    } else {
      negotiatePrice = strReplaceNumber(negotiatePrice);
      negotiatePrice = parseFloat(negotiatePrice);
    }

    currentPrice = dataAnalysisMaterials[row].cost;

    // Calcular porcentaje
    calculatePercent(negotiatePrice, currentPrice);

    for (let i = 0; i < products.length; i++) {
      if (dataAnalysisMaterials[row].id_product == products[i].idProduct) {
        unitsmanufacturated = totalUnits;
        break;
      }
    }

    // Calcular costo proyectado
    calculateProjectedCost(row);
    $('#monthlySavings').css('border-color', 'mediumseagreen');
    $('#annualSavings').css('border-color', 'blue');

    // Calcular costo total
    calculateCostMaterial(row);

    // Calcular ahorro mensual
    savingsMontly();

    // Calcular ahorro Anual
    savingsAnnual();
  });

  /* Calcular porcentaje */
  calculatePercent = (negotiatePrice, currentPrice) => {
    percentage = 100 - (negotiatePrice / currentPrice) * 100;

    if (isNaN(negotiatePrice)) $(`#percentage-${row}`).html('');
    else
      $(`#percentage-${row}`).html(
        `${percentage.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })} %`
      );

    if (percentage < 0) {
      $(`#percentage-${row}`).css('color', 'red');
      $(`#monthlySavings`).css('color', 'red');
      $(`#annualSavings`).css('color', 'red');
    } else {
      $(`#percentage-${row}`).css('color', '#505d69');
      $(`#monthlySavings`).css('color', 'gray');
      $(`#annualSavings`).css('color', 'gray');
    }
  };

  /* Calcula el costo de los materiales */
  calculateCostMaterial = (row) => {
    unityCost = $(`#unityCost-${row}`).html();
    quantity = $(`#quantity-${row}`).html();

    // Eliminar miles
    unityCost = strReplaceNumber(unityCost);
    unityCost = unityCost.replace('$', '');

    // Eliminar miles
    quantity = strReplaceNumber(quantity);

    unityCost = parseFloat(unityCost);
    quantity = parseFloat(quantity);

    totalCost = unitsmanufacturated * unityCost;

    $(`#totalCost-${row}`).html(
      totalCost.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })
    );
  };

  /* Calcula el costo proyectado */
  calculateProjectedCost = (row) => {
    quantity = $(`#quantity-${row}`).html();

    quantity = strReplaceNumber(quantity);

    quantity = parseFloat(quantity);

    let negotiatePrice = $(`#price-${row}`).val();

    negotiatePrice == '' ? (negotiatePrice = '0') : negotiatePrice;

    // Eliminar miles
    negotiatePrice = strReplaceNumber(negotiatePrice);
    negotiatePrice = parseFloat(negotiatePrice);

    projectedCost = quantity * negotiatePrice * unitsmanufacturated;

    if (isNaN(projectedCost)) $(`#projectedCost-${row}`).html();
    else
      $(`#projectedCost-${row}`).html(
        `$ ${projectedCost.toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`
      );
  };

  /* Calcula el ahorro mensual */
  savingsMontly = () => {
    totalMonthlySavings = 0;

    for (i = 0; i < dataAnalysisMaterials.length; i++) {
      let projectedCost = $(`#projectedCost-${i}`).html();

      // Eliminar miles
      projectedCost = strReplaceNumber(projectedCost);
      projectedCost = projectedCost.replace('$', '');
      projectedCost = parseFloat(projectedCost);

      let currentCost = $(`#totalCost-${i}`).html();
      // Eliminar miles
      currentCost = strReplaceNumber(currentCost);
      currentCost = currentCost.replace('$', '');
      currentCost = parseFloat(currentCost);

      monthlySavingsRow = currentCost - projectedCost;

      isNaN(monthlySavingsRow) ? (monthlySavingsRow = 0) : monthlySavingsRow;

      totalMonthlySavings += monthlySavingsRow;
    }
    $(`#monthlySavings`).html(
      `$ ${totalMonthlySavings.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}`
    );
  };

  /* Calcula el costo Anual */
  savingsAnnual = () => {
    if (isNaN(totalMonthlySavings)) {
      $('#monthlySavings').val('');
      $('#annualSavings').val('');
    } else {
      $('#monthlySavings').val(
        `$ ${totalMonthlySavings.toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`
      );
      // Calcular ahorro anual
      annualSavings = totalMonthlySavings * 12;
      $('#annualSavings').val(
        `$ ${annualSavings.toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`
      );
    }
  };
});
