$(document).ready(function () {
  $('#btnSaveSimulator').click(function (e) {
    e.preventDefault();
    let dataMachine = dataSimulator.productsProcess.filter(
      (item) => item.machine !== 'PROCESO MANUAL'
    );
    // dataMachine = dataMachine.filter(
    //   (obj, index, self) =>
    //     index === self.findIndex((o) => o.id_machine === obj.id_machine)
    // );

    machineCalculations(dataMachine, dataSimulator.factoryLoad);

    materialsCalculations(dataSimulator.materials);

    calcWorkforce(dataSimulator.payroll, dataSimulator.productsProcess);

    expenseDistribution(
      dataSimulator.expensesDistribution,
      dataSimulator.totalExpense
    );

    calcServices(dataSimulator.externalServices);

    setDataDashboardSimulator(dataSimulator.products[0]);
    $('#modalSimulator').modal('hide');
    toastr.success('Datos guardados correctamente');
  });

  $(document).on('click keyup', '.inputSimulator', function () {
    let data = 1 * parseFloat(this.value);

    let key = getLastText(this.className);
    let text = this.className.split(' ');
    let id = text[text.length - 2];

    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese un valor valido');
      if (key == '0')
        $(`#${this.id}`).val(dataSimulator[cardData.data][key][this.id]);
      else
        for (let i = 0; i < dataSimulator[cardData.data].length; i++) {
          if (dataSimulator[cardData.data][i][id] == key)
            $(`#${this.id}`).val(dataSimulator[cardData.data][i][this.id]);
        }
      return false;
    }

    if (key == '0')
      dataSimulator[cardData.data][key][this.id] = parseFloat(this.value);
    else {
      for (let i = 0; i < dataSimulator[cardData.data].length; i++) {
        if (dataSimulator[cardData.data][i][id] == key) {
          dataSimulator[cardData.data][i][this.id] = parseFloat(this.value);

          if (id == 'id_payroll') {
            let type_salary = sessionStorage.getItem('type_salary');

            if (type_salary) {
              let payroll = calcSalaryNetSimulator(
                dataSimulator[cardData.data][i]
              );

              dataSimulator[cardData.data][i].factor_benefit = payroll.factor;
              dataSimulator[cardData.data][i].salary_net = payroll.salary_net;
              dataSimulator[cardData.data][i].minute_value =
                payroll.minute_value;
            }
          }
        }
      }
    }
  });
});
