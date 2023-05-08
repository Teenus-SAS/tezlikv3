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

  $(document).on('click', '.btn-outline-secondary', function () {
    $('.cardTableSimulator').hide();

    let op = this.value;

    switch (op) {
      case '1':
        card = 'cardTableSimulatorProducts';
        break;
      case '2':
        card = 'cardTableSimulatorMachines';
        break;
      case '3':
        card = 'cardTableSimulatorMaterials';
        break;
      case '4':
        card = 'cardTableSimulatorProductsMaterials';
        break;
      case '5':
        card = 'cardTableSimulatorProductsProcess';
        break;
      case '6':
        card = 'cardTableSimulatorFactoryLoad';
        break;
      case '7':
        card = 'cardTableSimulatorServices';
        break;
      case '8':
        card = 'cardTableSimulatorPayroll';
        break;
      case '9':
        card = 'cardTableSimulatorExpensesDistribution';
        break;
      case '10':
        card = 'cardTableSimulatorExpensesRecover';
        break;
    }

    $(`.${card}`).show(800);
  });
});
