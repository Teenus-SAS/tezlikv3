$(document).ready(function () {
  // Ocultar card formulario Analisis Inventario ABC
  $('.cardAddMonths').hide();

  // Ocultar botón analisar Inventario ABC
  $('.cardBtnAddMonths').hide();

  $('#btnInvetoryABC').click(function (e) {
    e.preventDefault();
    $('.cardImportInventory').hide(800);
    $('.cardAddMonths').toggle(800);

    $('#formAddMonths').trigger('reset');
  });

  $('#btnAddMonths').click(function (e) {
    e.preventDefault();

    cantMonths = $('#cantMonths').val();

    if (!cantMonths || cantMonths == '') {
      toastr.error('Ingrese cantidad a calcular');
      return false;
    }

    category = $('#category').val();

    if (category.includes('en proceso'))
      products = sessionStorage.getItem('dataProductsInProcess');
    else if (category.includes('terminado'))
      products = sessionStorage.getItem('dataFinishProducts');

    products = JSON.parse(products);
    dataInventory = [];
    // Almacenar data para calcular clasificacion
    for (let i in products) {
      dataInventory.push({
        cantMonths: cantMonths,
        idProduct: products[i]['id_product'],
      });
    }

    $.ajax({
      type: 'POST',
      url: '/api/calcClassification',
      data: { products: dataInventory },
      success: function (response) {
        message(response);
      },
    });
  });

  /* Mensaje de exito */
  message = async (data) => {
    if (data.success == true) {
      $('.cardAddMonths').hide(800);
      $('#formAddMonths').trigger('reset');

      await loadInventory();
      $('#category').change();

      toastr.success(data.message);
      sessionStorage.removeItem('dataProductsInProcess');
      sessionStorage.removeItem('dataFinishProducts');
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
