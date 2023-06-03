$(document).ready(function () {
  $('.cardTableSimulator').hide();

  /* Modal Simulador */
  $('#btnSimulate').click(function (e) {
    e.preventDefault();

    let product = $('#product').val();

    if (!product) {
      toastr.error('Seleccione un producto antes de simular');
      return false;
    }

    $('.cardTableSimulator').hide();
    $('#modalSimulator').modal('show');
  });

  $('.closeModalSimulator').click(function (e) {
    e.preventDefault();

    $('#modalSimulator').modal('hide');
    dataSimulator = dataBDSimulator;
  });

  $(document).on('click', '.btn-outline-light', async function () {
    $('.cardTableSimulator').hide();
    $('.cardAddSimulator').hide(800);

    // dataSimulator = dataBDSimulator;

    op = this.id;

    let cardMapping = {
      1: {
        data: 'products',
        loader: loadTblSimulatorProducts,
      },
      2: {
        data: 'productsProcess',
        loader: loadTblSimulatorMachines,
      },
      3: {
        data: 'materials',
        loader: loadTblSimulatorMaterials,
      },
      4: {
        data: 'materials',
        loader: loadTblSimulatorProductsMaterials,
      },
      5: {
        data: 'productsProcess',
        loader: loadTblSimulatorProductsProcess,
      },
      6: {
        data: 'factoryLoad',
        loader: loadTblSimulatorFactoryLoad,
      },
      7: {
        data: 'externalServices',
        loader: loadTblSimulatorExternalServices,
      },
      8: { data: 'payroll', loader: loadTblSimulatorPayroll },
      9: {
        data: 'expensesDistribution',
        loader: loadTblSimulatorDistribution,
      },
      10: {
        data: 'expenseRecover',
        loader: loadTblSimulatorRecover,
      },
    };

    cardData = cardMapping[op];
    if (cardData) {
      await cardData.loader(dataSimulator[cardData.data]);
    }

    $('.cardTableSimulator').show(800);

    setTimeout(() => {
      let tables = document.getElementsByClassName(
        'dataTables_scrollHeadInner'
      );

      for (let i = 0; i < tables.length; i++) {
        let attr = tables[i];
        attr.style.width = '100%';
        attr = tables[i].firstElementChild;
        attr.style.width = '100%';
      }
    }, 1000);
  });

  /* Maquinas */
  machineCalculations = (dataMachine, dataFactoryLoad) => {
    dataFactoryLoad = dataFactoryLoad.reduce(function (result, current) {
      let existing = result.find(function (item) {
        return item.id_machine === current.id_machine;
      });

      if (existing) {
        existing.cost += current.cost;
      } else {
        result.push({
          id_machine: current.id_machine,
          cost: current.cost,
        });
      }
      return result;
    }, []);

    // Calcular costo x minuto carga fabril
    dataFactoryLoad.length == 0
      ? (count = 1)
      : (count = dataFactoryLoad.length);

    let cost_indirect_cost = 0;
    for (let i = 0; i < count; i++) {
      let cost_minute = 0;
      let minute_depreciation = 0;
      let cost_factory = 0;

      for (let j = 0; j < dataMachine.length; j++) {
        if (!dataFactoryLoad[i]) cost_factory = 0;
        else if (dataFactoryLoad[i].id_machine == dataMachine[j].id_machine)
          cost_factory = dataFactoryLoad[i].cost;

        cost_minute =
          cost_factory /
          dataMachine[j].days_machine /
          dataMachine[j].hours_machine /
          60;

        if (dataFactoryLoad[i])
          dataSimulator.factoryLoad[i].cost_minute = cost_minute;

        // Calcular minuto de depreciacion
        minute_depreciation =
          (dataMachine[j].cost_machine - dataMachine[j].residual_value) /
          (dataMachine[j].years_depreciation * 12) /
          dataMachine[j].hours_machine /
          dataMachine[j].days_machine /
          60;

        dataSimulator.dataMachine[j].minute_depreciation = minute_depreciation;

        // Calcular costo indirecto
        cost_indirect_cost +=
          (cost_minute + minute_depreciation) * dataMachine[j].operation_time;
      }
    }
    dataSimulator.products[0].cost_indirect_cost = cost_indirect_cost;
  };

  /* Materiales */
  materialsCalculations = (data) => {
    // calculo costo materias prima
    let cost = 0;
    for (let i = 0; i < data.length; i++) {
      let quantity = convertUnitsMaterials(data[i]);

      cost += quantity * data[i].cost_material;
    }

    dataSimulator.products[0].cost_materials = cost;
  };

  /* Calculo mano de obra */
  calcWorkforce = (dataPayroll, dataProductProcess) => {
    let cost_workforce = 0;

    for (let i = 0; i < dataProductProcess.length; i++) {
      for (let j = 0; j < dataPayroll.length; j++) {
        if (dataProductProcess[i].id_process == dataPayroll[j].id_process) {
          let cost =
            dataPayroll[j].minute_value *
            (dataProductProcess[i].enlistment_time +
              dataProductProcess[i].operation_time);

          cost_workforce += cost;
        }
      }
    }

    dataSimulator.products[0].cost_workforce = cost_workforce;
  };

  /* Calculo gasto asignable */
  expenseDistribution = (data, total_expense) => {
    let units_sold = 0;
    let turnover = 0;

    for (let i = 0; i < data.length; i++) {
      units_sold += data[i].units_sold;
      turnover += data[i].turnover;
    }

    let percentageUnitSolds = 0;
    let percentageVolSolds = 0;
    let average = 0;
    let assignableExpense = 0;

    for (let i = 0; i < data.length; i++) {
      percentageUnitSolds = data[i].units_sold / units_sold;
      percentageVolSolds = data[i].turnover / turnover;
      average = (percentageUnitSolds + percentageVolSolds) / 2;
      averageExpense = average * total_expense;
      assignableExpense = averageExpense / data[i].units_sold;

      data[i].assignable_expense = assignableExpense;

      if (data[i].id_product == dataSimulator.products[0].id_product)
        dataSimulator.products[0].assignable_expense = assignableExpense;
    }
  };

  /* Calculos servicios */
  calcServices = (data) => {
    let cost = 0;
    for (let i = 0; i < data.length; i++) {
      cost += parseInt(data[i].cost);
    }

    dataSimulator.products[0].services = cost;
  };
});
