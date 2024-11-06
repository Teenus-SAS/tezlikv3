$(document).ready(function () {
  /* Calculo 
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
        $(`#profitMargin-${row}`).html('');

        toastr.error('Ingrese un valor mayor a cero');
        return false;
      }

      if (id.includes('unity')) {
        this.value == '' ? (unity = '0') : (unity = this.value);
        unity = parseInt(strReplaceNumber(unity));

        let profitMargin = { 1: 1, 2: 1, 3: 0.5, 4: 0.333333333333333, 5: 0.5 }; 

        unitys[row] = unity;

        for (let i = row; i < 5; i++) {
          unity = unity * (1 + profitMargin[i + 1]);

          $(`#unity-${i + 1}`).val(parseInt(unity));

          unitys[i + 1] = unity;
        }
      } else {
        let price = parseInt(this.value);

        let typePrice = sessionStorage.getItem('typePrice');
        typePrice == '2' ? max = 2 : max = 0;

        prices[row] = price;
        for (let i = row; i < 5; i++) {
          prices[i + 1] = price;
          $(`#price-${i + 1}`).val(price.toFixed(max));
        }
      }
      if (id != 'unity-0') generalCalc(1);
    } catch (error) {
      console.log(error);
    }
  }); */
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

      if (id.includes('unity')) {
        updateUnity(row, value);
      } else {
        updatePrice(row, value);
      }

      if (id !== 'unity-0') generalCalc(1);
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

    prices[row] = price;
    for (let i = row; i < 5; i++) {
      prices[i + 1] = price;
      $(`#price-${i + 1}`).val(price.toFixed(max));
    }
  }

  /* generalCalc = async (op) => {
    try {
      op == 0 ? (count = 0) : (count = 5);

      let typePrice = sessionStorage.getItem('typePrice');
      typePrice == '2' ? max = 2 : max = 0;

      var startTime = performance.now();

      // Definir una función asíncrona para manejar cada iteración del ciclo
      const handleIteration = async (i) => {
        let unit = unitys[i];
        let price = prices[i];

        if (unit > 0 && price > 0) {
          // Costos Variables

          let totalVariableCost = variableCost * unit;
          $(`#variableCosts-${i}`).html(
            `$ ${totalVariableCost.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          // Total Costos y Gastos
          let totalCostsAndExpense = fixedCost + totalVariableCost;

          $(`#totalCostsAndExpenses-${i}`).html(
            `$ ${totalCostsAndExpense.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          // Calculo Total Ingresos
          totalRevenue = unit * price;

          $(`#totalRevenue-${i}`).html(
            `$ ${totalRevenue.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          // Calculo Costo x Unidad
          unityCost = parseFloat(totalCostsAndExpense) / parseFloat(unit);

          $(`#unityCost-${i}`).html(
            `$ ${unityCost.toLocaleString('es-CO', {
              maximumFractionDigits: max,
            })}`
          );

          // Calculo Utilidad x Unidad
          let unitUtility = price - unityCost;
          $(`#unitUtility-${i}`).html(
            `$ ${unitUtility.toLocaleString('es-CO', {
              maximumFractionDigits: max,
            })}`
          );

          // Calculo Utilidad Neta
          let netUtility = unitUtility * unit;

          netUtility < 0
            ? $(`#netUtility-${i}`).css('color', 'red')
            : $(`#netUtility-${i}`).css('color', 'black');

          $(`#netUtility-${i}`).html(
            `$ ${netUtility.toLocaleString('es-CO', {
              maximumFractionDigits: 0,
            })}`
          );

          // Margen de Utilidad
          profitMargin = (netUtility / totalRevenue) * 100;

          $(`#profitMargin-${i}`).html(
            `${profitMargin.toLocaleString('es-CO', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })} %`
          );

          // Verificar si el margen de utilidad es mayor o igual a la rentabilidad
          if (i == 0 && profitMargin >= profitability) {
            return false;
          }
          // Verificar si el margen de utilidad es negativo
          else if (i == 0 && profitMargin < profitability) {
            profitMargin > 0 ? cant += 2 : cant = 1;

            let division = Math.ceil((totalCostsAndExpense / price) + cant);

            if (division > 10000000) {
              toastr.error('Precios muy por debajo de lo requerido. Si se sigue calculando automáticamente generará números demasiado grandes');
              return false;
            } else {
              typeExpense == '2' ? division = Math.ceil(division / 12) : division;
              
              $(`#unity-${i}`).val(division.toLocaleString('es-CO', {
                maximumFractionDigits: 0,
              }));

              unitys[i] = division;

              var endTime = performance.now();
              var mSeconds = endTime - startTime;
              var seconds = mSeconds / 1000;

              if (seconds > 5) {
                return false;
              } else {
                await new Promise(resolve => setTimeout(resolve, 0));
                await $(`#unity-${i}`).blur();
              }
            }
          }
          return true;
        }
      };

      // Iterar sobre cada índice
      for (i = op; i <= count; i++) {
        const result = await handleIteration(i);
        if (!result) break;
        else if (op == 0)
          i = i - 1;
      }

      if (op == 0)
        $('#unity-1').blur();

      $('.cardLoading').remove(); 
      checkPrices();
    } catch (error) {
      console.log(error);
    }
  }; */

  generalCalc = async (op) => {
    try {
      const count = op === 0 ? 0 : 5;
      const typePrice = sessionStorage.getItem('typePrice');
      const maxDecimals = typePrice === '2' ? 2 : 0;
      const startTime = performance.now();
      const maxTimeLimit = 5; // segundos

      const formatCurrency = (amount, decimals = 0) =>
        `$ ${amount.toLocaleString('es-CO', { maximumFractionDigits: decimals })}`;

      const handleIteration = async (i) => {
        const unit = unitys[i];
        const price = prices[i];

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

          if (i === 0 && profitMargin >= profitability) return false;

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
    let idProduct = $('#refProduct').val();

    if (!idProduct || idProduct == 0) {
      toastr.error('Seleccione un producto');
      return false;
    }

    let percentage = parseFloat(this.value) / 100;
    let key = $(this).attr("id").split("-")[1];
    
    let unit = parseInt($(`#unity-${key}`).val());

    let price = (unit * (1 - percentage));

    let typePrice = sessionStorage.getItem('typePrice');
    typePrice == '2' ? max = 2 : max = 0;

    $(`#price-${key}`).val(price.toFixed(max)).blur();
  });
});
