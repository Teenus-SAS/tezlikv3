$(document).ready(function () {
  /* Modal Simulador */
  $('#btnSimulate').click(function (e) {
    e.preventDefault();

    let product = $('#product').val();

    if (!product) {
      toastr.error('Seleccione un producto antes de simular');
      return false;
    }

    $('#modalSimulator').modal('show');
  });

  $('.cardTableSimulator').hide();
  $('.cardGeneralBtnsSimulator').show();

  $(document).on('click', '.bt-outline-secondary', async function () {
    let op = this.value;
    let url;

    switch (op) {
      case 1:
        url = '/api/products';
      case 2:
        url = '/api/machines';
      case 3:
        url = '/api/materials';
      case 4:
        url = '/api/productsMaterials/${idProduct}';
      case 5:
        url = '/api/productsProcess/${idProduct}';
      case 6:
        url = '/api/factoryLoad';
      case 7:
        url = '/api/externalServices/${idProducts}';
      case 8:
        url = '/api/payroll';
      case 9:
        url = '/api/expensesDistribution';
      case 10:
        url = '/api/expensesRecover';
    }

    let data = await searchData(url);
  });
});
