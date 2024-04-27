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

        // id != 'unity-0'
        //   ? (unity = parseInt(strReplaceNumber(unity)))
        //   : (unity = unitys[0]);

        let percentage = { 1: 1, 2: 1, 3: 0.5, 4: 0.333333333333333, 5: 0.5 };
        // unitys = [1];

        unitys[row] = unity;

        for (let i = row; i < 5; i++) {
          unity = unity * (1 + percentage[i + 1]);

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
  }); 

  /*generalCalc = async (op) => {
    try {
      op == 0 ? (count = 0) : (count = 5);

      let typePrice = sessionStorage.getItem('typePrice');
      typePrice == '2' ? max = 2 : max = 0;
      
      var startTime = performance.now();

      for (i = op; i <= count; i++) {
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

          // Porcentaje  
          percentage = (netUtility / totalRevenue) * 100;
           
          $(`#percentage-${i}`).html(
            `${percentage.toLocaleString('es-CO', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })} %`
          );

          // Verificar si el margen de utilidad es negativo
          if (i == 0 && percentage >= profitability) {
            // percentage += profitability;
            $(`#percentage-${i}`).html(
              `${percentage.toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })} %`
            );
          } else if (i == 0 && percentage < profitability) {
            let cant = 1;
            
            percentage > 0 ? cant += 2 : cant = 1;
 
            let division = Math.ceil((totalCostsAndExpense / price) + cant);

            if (division > 10000000) {
              toastr.error('Precios muy por debajo de lo requerido. Si se sigue calculando automaticamente generara numeros demasiado grandes');
              break;
            } else {
              $(`#unity-${i}`).val(division.toLocaleString('es-CO', {
                maximumFractionDigits: 0,
              }));

              unitys[i] = division;

              var endTime = performance.now();
              var mSeconds = endTime - startTime;
              var seconds = mSeconds / 1000;

              if (seconds > 5) {
                // toastr.error('Precios muy por debajo de lo requerido. Revise los costos fijos');
                break;
              } else {
                await $(`#unity-${i}`).blur();
              }
            }
            i = i - 1;
          }
        }
      }

      $('.cardLoading').remove();
      $('.cardBottons').show(400);
    } catch (error) {
      console.log(error);
    }
  }; */

  /* */ generalCalc = async (op) => {
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
              maximumFractionDigits: max,
            })}`
          );

          /* Calculo Utilidad x Unidad */
          let unitUtility = price - unityCost;
          $(`#unitUtility-${i}`).html(
            `$ ${unitUtility.toLocaleString('es-CO', {
              maximumFractionDigits: max,
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
          // percentage = (netUtility / (totalRevenue - commission)) * 100;
          percentage = (netUtility / totalRevenue) * 100;

          $(`#percentage-${i}`).html(
            `${percentage.toLocaleString('es-CO', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })} %`
          );

          // Verificar si el margen de utilidad es mayor o igual a la rentabilidad
          if (i == 0 && percentage >= profitability) {
            return false;
          }
          // Verificar si el margen de utilidad es negativo
          else if (i == 0 && percentage < profitability) { 
            percentage > 0 ? cant += 2 : cant = 1;

            let division = Math.ceil((totalCostsAndExpense / price) + cant);

            if (division > 10000000) {
              toastr.error('Precios muy por debajo de lo requerido. Si se sigue calculando automáticamente generará números demasiado grandes');
              return false;
            } else {
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
        else if(op == 0)
          i = i - 1;
      }

      if(op == 0)
        $('#unity-1').blur();

      $('.cardLoading').remove();
      $('.cardBottons').show(400);
    } catch (error) {
      console.log(error);
    }
  };
});
