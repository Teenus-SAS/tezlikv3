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
        await loadTblSimulatorProducts(data.products);
        break;
      case '2':
        card = 'cardTableSimulatorMachines';
        await loadTblSimulatorMachines(data.productsProcess);
        break;
      case '3':
        card = 'cardTableSimulatorMaterials';
        await loadTblSimulatorMaterials(data.materials);
        break;
      case '4':
        card = 'cardTableSimulatorProductsMaterials';
        await loadTblSimulatorProductsMaterials(data.materials);
        break;
      case '5':
        card = 'cardTableSimulatorProductsProcess';
        await loadTblSimulatorProductsProcess(data.productsProcess);
        break;
      case '6':
        card = 'cardTableSimulatorFactoryLoad';
        await loadTblSimulatorFactoryLoad(data.factoryLoad);
        break;
      case '7':
        card = 'cardTableSimulatorServices';
        await loadTblSimulatorExternalServices(data.externalServices);
        break;
      case '8':
        card = 'cardTableSimulatorPayroll';
        await loadTblSimulatorPayroll(data.payroll);
        break;
      case '9':
        card = 'cardTableSimulatorExpensesDistribution';
        await loadTblSimulatorDistribution(data.expensesDistribution);
        break;
      case '10':
        card = 'cardTableSimulatorExpensesRecover';
        await loadTblSimulatorRecover(data.expenseRecover);
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
});
