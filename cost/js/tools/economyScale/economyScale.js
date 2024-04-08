$(document).ready(function () {
  let economyScale = [];

  loadAllData = async () => {
    try {
      const data = await searchData('/api/calcEconomyScale');

      economyScale = data;
 
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadAllData();

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
    let costFixed = 0;
    let variableCost1 = 0;

    $('.cardBottons').hide();

    let form = document.getElementById('spinnerLoading');
    $('#spinnerLoading').empty();

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
    
    let data = economyScale.find(item => item.id_product == id);
    // data = await searchData(`/api/calcEconomyScale/${id}`);
    let typePrice = document.getElementsByClassName('btn btn-sm btn-primary typePrice')[0];
    // let session_flag = sessionStorage.getItem('flag_type_price');
    
    if (typePrice.id === 'sugered') {
      price = Math.ceil(data.price); 
    } else {
      price = Math.ceil(data.sale_price); 
    }
    // $('#labelDescription').html(` Descripción (${typePrice.id == 'actual' ? 'Precio Actual' : 'Precio Sugerido'}) `);
    
    // if (price == 0 || !price) {
    // if (typePrice.id == 'sugered') { 
    // if (session_flag == '1' || !session_flag) {
    //   $('#labelDescription').html(`Descripción (Precio Sugerido)`);

    //   document.getElementById("actual").className =
    //     "btn btn-sm btn-primary typePrice cardBottons";
    //   document.getElementById("sugered").className =
    //     "btn btn-sm btn-outline-primary typePrice cardBottons";
        
    //   price = Math.ceil(data.sale_price);
    // } else {
    //   $('#labelDescription').html(`Descripción (Precio Actual)`);

    //   document.getElementById("sugered").className =
    //     "btn btn-sm btn-primary typePrice cardBottons";
    //   document.getElementById("actual").className =
    //     "btn btn-sm btn-outline-primary typePrice cardBottons";
        
    //   price = Math.ceil(data.price)
    // }
      
    if (price == 0 || !price) {
      typePrice.id == 'sugered' ? price = 'sugerido' : price = 'actual';
      toastr.error(`Ingrese el precio de venta ${price} para el producto`);
      return false;
    }
    // }

    typePrice = sessionStorage.getItem('typePrice');

    if (typePrice == '2') {
      price = price / parseFloat(coverage);
      costFixed = data.costFixed / parseFloat(coverage);
      variableCost1 = data.variableCost / parseFloat(coverage);
    } else {
      costFixed = data.costFixed;
      variableCost1 = data.variableCost;
    }

    $('#unity-0').val(1);
    unitys = [1];

    commission = data.commission;

    // Regla de tres rentabilidad
    profitability = (price * data.profitability) / price;

    /* Precios */
    typePrice == '2' ? max = 2 : max = 0;

    $('.price').val(price.toFixed(max));
    $('#price-0').val(price.toLocaleString('es-CO', { maximumFractionDigits: max }));

    prices = [
      price,
      price,
      price,
      price,
      price,
      price,
    ];

    /* Costos Fijos */
    fixedCost = costFixed;

    variableCost = variableCost1;

    // Costos Fijos
    $(`.fixedCosts`).html(
      `$ ${fixedCost.toLocaleString('es-CO', { maximumFractionDigits: max })}`
    );

    // Total Costos y Gastos
    $(`.totalCostsAndExpenses`).html(
      `$ ${(fixedCost + variableCost).toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })}`
    );

    generalCalc(0);
  };

  $('.btnPricesUSD').click(async function (e) {
    e.preventDefault();
    let id = this.id;

    id == 'cop' ? op = 1 : op = 2;
    sessionStorage.setItem('typePrice', op);
    let element = document.getElementsByClassName('btnPricesUSD')[0];

    if (id == 'usd') {
      element.id = 'cop';
      element.innerText = 'Precios COP';
    }
    else {
      element.id = 'usd';
      element.innerText = 'Precios USD';
    }
    let id_product = $('#refProduct').val();

    loadDataProduct(id_product);    
  });
});
