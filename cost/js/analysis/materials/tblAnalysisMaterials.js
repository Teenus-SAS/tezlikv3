$(document).ready(function () {
  let count;
  let unitsmanufacturated;
  let quantity;
  let currentPrice;
  let negotiatePrice;
  let currentCost;
  let projectedCost;

  loadTableRawMaterialsAnalisys = (idProduct) => {
    $('#unitsmanufacturated').val('');
    $('#unitsmanufacturated').click();

    $('.colMaterials').empty();
    $.ajax({
      url: `/api/rawMaterials/${idProduct}`,
      success: function (r) {
        if (r.length == 0) {
          $('.colMaterials').append(`
                        <tr class="col">
                            <th class="text-center" colspan="9">Ningún dato disponible en esta tabla =(</th>
                        </tr>
                      `);
          return false;
        } else {
          $('.empty').hide();
          $('.colMaterials').empty();

          let participation = 0;

          for (i = 0; participation <= 80; i++) {
            //<th id="reference-${i + 1}">${r[i].reference}</th>
            $('.colMaterials').append(
              `<tr class="col${i + 1} text-center" id="col${i + 1}">
                              <th scope="row">${i + 1}</th>
                              <th scope="row">${r[i].participation.toFixed(
                                1
                              )}%</th>
                              <th id="rawMaterial-${i + 1}">${
                r[i].material
              }</th>
                              <th id="quantity-${i + 1}">${r[i].quantity}</th>
                              <th id="currentPrice-${i + 1}">$ ${r[
                i
              ].cost.toLocaleString('es-ES')}</th>
                              <th><input class="form-control number negotiatePrice text-center" type="text" id="${
                                i + 1
                              }"></th>
                              <th id="percentage-${i + 1}"></th>
                              <th id="unityCost-${i + 1}">$ ${r[
                i
              ].unityCost.toLocaleString('es-ES')}</th>
                              <th id="totalCost-${i + 1}"></th>
                              <th id="projectedCost-${i + 1}"></th>
                            </tr>
                    
                           <script src="../../global/js/global/number.js"></script>`
            );
            participation = participation + r[i].participation;
          }
        }
        count = i;

        for (let i = 1; i < count + 1; i++)
          $(`#${i}`).prop('readonly', 'readonly');
      },
    });
  };

  $(document).on('click', '.negotiatePrice', function (e) {
    e.preventDefault();

    unitsmanufacturated = $('#unitsmanufacturated').val();
    // Eliminar decimales
    unitsmanufacturated = decimalNumber(unitsmanufacturated);
    unitsmanufacturated = parseFloat(unitsmanufacturated);

    if (!unitsmanufacturated) {
      toastr.error('Ingrese las unidades a fabricar');
      return false;
    } else $(`.negotiatePrice`).removeAttr('readonly');
  });

  // Calcular
  $(document).on('keyup', '.negotiatePrice', function (e) {
    negotiatePrice = this.value;
    line = this.id;

    if (negotiatePrice == '') {
      negotiatePrice = 0;
    } else {
      negotiatePrice = negotiatePrice.replace('.', '');
      negotiatePrice = parseFloat(negotiatePrice);
    }

    currentPrice = $(`#currentPrice-${line}`).html();
    currentPrice = currentPrice.replace('.', '').replace('$', '');
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
    else $(`#percentage-${line}`).html(`${percentage.toFixed(3)} %`);

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

    // Eliminar decimales
    unitsmanufacturated = decimalNumber(unitsmanufacturated);
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

      unityCost = unityCost.replace('$', '').replace(',', '.');
      // Eliminar decimales
      unityCost = decimalNumber(unityCost);

      quantity = quantity.replace(',', '.');
      // Eliminar decimales
      quantity = decimalNumber(quantity);

      unityCost = parseFloat(unityCost);
      quantity = parseFloat(quantity);

      totalCost = unitsmanufacturated * unityCost;

      $(`#totalCost-${i}`).html(totalCost.toLocaleString('es-ES'));
    }
  };

  /* Calcula el costo proyectado */
  calculateProjectedCost = (i) => {
    for (i = 1; i < count + 1; i++) {
      quantity = $(`#quantity-${i}`).html();
      quantity = parseFloat(quantity);

      let negotiatePrice = $(`#${i}`).val();

      if (negotiatePrice == '') return false;

      negotiatePrice = negotiatePrice.replace('$', '');
      // Eliminar decimales
      negotiatePrice = decimalNumber(negotiatePrice);
      negotiatePrice = parseFloat(negotiatePrice);

      if (negotiatePrice) {
        projectedCost = quantity * negotiatePrice * unitsmanufacturated;

        if (isNaN(projectedCost)) $(`#projectedCost-${i}`).html();
        else
          $(`#projectedCost-${i}`).html(
            `$ ${projectedCost.toLocaleString('es-ES')}`
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

      projectedCost = projectedCost.replace('$', '').replace(',', '.');
      // Eliminar decimales
      projectedCost = decimalNumber(projectedCost);
      projectedCost = parseFloat(projectedCost);

      let currentCost = $(`#totalCost-${i}`).html();
      currentCost = currentCost.replace('$', '').replace(',', '.');
      // Eliminar decimales
      currentCost = decimalNumber(currentCost);
      currentCost = parseFloat(currentCost);

      monthlySavingsRow = currentCost - projectedCost;

      isNaN(monthlySavingsRow) ? (monthlySavingsRow = 0) : monthlySavingsRow;

      totalMonthlySavings = totalMonthlySavings + monthlySavingsRow;

      $(`#monthlySavings`).html(
        `$ ${totalMonthlySavings.toLocaleString('es-ES')}`
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
        `$ ${totalMonthlySavings.toLocaleString('es-ES')}`
      );
      // Calcular ahorro anual
      annualSavings = totalMonthlySavings * 12;
      $('#annualSavings').val(`$ ${annualSavings.toLocaleString('es-ES')}`);
    }
  };

  /* Eliminar puntos decimales */
  decimalNumber = (num) => {
    while (num.includes('.')) {
      num = num.replace('.', '');
    }
    return num;
  };
});
