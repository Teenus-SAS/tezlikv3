$(document).ready(function () {
  $(document).on('blur', '.totalRevenue', function () {
    try {
      let idProduct = $('#refProduct').val();

      if (!idProduct || idProduct == 0) {
        toastr.error('Seleccione un producto');
        return;
      }

      let value = this.value;
      let id = this.id;
      let row = parseInt(id.slice(6, 7));

      if (!value || value == 0) {
        clearFields(row);
        toastr.error('Ingrese un valor mayor a cero');
        return;
      }

      let percentage = $(`#percentage-${row}`).val();

      if (id.includes('unity')) {
        updateUnity(row, value);
      } else {
        if (!percentage)
          updatePrice(row, value);
      }

      const calcRow = percentage ? row : (id !== 'unity-0' ? 1 : null);
      if (calcRow !== null) {
        generalCalc(calcRow, percentage ? row : 5);
      }

    } catch (error) {
      console.log(error);
    }
  });

  function clearFields(row) {
    let fields = ['totalRevenue', 'variableCosts', 'totalCostsAndExpenses', 'unityCost', 'unitUtility', 'netUtility', 'profitMargin'];
    fields.forEach(field => $(`#${field}-${row}`).html(''));
  }

  function updateUnity(row, value) {
    let unity = parseInt(strReplaceNumber(value || '0'));
    let profitMargin = { 1: 1, 2: 1, 3: 0.5, 4: 0.333333333333333, 5: 0.5 };
    
    unitys[row] = unity;

    for (let i = row; i < 5; i++) {
      unity *= (1 + profitMargin[i + 1]);
      $(`#unity-${i + 1}`).val(parseInt(unity));
      unitys[i + 1] = unity;
    }
  }

  function updatePrice(row, value) {
    let price = parseInt(value);
    let max = sessionStorage.getItem('typePrice') === '2' ? 2 : 0;

    prices[row].original_price = price;
    prices[row].partial_price = price;

    for (let i = row; i < 5; i++) {
      prices[i + 1].original_price = price;
      prices[i + 1].partial_price = price;
      $(`#price-${i + 1}`).val(price.toFixed(max));
    }
  }

  generalCalc = async (op, count) => {
    try {
      const typePrice = sessionStorage.getItem('typePrice');
      const maxDecimals = typePrice === '2' ? 2 : 0;
      const startTime = performance.now();
      const maxTimeLimit = 5; // segundos

      const formatCurrency = (amount, decimals = 0) =>
        `$ ${amount.toLocaleString('es-CO', { maximumFractionDigits: decimals })}`;

      const handleIteration = async (i) => {
        const unit = unitys[i];
        const price = prices[i].partial_price;

        if (unit > 0 && price > 0) {
          const totalVariableCost = variableCost * unit;
          const totalCostsAndExpense = fixedCost + totalVariableCost;
          const totalRevenue = unit * price;
          const unityCost = totalCostsAndExpense / unit;
          const unitUtility = price - unityCost;
          const netUtility = unitUtility * unit;
          const profitMargin = (netUtility / totalRevenue) * 100;

          // Actualización de elementos en el DOM
          $(`#variableCosts-${i}`).html(formatCurrency(totalVariableCost));
          $(`#totalCostsAndExpenses-${i}`).html(formatCurrency(totalCostsAndExpense));
          $(`#totalRevenue-${i}`).html(formatCurrency(totalRevenue));
          $(`#unityCost-${i}`).html(formatCurrency(unityCost, maxDecimals));
          $(`#unitUtility-${i}`).html(formatCurrency(unitUtility, maxDecimals));
          $(`#netUtility-${i}`)
            .html(formatCurrency(netUtility))
            .css('color', netUtility < 0 ? 'red' : 'black');
          $(`#profitMargin-${i}`).html(`${profitMargin.toFixed(2)} %`);

          if (i === 0 && profitMargin >= profitability) {
            let division = Math.ceil(totalCostsAndExpense / price + cant);

            if (division == 1) {
              $('#unity-1').val(2);
            }
            return false;
          }

          if (i === 0 && profitMargin < profitability) {
            cant = profitMargin > 0 ? cant + 2 : 1;
            let division = Math.ceil(totalCostsAndExpense / price + cant);

            if (division > 10_000_000) {
              toastr.error('Precios muy por debajo de lo requerido. Si se sigue calculando automáticamente generará números demasiado grandes');
              return false;
            }

            if (typeExpense === '2') division = Math.ceil(division / 12);
            $(`#unity-${i}`).val(division.toLocaleString('es-CO', { maximumFractionDigits: 0 }));
            unitys[i] = division;

            const elapsedSeconds = (performance.now() - startTime) / 1000;
            if (elapsedSeconds > maxTimeLimit) return false;

            await new Promise(resolve => setTimeout(resolve, 0));
            await $(`#unity-${i}`).blur();
          }
        }
        return true;
      };

      for (let i = op; i <= count; i++) {
        if (!(await handleIteration(i))) break;
        if (op === 0) i -= 1;
      }

      if (op === 0) $('#unity-1').blur();
      $('.cardLoading').remove();
      checkPrices();
    } catch (error) {
      console.error(error);
    }
  };

  function checkPrices() {
    if (sugered_price)
      $('#sugered').show(400);
    if (actual_price)
      $('#actual').show(400);
    if (real_price)
      $('#real').show(400);
  }

  // Calculo precio por porcentaje
  $(document).on('keyup', '.percentage', function () {
    !this.value ? this.value = 0 : this.value;

    let idProduct = $('#refProduct').val();

    if (!idProduct || idProduct == 0) {
      toastr.error('Seleccione un producto');
      return false;
    }

    let percentage = parseFloat(this.value) / 100;
    let key = $(this).attr("id").split("-")[1];
     
    let price = prices[key].original_price;

    let value = (price * (1 - percentage));

    let typePrice = sessionStorage.getItem('typePrice');
    typePrice == '2' ? max = 2 : max = 0;

    // prices[key].partial_price = value;
    prices[key] = { ...prices[key], partial_price: value };
    $(`#price-${key}`).val(value.toFixed(max)).blur();
  });
});
