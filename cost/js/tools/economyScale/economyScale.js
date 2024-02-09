$(document).ready(function () { 
  $('#refProduct').change(function (e) {
    e.preventDefault();

    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  loadDataProduct = async (id) => {
    $('.general').val('');
    $('.general').html('');

    data = await searchData(`/api/calcEconomyScale/${id}`); 
    let typePrice = document.getElementsByClassName('btn btn-sm btn-primary typePrice')[0];
    
    typePrice.id == 'sugered' ? price = data.sale_price : price = data.price;
    
    if (price == 0 || !price) {
      typePrice.id == 'sugered' ? price = 'sugerido' : price = 'actual';
      toastr.error(`Ingrese el precio de venta ${price} para el producto`);
      return false;
    }

    $('#unity-0').val(1);
    unitys = [1];

    commission = data.commission;


    // Regla de tres rentabilidad
    profitability = (price * data.profitability) / price;

    /* Precios */
    $('.price').val(
      price.toLocaleString('es-CO', { maximumFractionDigits: 0 })
    );
    prices = [
      price,
      price,
      price,
      price,
      price,
      price,
    ];

    /* Costos Fijos */
    fixedCost = data.fixedCost;

    variableCost = data.variableCost;

    // Costos Fijos
    $(`.fixedCosts`).html(
      `$ ${fixedCost.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`
    );

    // Total Costos y Gastos
    $(`.totalCostsAndExpenses`).html(
      `$ ${(fixedCost + variableCost).toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })}`
    );

    generalCalc(0);
  }; 
});
