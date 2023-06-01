$(document).ready(function () {
  let totalUnits = 0;
  /* Ingresar asignacion de gasto manual */
  $(document).on('keyup', '#expenseAssignation', function () {
    // $('.general').val('');
    $('.general').html('');

    expenseAsignation = this.value;

    expenseAsignation = parseFloat(strReplaceNumber(expenseAsignation));

    let expenses = expenseAsignation + sumTotalCostFixed;

    $('#expenses').html(
      `$ ${expenses.toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })}`
    );

    $('#soldUnit-0').click();
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

      multiproducts[row].soldUnit = this.value;

      let dataMultiproducts = [];

      for (let i = 0; i < multiproducts.length; i++) {
        // unit = $(`#soldUnit-${i}`).val();
        unit = multiproducts[i].soldUnit;

        multi = {};

        multi.id_product = multiproducts[i]['id_product'];
        multi.soldUnit = unit;
        multi.expense = expenseAsignation;

        let participation = 0;

        // if (unit > 0) {
        let totalUnitsSold = sumTotalSoldUnits();

        // Calcular porcentaje de participacion
        participation = (unit / totalUnitsSold) * 100;
        isNaN(participation) ? (participation = 0) : participation;
        // }
        multiproducts[i]['participation'] = participation;

        multi.participation = participation;
        $(`#part-${i}`).html(
          `${participation.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })} %`
        );

        dataMultiproducts.push(multi);
        sumTotalParticipation();

        // Calculo promedio ponderado
        let average =
          (multiproducts[i].price - multiproducts[i].variable_cost) *
          (participation / 100);

        multiproducts[i]['average'] = average;
      }

      for (let i = 0; i < multiproducts.length; i++) {
        // let unit = parseInt($(`#soldUnit-${i}`).val());
        let unit = multiproducts[i].soldUnit;

        // if (unit > 0) {
        let totalAverages = sumTotalAverages();

        // Calcular total Unidades
        totalUnits = (expenseAsignation + sumTotalCostFixed) / totalAverages;
        totalUnits == Infinity ? (totalUnits = 0) : totalUnits;

        // Calcular unidades a vender
        let unitsToSold =
          (multiproducts[i]['participation'] / 100) * totalUnits;
        multiproducts[i]['unitsToSold'] = unitsToSold;
        $(`#unitTo-${i}`).html(
          unitsToSold.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        let percentage = (unit / unitsToSold) * 100;
        multiproducts[i]['percentage'] = percentage;
        // }
      }

      // if (this.value == '' || !this.value) return false;
      // else {
      $('#totalUnits').html(
        totalUnits.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })
      );

      sumTotalUnits();

      dataMultiproducts.totalUnits = totalUnits;

      saveMultiproducts(dataMultiproducts);
      // }
    } catch (error) {
      console.log(error);
    }
  });

  /* Sumar total unidades */
  sumTotalSoldUnits = () => {
    let totalSoldsUnits = 0;

    for (let i = 0; i < multiproducts.length; i++) {
      // let unit = $(`#soldUnit-${i}`).val();
      let unit = multiproducts[i].soldUnit;

      unit == '' || unit == undefined ? (unit = 0) : unit;

      totalSoldsUnits += parseInt(unit);
    }

    $('#totalSoldsUnits').html(totalSoldsUnits.toLocaleString());

    return totalSoldsUnits;
  };

  /* Sumar total porcentaje de participacion */
  sumTotalParticipation = () => {
    let totalParticipation = 0;

    for (let i = 0; i < multiproducts.length; i++) {
      let participation = multiproducts[i].participation;

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

    for (let i = 0; i < multiproducts.length; i++) {
      let average = multiproducts[i].average;

      average == '' || average == undefined ? (average = 0) : average;

      totalAverages += average;
    }

    return totalAverages;
  };

  /* Sumar total unidades a vender */
  sumTotalUnits = () => {
    let totalSumUnits = 0;

    for (let i = 0; i < multiproducts.length; i++) {
      let unitsToSold = multiproducts[i].unitsToSold;

      unitsToSold == '' || unitsToSold == undefined
        ? (unitsToSold = 0)
        : unitsToSold;

      totalSumUnits += unitsToSold;
    }

    $('#totalSumUnits').html(
      totalSumUnits.toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })
    );
  };
});
