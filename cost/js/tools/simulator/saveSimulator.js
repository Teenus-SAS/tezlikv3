$(document).ready(function () {
  $('#btnSaveSimulator').click(function (e) {
    e.preventDefault();
    let dataMachine = dataSimulator.productsProcess.filter(
      (item) => item.machine !== 'PROCESO MANUAL'
    );

    dataSimulator.dataMachine = dataMachine;
    machineCalculations(dataMachine, dataSimulator.factoryLoad);

    dataMachine = dataMachine.filter(
      (obj, index, self) =>
        index === self.findIndex((o) => o.id_machine === obj.id_machine)
    );

    dataSimulator.dataMachine = dataMachine;

    materialsCalculations(dataSimulator.materials);

    calcWorkforce(dataSimulator.payroll, dataSimulator.productsProcess);

    expenseDistribution(
      dataSimulator.expensesDistribution,
      dataSimulator.totalExpense
    );

    calcServices(dataSimulator.externalServices);

    setDataDashboardSimulator(dataSimulator.products[0]);
    $('#modalSimulator').modal('hide');
    $('.cardAddSimulator').show(800);
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

  $('#btnAddSimulator').click(function (e) {
    e.preventDefault();

    bootbox.confirm({
      title: 'Guardar Datos',
      message:
        'Está seguro de guardar esta simulación? Esta acción no se puede reversar.',
      buttons: {
        confirm: {
          label: 'Si',
          className: 'btn-success',
        },
        cancel: {
          label: 'No',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          $('.cardBottons').hide(400);

          let header = document.getElementById('cardHeader');

          header.insertAdjacentHTML(
            'beforeend',
            `<div class="col-sm-1 cardLoading">
              <div class="spinner-border text-secondary" role="status">
                  <span class="sr-only">Loading...</span>
              </div>
            </div>`
          );

          $.ajax({
            type: 'POST',
            url: '/api/addSimulator',
            data: { simulator: dataSimulator },
            success: function (resp) {
              message(resp);
            },
          });
        }
      },
    });
  });

  message = (data) => {
    $('.cardLoading').remove();
    $('.cardBottons').show(400);

    if (data.success == true) {
      setTimeout(() => {
        window.location.reload();
      }, 2000);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
