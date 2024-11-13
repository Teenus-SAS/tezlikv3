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
 
  const loadDataProduct = async (id, op) => {
    let costFixed = 0, variableCost1 = 0, max = 1, typeCurrency = '1';
    
    $('#sugered, #actual, #real').show();
    $('.general').val('').html('');
    
    const data = economyScale.find(item => item.id_product == id);
    let { turnover, units_sold, turnover_anual, units_sold_anual,
      price, sale_price, costFixed: dataCostFixed, commission } = data;
    
    variableCost = data.variableCost;
    profitability = data.profitability;

    sugered_price = Math.ceil(price);
    actual_price = Math.ceil(sale_price);
    real_price = typeExpense === '1' ? turnover / units_sold : turnover_anual / units_sold_anual;
    
    if (op === 1 && real_price) {
      $('#labelDescription').html('Descripción (Precio Real)');
      ['real', 'actual', 'sugered'].forEach(id =>
        document.getElementById(id).className =
        id === 'real' ? 'btn btn-sm btn-primary typePrice cardBottons' :
          'btn btn-sm btn-outline-primary typePrice cardBottons'
      );
    }

    $('.cardBottons').hide();
    $('#spinnerLoading').empty().append(`
      <div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>
    `);

    const activeTypePrice = document.querySelector('.btn-primary.typePrice').id;
    let selected_price = activeTypePrice === 'sugered' ? sugered_price :
      activeTypePrice === 'actual' ? actual_price : real_price;

    if ((flag_currency_usd === '1' || flag_currency_eur === '1') && sessionStorage.getItem('typeCurrency')) {
      typeCurrency = sessionStorage.getItem('typeCurrency');
    }

    if (typeCurrency !== '1') {
      const coverage = typeCurrency === '2' ? coverage_usd : coverage_eur;
      selected_price /= parseFloat(coverage);
      costFixed = dataCostFixed / parseFloat(coverage);
      variableCost1 = variableCost / parseFloat(coverage);
      max = 2;
    } else {
      $('.selectTypeExpense').show();
      costFixed = typeExpense === '1' ? dataCostFixed : data.costFixedAnual;
      variableCost1 = variableCost;
    }

    $('#unity-0').val(1);
    unitys = [1];

    $('.price').val(selected_price.toFixed(max));
    $('#price-0').val(selected_price.toLocaleString('es-CO', { maximumFractionDigits: max }));
    prices = Array(6).fill({ original_price: selected_price, partial_price: selected_price });

    fixedCost = costFixed;
    variableCost = variableCost1;

    $('.fixedCosts').html(`$ ${fixedCost.toLocaleString('es-CO', { maximumFractionDigits: max })}`);
    $('.totalCostsAndExpenses').html(`$ ${(fixedCost + variableCost).toLocaleString('es-CO')}`);

    commission, cant = commission, 1;
    profitability = (selected_price * profitability) / selected_price;

    generalCalc(0, 0);
  };
  
  $(document).on('change', '#selectCurrency', function () {
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
