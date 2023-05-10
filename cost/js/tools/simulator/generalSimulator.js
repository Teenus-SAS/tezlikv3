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

    let navbarToggleExternalContent = document.getElementById(
      'navbarToggleExternalContent'
    );

    $('#modalSimulator').modal('hide');
    navbarToggleExternalContent.classList.remove('show');
  });

  $(document).on('click', '.btn-outline-secondary', async function () {
    $('.cardTableSimulator').hide();

    let op = this.value;

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

    setInterval(() => {
      let tables = document.getElementsByClassName(
        'dataTables_scrollHeadInner'
      );

      for (let i = 0; i < tables.length; i++) {
        let attr = tables[i].firstElementChild;
        attr.style.width = '380px';
      }
    }, 1000);

    $(`.${card}`).show(800);
  });

  $('#btnSaveSimulator').click(function (e) {
    e.preventDefault();
  });
});
