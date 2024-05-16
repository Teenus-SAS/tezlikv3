$(document).ready(function () {
  $('#refCompositeProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#compositeProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });

  $('#compositeProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#refCompositeProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });

  $.ajax({
    url: '/api/products',
    success: function (r) {
      sessionStorage.setItem('dataProducts', JSON.stringify(r));
    
      populateSelect('.refProduct', r, 'reference');
      populateSelect('.selectNameProduct', r, 'product');

      if(flag_composite_product == '1'){
        economyScaleOtions('.refESProduct', r, 'reference');
        economyScaleOtions('.selectESNameProduct', r, 'product');
      }

      let compositeProduct = r.filter(item => item.composite == 1);
      populateOptions('#refCompositeProduct', compositeProduct, 'reference');
      populateOptions('#compositeProduct', compositeProduct, 'product');
    }
  });

  function populateSelect(selector, data, property) {
    let $select = $(selector);
    $select.empty();
  
    let sortedData = sortFunction(data, property);
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
  
    $.each(sortedData, function (i, value) {
      $select.append(`<option value ='${value.id_product}' class='${value.composite}'> ${value[property]} </option>`);
    });
  };

  function populateOptions(selector, data, property) {
    let $select = $(selector);
    $select.empty();
  
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
  
    $.each(data, function (i, value) {
      $select.append(`<option value ="${value.id_product}"> ${value[property]} </option>`);
    });
  };

  function economyScaleOtions(selector, data, property) {
    let $select = $(selector);
    $select.empty();

    data = data.filter(item => item.composite == 0);
  
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
  
    $.each(data, function (i, value) {
      $select.append(`<option value ="${value.id_product}"> ${value[property]} </option>`);
    });
  };
});
