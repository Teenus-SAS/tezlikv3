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

  /* General */
  $(document).on('click keyup', '.inputSimulator', function () {
    let value = 1 * parseFloat(strReplaceNumber(this.value));

    let key = getLastText(this.className);
    let text = this.className.split(' ');
    let id = text[text.length - 2];

    let input = this.id;

    while (this.id.includes('-')) {
      this.id = this.id.substring(0, this.id.length - 1);
    }

    if (isNaN(value) || value <= 0) {
      toastr.error('Ingrese un valor valido');
      if (key == '0')
        $(`#${input}`).val(dataSimulator[cardData.data][key][this.id]);
      else
        for (let i = 0; i < dataSimulator[cardData.data].length; i++) {
          if (dataSimulator[cardData.data][i][id] == key)
            $(`#${input}`).val(
              dataSimulator[cardData.data][i][this.id].toLocaleString('es-CO')
            );
        }
      return false;
    }

    if (key == '0')
      dataSimulator[cardData.data][key][this.id] = parseFloat(value);
    else {
      for (let i = 0; i < dataSimulator[cardData.data].length; i++) {
        if (dataSimulator[cardData.data][i][id] == key) {
          dataSimulator[cardData.data][i][this.id] = parseFloat(value);

          if (id == 'id_payroll') {
            let payroll = calcSalaryNetSimulator(
              dataSimulator[cardData.data][i]
            );

            dataSimulator[cardData.data][i].factor_benefit = payroll.factor;
            dataSimulator[cardData.data][i].salary_net = payroll.salary_net;
            dataSimulator[cardData.data][i].minute_value = payroll.minute_value;
          }
        }
      }
    }
  });

  /* Ficha tecnica procesos */
  $(document).on('change', '#machines', function () {
    let key = getLastText(this.className);
    let text = this.className.split(' ');
    let id = text[text.length - 2];

    let dataMachines = sessionStorage.getItem('dataMachines');

    dataMachines = JSON.parse(dataMachines);

    for (let i = 0; i < dataMachines.length; i++) {
      if (dataMachines[i].id_machine == this.value) {
        dataMachines = dataMachines[i];
        break;
      }
    }

    for (let i = 0; i < dataSimulator[cardData.data].length; i++) {
      if (dataSimulator[cardData.data][i][id] == key) {
        dataSimulator[cardData.data][i]['id_machine'] = parseFloat(this.value);
        dataSimulator[cardData.data][i]['machine'] =
          this.value == 0 ? 'PROCESO MANUAL' : dataMachines.machine.trim();
        dataSimulator[cardData.data][i]['cost_machine'] =
          this.value == 0 ? 0 : parseFloat(dataMachines.cost);
        dataSimulator[cardData.data][i]['years_depreciation'] =
          this.value == 0 ? 0 : parseFloat(dataMachines.years_depreciation);
        dataSimulator[cardData.data][i]['residual_value'] =
          this.value == 0 ? 0 : parseFloat(dataMachines.residual_value);
        dataSimulator[cardData.data][i]['minute_depreciation'] =
          this.value == 0 ? 0 : parseFloat(dataMachines.minute_depreciation);
        dataSimulator[cardData.data][i]['hours_machine'] =
          this.value == 0 ? 0 : parseFloat(dataMachines.hours_machine);
        dataSimulator[cardData.data][i]['days_machine'] =
          this.value == 0 ? 0 : parseFloat(dataMachines.days_machine);
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
            url: '/api/simulator/addSimulator',
            data: { simulator: dataSimulator },
            success: function (resp) {
              message(resp);
            },
          });
        }
      },
    });
  });

  $(document).on('click', '.btnCreateDataSimulator', async function (e) {
    e.preventDefault();
    let form = document.getElementsByClassName('data');

    let data = new Object();

    data.id_product = dataSimulator.products[0].id_product;

    for (let i = 0; i < form.length; i++) {
      let status = true;
      let value = form[i].value;
      let text = /[a-zA-Z]/.test(value);

      if (text == false) {
        value = 1 * parseFloat(strReplaceNumber(form[i].value));
        isNaN(value) ? (status = false) : status;
      } else value == '' ? (status = false) : status;

      if (status == false) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      data[form[i].id] = value;
    }

    if (this.id == 'family')
      for (let i = 0; i < dataSimulator[cardData.data].length; i++) {
        if (data.id_family == dataSimulator[cardData.data][i].id_family) {
          dataSimulator.products[0].id_family = data.id_family;
          break;
        }
      }
    else dataSimulator[cardData.data].push(data);

    toastr.success('Datos agregados correctamente');
    $('#formDataSimulator').trigger('reset');
    await cardData.loader(dataSimulator[cardData.data]);
  });

  message = (data) => {
    if (data.reload) {
      location.reload();
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);

    if (data.success == true) {
      // setTimeout(() => {
      //   window.location.reload();
      // }, 4000);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
