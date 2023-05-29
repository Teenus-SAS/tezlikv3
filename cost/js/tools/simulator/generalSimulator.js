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

    dataSimulator = dataBDSimulator;

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
    for (let i = 0; i < dataFactoryLoad.length; i++) {
      let cost_minute = 0;
      let minute_depreciation = 0;
      let cost_indirect_cost = 0;

      for (let j = 0; j < dataMachine.length; j++) {
        if (dataFactoryLoad[i].id_machine == dataMachine[j].id_machine) {
          cost_minute =
            dataFactoryLoad[i].cost /
            dataMachine[j].days_machine /
            dataMachine[j].hours_machine /
            60;

          // Calcular minuto de depreciacion
          minute_depreciation =
            (dataMachine[j].cost - dataMachine[j].residual_value) /
            (dataMachine[j].years_depreciation * 12) /
            dataMachine[j].hours_machine /
            dataMachine[j].days_machine /
            60;

          // Calcular costo indirecto
          cost_indirect_cost =
            (cost_minute + minute_depreciation) * dataMachine[j].operation_time;

          dataSimulator.products[0].cost_indirect_cost = cost_indirect_cost;
        }
      }
    }
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

  /* Conversion de unidades */
  convertUnitsMaterials = (data) => {
    try {
      let magnitude = data.magnitude;
      let unitProductMaterial = data.abbreviation_p_materials;
      let unitMaterial = data.abbreviation_material;
      let quantity = data.quantity;

      let arr = {};

      if (unitProductMaterial !== unitMaterial && magnitude !== 'UNIDAD') {
        switch (magnitude) {
          case 'LONGITUD':
            arr['M'] = {
              CM: { value: 100, op: '/' },
              ML: { value: 1000, op: '/' },
              INCH: { value: 39.37, op: '/' },
              FT: { value: 3.281, op: '/' },
            };
            arr['CM'] = {
              M: { value: 100, op: '*' },
              ML: { value: 10, op: '/' },
              INCH: { value: 2.54, op: '*' },
              FT: { value: 30.48, op: '*' },
            };
            arr['ML'] = {
              M: { value: 1000, op: '*' },
              CM: { value: 10, op: '*' },
              INCH: { value: 25.4, op: '*' },
              FT: { value: 304.8, op: '*' },
            };
            arr['INCH'] = {
              M: { value: 39.37, op: '*' },
              CM: { value: 2.54, op: '/' },
              ML: { value: 25.4, op: '/' },
              FT: { value: 12, op: '*' },
            };
            arr['FT'] = {
              M: { value: 3.281, op: '*' },
              CM: { value: 38.48, op: '/' },
              ML: { value: 304.8, op: '/' },
              INCH: { value: 12, op: '/' },
            };
            break;
          case 'MASA':
            arr['TN'] = {
              KG: { value: 1000, op: '/' },
              GR: { value: 1000000, op: '/' },
              MG: { value: 1000000000, op: '/' },
              LB: { value: 2205, op: '/' },
            };
            arr['KG'] = {
              TN: { value: 1000, op: '*' },
              GR: { value: 1000, op: '/' },
              MG: { value: 1000000, op: '/' },
              LB: { value: 2.205, op: '/' },
            };
            arr['GR'] = {
              TN: { value: 1000000, op: '*' },
              KG: { value: 1000, op: '*' },
              MG: { value: 1000, op: '/' },
              LB: { value: 453.6, op: '*' },
            };
            arr['MG'] = {
              TN: { value: 1000000000, op: '*' },
              KG: { value: 1000000, op: '*' },
              GR: { value: 1000, op: '*' },
              LB: { value: 453600, op: '*' },
            };
            arr['LB'] = {
              TN: { value: 2205, op: '*' },
              KG: { value: 2.205, op: '*' },
              GR: { value: 453.6, op: '/' },
              MG: { value: 1000, op: '*' },
            };
            break;
          case 'VOLUMEN':
            arr['CM3'] = {
              M3: { value: 1000000, op: '*' },
              L: { value: 1000, op: '*' },
              ML: { value: 1, op: '*' },
            };
            arr['M3'] = {
              CM3: { value: 1000000, op: '/' },
              L: { value: 1000, op: '/' },
              ML: { value: 1000000, op: '/' },
            };
            arr['L'] = {
              CM3: { value: 1000, op: '/' },
              M3: { value: 1000, op: '*' },
              ML: { value: 1000, op: '/' },
            };
            arr['ML'] = {
              CM3: { value: 1, op: '*' },
              M3: { value: 1000000, op: '*' },
              L: { value: 1000, op: '*' },
            };
            break;
          case 'ÁREA':
            arr['DM2'] = {
              M2: { value: 100, op: '*' },
              FT2: { value: 9.29, op: '*' },
              INCH2: { value: 15.5, op: '/' },
            };
            arr['M2'] = {
              DM2: { value: 100, op: '/' },
              FT2: { value: 10.764, op: '/' },
              INCH2: { value: 1550, op: '/' },
            };
            arr['FT2'] = {
              DM2: { value: 9.29, op: '/' },
              M2: { value: 10.764, op: '*' },
              INCH2: { value: 144, op: '/' },
            };
            arr['INCH2'] = {
              DM2: { value: 15.5, op: '*' },
              M2: { value: 1550, op: '*' },
              FT2: { value: 144, op: '*' },
            };
            break;
        }

        let unit = arr[unitMaterial][unitProductMaterial];

        quantity = calcQuantity(quantity, unit.op, unit.value);
        return quantity;
      } else {
        return quantity;
      }
    } catch (error) {
      console.log(error);
    }
  };

  calcQuantity = (num1, operator, num2) => {
    let value;
    if (operator == '/') {
      value = num1 / num2;
    } else if (operator == '*') {
      value = num1 * num2;
    }
    return value;
  };

  /* Calculo mano de obra */
  calcWorkforce = (dataPayroll, dataProductProcess) => {
    dataPayroll = dataPayroll.reduce(function (result, current) {
      let existing = result.find(function (item) {
        return item.id_process === current.id_process;
      });

      if (existing) {
        existing.minute_value += current.minute_value;
      } else {
        result.push({
          id_process: current.id_process,
          minute_value: current.minute_value,
        });
      }
      return result;
    }, []);

    dataProductProcess = dataProductProcess.reduce(function (result, current) {
      let existing = result.find(function (item) {
        return item.id_process === current.id_process;
      });

      if (existing) {
        existing.total_time += current.enlistment_time + current.operation_time;
      } else {
        result.push({
          id_process: current.id_process,
          total_time: current.enlistment_time + current.operation_time,
        });
      }
      return result;
    }, []);

    let cost_workforce = 0;

    for (let i = 0; i < dataProductProcess.length; i++) {
      for (let j = 0; j < dataPayroll.length; j++) {
        if (dataProductProcess[i].id_process == dataPayroll[j].id_process)
          cost_workforce +=
            dataPayroll[j].minute_value *
            (dataProductProcess[i].enlistment_time +
              dataProductProcess[i].operation_time);
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
});
