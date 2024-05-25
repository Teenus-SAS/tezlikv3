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
    $('#cardAddDataSimulator').empty();
    $('.cardAddDataSimulator').hide();

    // dataSimulator = dataBDSimulator;

    op = this.id;

    flag_expense_distribution == 2
      ? (distribution = loadTblSimulatorDistributionFamily)
      : (distribution = loadTblSimulatorDistribution);

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
        loader: distribution,
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
 
    let cost_indirect_cost = 0;
 
    let cost_minute = 0;
    let minute_depreciation = 0;
    let cost_factory = 0;
    let factory_a_machine = 0;
    let efficiency = 0;
    let total_time = 0;
    let process_machine_indirect_cost = 0;

    for (let i = 0; i < dataMachine.length; i++) { 
      let arr = dataFactoryLoad.find(item => item.id_machine == dataMachine[i].id_machine);
      
      !arr ? cost_factory = 0 : cost_factory = arr.cost;
      
      // Calcular costo x minuto carga fabril
      cost_minute =
        (cost_factory /
          dataMachine[i].days_machine /
          dataMachine[i].hours_machine /
          60);

      const index = dataSimulator.factoryLoad.findIndex(item => item.id_machine === dataMachine[i].id_machine);

      if (index !== -1) {
        dataSimulator.factoryLoad[index].cost_minute = cost_minute;
      }

      // Calcular minuto de depreciacion
      minute_depreciation =
        (dataMachine[i].cost_machine - dataMachine[i].residual_value) /
        (dataMachine[i].years_depreciation * 12) /
        dataMachine[i].hours_machine /
        dataMachine[i].days_machine /
        60;

      dataSimulator.dataMachine[i].minute_depreciation = minute_depreciation;

      // Calcular costo indirecto
      factory_a_machine = cost_minute + minute_depreciation;

      dataMachine[i].efficiency == 0 ? efficiency = 100 : efficiency = dataMachine[i].efficiency;

      total_time = (dataMachine[i].enlistment_time + dataMachine[i].operation_time) / (efficiency / 100);

      process_machine_indirect_cost = factory_a_machine * total_time;

      cost_indirect_cost += process_machine_indirect_cost;
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
      let efficiency = 0;
      dataProductProcess[i].efficiency == 0 ? efficiency = 1 : efficiency = parseFloat(dataProductProcess[i].efficiency) / 100;

      for (let j = 0; j < dataPayroll.length; j++) {
        if (dataProductProcess[i].id_process == dataPayroll[j].id_process) {
          let cost =
            dataPayroll[j].minute_value *
            ((dataProductProcess[i].enlistment_time +
              dataProductProcess[i].operation_time) / efficiency);

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

      isNaN(assignableExpense) ? assignableExpense = 0 : assignableExpense;

      data[i].assignable_expense = assignableExpense;

      if (flag_expense_distribution == 2) {
        if (data[i].id_family == dataSimulator.products[0].id_family)
          dataSimulator.products[0].assignable_expense = assignableExpense;
      } else {
        if (data[i].id_product == dataSimulator.products[0].id_product)
          dataSimulator.products[0].assignable_expense = assignableExpense;
      }
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
