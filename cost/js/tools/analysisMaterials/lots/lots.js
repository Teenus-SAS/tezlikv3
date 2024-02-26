$(document).ready(function () {
  products = [];
  totalUnits = 0;

  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    // $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $(`#refProduct option[value=${id}]`).prop('selected', true);
  });

  /* Productos Cargados */
  $('#btnProductsLoaded').click(function (e) {
    e.preventDefault();

    sessionStorage.removeItem('update');
    $('#formAddLot').trigger('reset');
    $('.cardRawMaterialsAnalysis').hide(800);
    $('.cardTableRawMaterials').hide(800);
    $('.cardAddLot').show(800);
    $('.cardTableProducts').show(800);
    $('#btnAddLot').html('Adicionar');

    setTimeout(setCSSTbl(), 1000);
  });

  /* Analizar Materias Primas */
  $('#btnRawMaterialsAnalysis').click(function (e) {
    e.preventDefault();

    $('.cardAddLot').hide(800);
    $('.cardRawMaterialsAnalysis').show(800);
    $('.cardTableRawMaterials').hide(800);
    $('.cardTableProducts').hide(800);

    if (totalUnits > 0)
      $('#totalUnits').val(totalUnits.toLocaleString('es-CO'));

    setTimeout(setCSSTbl, 1000);
  });

  /* Materia Prima Consolidada*/
  $('#btnConsolidatedMP').click(function (e) {
    e.preventDefault();

    $('.cardRawMaterialsAnalysis').hide(800);
    $('.cardTableRawMaterials').show(800);
    $('.cardAddLot').hide(800);
    $('.cardTableProducts').hide(800);

    setTimeout(setCSSTbl(), 1000);
  });

  function setCSSTbl() {
    $('#tblAnalysisMaterials').css('width', '100%');
    $('#tblProducts').css('width', '100%');
    $('#tblMaterials').css('width', '100%');
  }

  $('#btnAddLot').click(function (e) {
    e.preventDefault();

    let idProduct = parseInt($('#refProduct').val());
    let units = parseFloat($('#unitsmanufacturated').val());

    // units = parseFloat(strReplaceNumber(units));

    data = idProduct * units;

    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let row = sessionStorage.getItem('update');

    if (!row || row == '') {
      let product = {};
      
      product.idProduct = idProduct;
      product.reference = $('#refProduct :selected').text().trim();
      product.name = $('#selectNameProduct :selected').text().trim();
      product.units = units;

      for (let i = 0; i < products.length; i++) {
        if (idProduct == products[i].idProduct) {
          products.splice(i--, 1);
          break;
        }
      }
      products.push(product);
    } else {
      products[row].idProduct = idProduct;
      products[row].reference = $('#refProduct :selected').text().trim();
      products[row].name = $('#selectNameProduct :selected').text().trim();
      products[row].units = units;      
    }

    loadTblProducts(products);

    // for (let i = 0; i < dataMaterials.length; i++) {
    //   if (idProduct == dataMaterials[i].id_product) {
    //     dataMaterials.splice(i--, 1);
    //     break;
    //   }
    // }

    // dataMaterials.push({ id_product: idProduct, unit: units });

    fetchData(products);

    !row || row == '' ? msg = 'Producto adicionado correctamente' : msg = 'Producto modificado correctamente';
    toastr.success(msg);
    $('#formAddLot').trigger('reset');
    $('.cardAddLot').hide(800);
  });

  $(document).on('click','.updateProduct', function () {
    let row = this.id;

    let data = products[row];
 
    $(`#refProduct option[value=${data.idProduct}]`).prop('selected', true);
    $(`#selectNameProduct option[value=${data.idProduct}]`).prop('selected', true);
    $('#unitsmanufacturated').val(data.units);
    sessionStorage.setItem('update', row);
    $('#btnAddLot').html('Actualizar');

    $('.cardAddLot').show(800);
  });
});
