$(document).ready(function () {
  type_custom_price = type_custom_price.split(',');

  loadPriceList = async (op) => {
    let data = await searchData('/api/priceList');
    let $select = $(`.pricesList`);
    $select.empty();
    
    if (op == 2) {
      $select.append('Tipo de Precios');
      let dataPriceList = JSON.stringify(data);
      sessionStorage.setItem('dataPriceList', dataPriceList);
      
      $select.append(`<div class="checkbox checkbox-success">
                        <input class="typePriceList" id="-1" type="checkbox">
                        <label for="-1">TODOS</label>
                      </div>`);
      
      $.each(data, function (i, value) {
        $select.append(
          `<div class="checkbox checkbox-success">
            <input class="typePriceList" id="${value.id_price_list}" type="checkbox">
            <label for="${value.id_price_list}">${value.price_name}</label>
          </div>`
        );
      });
    }
    else {
      $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
      
      let dataPriceList = JSON.stringify(data);
      sessionStorage.setItem('dataPriceList', dataPriceList);
      let arr = data;
      let op = false;
      
      for (let i = 0; i < data.length; i++) {
        for (let j = 0; j < type_custom_price.length; j++) { 
          if (type_custom_price[j] == data[i].id_price_list) {
            if(op == false){
              sessionStorage.removeItem('dataPriceList');
              arr = [];
              op = true;
            }
            arr.push(data[i]);
          }
        }
      }
      sessionStorage.setItem('dataPriceList', JSON.stringify(arr));

      $.each(arr, function (i, value) {
        $select.append(
          `<option value = ${value.id_price_list}> ${value.price_name} </option>`
        );
      });
      
    }
  };

  loadPriceListByProduct = async (id_product) => {
    $('.selectPricelist').show();
    let data = await searchData(`/api/priceListByProduct/${id_product}`);

    let $select = $(`#pricesList`);
    $select.empty();
    let arr = [];
    
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);

    for (let i = 0; i < data.length; i++) {
      sessionStorage.removeItem('dataPriceList');
      if (type_custom_price[0] == '-1') {
        arr = data;
        sessionStorage.setItem('dataPriceList', JSON.stringify(arr));
        break;
      }
      for (let j = 0; j < type_custom_price.length; j++) {
        if (type_custom_price[j] == data[i].id_price_list) {

          // arr = [];
          // arr[i] = data[i];
          arr.push(data[i]);
          // break;
        }
      }
    
    }
    sessionStorage.setItem('dataPriceList', JSON.stringify(arr));

    $.each(arr, function (i, value) {
      $select.append(
        `<option value = ${value.id_price_list}> ${value.price_name} </option>`
      );
    });

    if (arr.length == 0) {
      $('.selectPricelist').hide();
      return 1;
    }

    if (arr.length == 1) {
      $(`#pricesList option[value=${arr[0].id_price_list}]`).prop('selected', true);
      $('#price').val(parseFloat(arr[0].price));
    }
  };

});
