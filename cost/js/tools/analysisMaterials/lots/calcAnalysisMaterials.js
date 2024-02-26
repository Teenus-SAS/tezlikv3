$(document).ready(function () {
  let totalCost;
  let currentPrice;
  let negotiatePrice;
  let projectedCost;

  // Calcular
  $(document).on('keyup click', '.negotiatePrice', function (e) {
    negotiatePrice = this.value;
    let id = this.id;

    row = id.slice(6, id.length);

    if (negotiatePrice == '') {
      negotiatePrice = 0;
    } else {
      negotiatePrice = strReplaceNumber(negotiatePrice);
      negotiatePrice = parseFloat(negotiatePrice);
    }

    // if (negotiatePrice == 0) {
    //   $('.dataAnalysis').val('');
    // }

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
    let totalQuantity = $(`#totalQuantity-${row}`).html();
    let aPrice = $(`#aPrice-${row}`).html();

    // Eliminar miles
    totalQuantity = strReplaceNumber(totalQuantity);
    
    // Eliminar miles
    aPrice = strReplaceNumber(aPrice);
    aPrice = aPrice.replace('$', '');

    totalQuantity = parseFloat(totalQuantity);
    aPrice = parseFloat(aPrice);

    if ($(`#price-${row}`).val() == '' || $(`#price-${row}`).val() == 0)
      $(`#totalCost-${row}`).html('');
    else {
      totalCost = aPrice * totalQuantity;

      $(`#totalCost-${row}`).html(
        `$ ${totalCost.toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`
      );
    }
  };

  /* Calcula el costo proyectado */
  calculateProjectedCost = (row) => {
    totalQuantity = $(`#totalQuantity-${row}`).html();
    negotiatePrice = parseFloat($(`#price-${row}`).val());

    // Eliminar miles
    totalQuantity = parseFloat(strReplaceNumber(totalQuantity));

    projectedCost = negotiatePrice * totalQuantity;

    if (isNaN(projectedCost) || $(`#price-${row}`).val() == '' || $(`#price-${row}`).val() == 0)
      $(`#projectedCost-${row}`).html('');
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
