$(document).ready(function () {
  let products = [];
  $('.cardAddLot').hide();

  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
  });

  $('#btnRawMaterialsAnalysis').click(function (e) {
    e.preventDefault();

    $('.cardAddLot').hide(800);
    $('.cardRawMaterialsAnalysis').show(800);
    $('.cardTableProducts').hide(800);
  });

  $('#btnProductsLoaded').click(function (e) {
    e.preventDefault();

    $('#formAddLot').trigger('reset');
    $('.cardRawMaterialsAnalysis').hide(800);
    $('.cardAddLot').show(800);
    $('.cardTableProducts').show(800);
  });

  $('#btnAddLot').click(function (e) {
    e.preventDefault();

    let idProduct = parseInt($('#refProduct').val());
    let units = $('#unitsmanufacturated').val();

    units = parseFloat(strReplaceNumber(units));

    data = idProduct * units;

    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let product = {};

    product.idProduct = idProduct;
    product.reference = $('#refProduct :selected').text().trim();
    product.name = $('#selectNameProduct :selected').text().trim();
    product.units = units;

    products.push(product);

    toastr.success('Producto adicionado correctamente');
    $('#formAddLot').trigger('reset');
    $('.cardAddLot').hide(800);

    loadTblProducts(products);
  });
});
