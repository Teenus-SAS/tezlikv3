$(document).ready(function () {
  let economyScale = [];
  cant = 1;

  const loadAllData = async () => {
    try {
      const data = await searchData('/api/calcEconomyScale');

      if (data.reload) {
        location.reload();
      }

      if (data.info) {
        toastr.info(data.message);
        return false;
      }

      economyScale = data;
 
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadAllData();

  function syncSelects(changedSelect, targetSelect) {
    let id = changedSelect.value;
    $(`${targetSelect} option`).removeAttr('selected');
    $(`${targetSelect} option[value=${id}]`).prop('selected', true);
    loadDataProduct(id, 1);
  }

  $('#refProduct').change(function (e) {
    e.preventDefault();
    syncSelects(this, '#selectNameProduct');
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    syncSelects(this, '#refProduct');
  });

  loadDataProduct = async (id, op) => {
    let costFixed = 0;
    let variableCost1 = 0;

    $('#sugered').show();
    $('#actual').show();
    $('#real').show();

    $('.general').val('');
    $('.general').html('');
    
    let data = economyScale.find(item => item.id_product == id); 

    sugered_price = Math.ceil(data.price);
    actual_price = Math.ceil(data.sale_price);
    if (typeExpense == '1') {
      real_price = parseFloat(data.turnover) / parseFloat(data.units_sold);
    } else { 
      real_price = parseFloat(data.turnover_anual) / parseFloat(data.units_sold_anual);
    }
    
    if (op == 1) {
      if (real_price) {
        $('#labelDescription').html(`Descripción (Precio Real)`);

        document.getElementById("real").className =
          "btn btn-sm btn-primary typePrice cardBottons";
        document.getElementById("actual").className =
          "btn btn-sm btn-outline-primary typePrice cardBottons";
        document.getElementById("sugered").className =
          "btn btn-sm btn-outline-primary typePrice cardBottons";
      }
    }

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
    
    let typePrice = document.getElementsByClassName('btn btn-sm btn-primary typePrice')[0]; 

    if (typePrice.id === 'sugered') {
      price = sugered_price; 
    } else if(typePrice.id === 'actual'){
      price = actual_price; 
    } else {
      price = real_price; 
    }  

    let typeCurrency = '1';
    
    if ((flag_currency_usd == '1' || flag_currency_eur == '1') && sessionStorage.getItem('typeCurrency'))
      typeCurrency = sessionStorage.getItem('typeCurrency');

    $('.selectTypeExpense').hide();
 
    switch (typeCurrency) {
      case '2': // Dolares
        price = price / parseFloat(coverage_usd);
        costFixed = data.costFixed / parseFloat(coverage_usd);
        variableCost1 = data.variableCost / parseFloat(coverage_usd);
        max = 2;
        break;
      case '3': // Euros
        price = price / parseFloat(coverage_eur);
        costFixed = data.costFixed / parseFloat(coverage_eur);
        variableCost1 = data.variableCost / parseFloat(coverage_eur);
        max = 2;
        break;
      default:// Pesos COP
        $('.selectTypeExpense').show();
        costFixed = typeExpense == '1' ? data.costFixed : data.costFixedAnual;
        variableCost1 = data.variableCost;
        max = 1;
        break;
    }

    $('#unity-0').val(1);
    unitys = [1];

    commission = data.commission;

    // Regla de tres rentabilidad
    profitability = (price * data.profitability) / price;
 
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
    cant = 1;

    generalCalc(0);
  };

  // Seleccionar moneda
  $(document).on('change','#selectCurrency', function () {
    let currency = this.value;
 
    sessionStorage.setItem('typeCurrency', currency);
    $('.cardUSD').hide();
    $('.cardEUR').hide();

    switch (currency) {
      case '1': // Pesos
        break;
      case '2': // Dolares
      $('.cardUSD').show(800);
      break;
      case '3': // Euros
      $('.cardEUR').show(800);
      break;
    
      default:
        break;
    }
 
    let id_product = $('#refProduct').val();

    if (id_product)
      loadDataProduct(id_product, 2);
  }); 

  // Seleccionar tipo de gasto
  $('#selectTypeExpense').change(function (e) {
    e.preventDefault();

    typeExpense = this.value;

    let id_product = $('#refProduct').val();

    if (id_product) { 
      loadDataProduct(id_product, 2);
    }
  });
});
