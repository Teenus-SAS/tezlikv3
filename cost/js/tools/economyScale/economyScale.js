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
    $('.cardBottons').hide();

    let form = document.getElementById('btnsEconomy');

    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    $('.general').val('');
    $('.general').html('');

    data = await searchData(`/api/calcEconomyScale/${id}`);
    let typePrice = document.getElementsByClassName('btn btn-sm btn-primary typePrice')[0];
    
    typePrice.id === 'sugered' ? price = Math.ceil(data.price) : price = Math.ceil(data.sale_price);
    $('#labelDescription').html(` Descripción (${typePrice.id == 'actual' ? 'Precio Actual' : 'Precio Sugerido'}) `);
    
    if (price == 0 || !price) {
      if (typePrice.id == 'sugered') {
        $('#labelDescription').html(`Descripción (Precio Sugerido)`);

        document.getElementById("actual").className =
          "btn btn-sm btn-primary typePrice cardBottons";
        document.getElementById("sugered").className =
          "btn btn-sm btn-outline-primary typePrice cardBottons";
        price = data.sale_price;
      } else {
        $('#labelDescription').html(`Descripción (Precio Actual)`);

        document.getElementById("sugered").className =
          "btn btn-sm btn-primary typePrice cardBottons";
        document.getElementById("actual").className =
          "btn btn-sm btn-outline-primary typePrice cardBottons";
        price = data.price
      }
      
      if (price == 0 || !price) {
        typePrice.id == 'sugered' ? price = 'sugerido' : price = 'actual';
        toastr.error(`Ingrese el precio de venta ${price} para el producto`);
        return false;
      }
    }


    $('#unity-0').val(1);
    unitys = [1];

    commission = data.commission;

    // Regla de tres rentabilidad
    profitability = (price * data.profitability) / price;

    /* Precios */
    $('.price').val(price);
    $('#price-0').val(price.toLocaleString('es-CO', { maximumFractionDigits: 0 }));

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
