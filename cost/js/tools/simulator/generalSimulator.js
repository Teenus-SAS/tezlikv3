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
  });

  $(document).on('click', '.btn-outline-light', async function () {
    $('.cardTableSimulator').hide();

    let op = this.id;

    switch (op) {
      case '1':
        card = 'cardTableSimulatorProducts';
        await loadTblSimulatorProducts(dataSimulator.products);
        break;
      case '2':
        card = 'cardTableSimulatorMachines';
        await loadTblSimulatorMachines(dataSimulator.productsProcess);
        break;
      case '3':
        card = 'cardTableSimulatorMaterials';
        await loadTblSimulatorMaterials(dataSimulator.materials);
        break;
      case '4':
        card = 'cardTableSimulatorProductsMaterials';
        await loadTblSimulatorProductsMaterials(dataSimulator.materials);
        break;
      case '5':
        card = 'cardTableSimulatorProductsProcess';
        await loadTblSimulatorProductsProcess(dataSimulator.productsProcess);
        break;
      case '6':
        card = 'cardTableSimulatorFactoryLoad';
        await loadTblSimulatorFactoryLoad(dataSimulator.factoryLoad);
        break;
      case '7':
        card = 'cardTableSimulatorServices';
        await loadTblSimulatorExternalServices(dataSimulator.externalServices);
        break;
      case '8':
        card = 'cardTableSimulatorPayroll';
        await loadTblSimulatorPayroll(dataSimulator.payroll);
        break;
      case '9':
        card = 'cardTableSimulatorExpensesDistribution';
        await loadTblSimulatorDistribution(dataSimulator.expensesDistribution);
        break;
      case '10':
        card = 'cardTableSimulatorExpensesRecover';
        await loadTblSimulatorRecover(dataSimulator.expenseRecover);
        break;
    }
    $(`.${card}`).show(800);

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

  $('#btnSaveSimulator').click(function (e) {
    e.preventDefault();
  });
});
