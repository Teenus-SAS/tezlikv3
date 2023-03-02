$(document).ready(function () {
  /* Ingresar asignacion de gasto manual */
  $(document).on('keyup', '#expenseAssignation', function () {
    $('.general').val('');
    $('.general').html('');

    expenseAsignation = this.value;

    expenseAsignation = parseFloat(strReplaceNumber(expenseAsignation));

    $('#expenses').html(
      `$ ${expenseAsignation.toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })}`
    );
  });

  $(document).on('click keyup', '.soldUnits', function () {
    try {
      let id = this.id;
      if (expenseAsignation == 0 || isNaN(expenseAsignation)) {
        toastr.error('Ingrese gasto general');
        $(`#${id}`).val('');
        return false;
      }

      let row = id.slice(9, id.length);
      $(`.row-${row}`).html('');

      for (let i = 0; i < data.length; i++) {
        data[i]['soldUnit'] = 0;
        data[i]['participation'] = 0;
        data[i]['average'] = 0;
        data[i]['unitsToSold'] = 0;
        data[i]['percentage'] = 0;

        let unit = parseInt($(`#soldUnit-${i}`).val());

        if (unit > 0) {
          unit = parseInt(unit);

          data[i]['soldUnit'] = unit;

          let totalUnitsSold = sumTotalSoldUnits();

          // Calcular porcentaje de participacion
          let participation = (unit / totalUnitsSold) * 100;

          data[i]['participation'] = participation;
          $(`#part-${i}`).html(
            `${participation.toLocaleString('es-CO', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })} %`
          );
          sumTotalParticipation();

          // Calculo promedio ponderado
          let average =
            (data[i].price - data[i].variable_cost) * (participation / 100);

          data[i]['average'] = average;
          $(`#aver-${i}`).html(
            average.toLocaleString('es-CO', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })
          );
        }

        for (let i = 0; i < data.length; i++) {
          let unit = parseInt($(`#soldUnit-${i}`).val());

          if (unit > 0) {
            let totalAverages = sumTotalAverages();

            // Calcular total Unidades
            totalUnits = expenseAsignation / totalAverages;

            // Calcular unidades a vender
            let unitsToSold = (data[i]['participation'] / 100) * totalUnits;
            data[i]['unitsToSold'] = unitsToSold;
            $(`#unitTo-${i}`).html(
              unitsToSold.toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
              })
            );

            let percentage = (unit / unitsToSold) * 100;
            data[i]['percentage'] = percentage;
          }
        }

        $('#totalUnits').html(
          totalUnits.toLocaleString('es-CO', {
            maximumFractionDigits: 2,
          })
        );

        sumTotalUnits();
      }
    } catch (error) {
      console.log(error);
    }
  });

  /* Sumar total unidades */
  sumTotalSoldUnits = () => {
    let totalSoldsUnits = 0;

    for (let i = 0; i < data.length; i++) {
      let unit = $(`#soldUnit-${i}`).val();

      unit == '' || unit == undefined ? (unit = 0) : unit;

      totalSoldsUnits += parseInt(unit);
    }

    $('#totalSoldsUnits').html(totalSoldsUnits.toLocaleString());

    return totalSoldsUnits;
  };

  /* Sumar total porcentaje de participacion */
  sumTotalParticipation = () => {
    let totalParticipation = 0;

    for (let i = 0; i < data.length; i++) {
      let participation = data[i].participation;

      participation == '' || participation == undefined
        ? (participation = 0)
        : participation;

      totalParticipation += participation;
    }

    $('#totalParticipation').html(
      `${totalParticipation.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })} %`
    );
  };

  /* Sumar total promedio ponderado */
  sumTotalAverages = () => {
    let totalAverages = 0;

    for (let i = 0; i < data.length; i++) {
      let average = data[i].average;

      average == '' || average == undefined ? (average = 0) : average;

      totalAverages += average;
    }

    $('#totalAverages').html(
      totalAverages.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );

    return totalAverages;
  };

  /* Sumar total unidades a vender */
  sumTotalUnits = () => {
    let totalSumUnits = 0;

    for (let i = 0; i < data.length; i++) {
      let unitsToSold = data[i].unitsToSold;

      unitsToSold == '' || unitsToSold == undefined
        ? (unitsToSold = 0)
        : unitsToSold;

      totalSumUnits += unitsToSold;
    }

    $('#totalSumUnits').html(
      totalSumUnits.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })
    );
  };
});
