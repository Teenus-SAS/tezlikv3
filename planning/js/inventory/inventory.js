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
    debugger;
    products = sessionStorage.getItem('products');
    // aLmacenar data para calcular clasificacion
  });
});
