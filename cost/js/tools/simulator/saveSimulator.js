$(document).ready(function () {
  $('#btnSaveSimulator').click(function (e) {
    e.preventDefault();

    machineCalculations(
      dataSimulator.productsProcess,
      dataSimulator.factoryLoad
    );

    materialsCalculations(dataSimulator.materials);

    // calcWorkforce(dataSimulator.payroll, dataSimulator.productsProcess);

    // expenseDistribution(
    //   dataSimulator.expenseDistribution,
    //   dataSimulator.totalExpense
    // );

    setDataDashboardSimulator(dataSimulator.products[0]);
    $('#modalSimulator').modal('hide');
    toastr.success('Datos guardados correctamente');
  });

  $(document).on('click keyup', '.inputSimulator', function () {
    let key = getLastText(this.className);
    if (key == '0') dataSimulator[cardData.data][key][this.id] = this.value;
    else {
      let text = this.className.split(' ');
      let id = text[text.length - 2];

      for (let i = 0; i < dataSimulator[cardData.data].length; i++) {
        if (dataSimulator[cardData.data][i][id] == key)
          dataSimulator[cardData.data][i][this.id] = this.value;
      }
    }
  });
});
