$(document).ready(function () {
  loadPriceList = async (op) => {
    let data = await searchData('/api/priceList');

    let $select = $(`#pricesList`);
    $select.empty();
    
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
    
    if (op == 2) {
      let dataPriceList = JSON.stringify(data);
      sessionStorage.setItem('dataPriceList', dataPriceList);
      
      $select.append(`<option value='-1'>TODO</option>`);
      $.each(data, function (i, value) {
        $select.append(
          `<option value = ${value.id_price_list}> ${value.price_name} </option>`
        );
      });
    }
    else {
      let dataPriceList = JSON.stringify(data);
      sessionStorage.setItem('dataPriceList', dataPriceList);
      let arr = data;
      
      for (let i = 0; i < data.length; i++) {
        if (parseInt(type_custom_price) == data[i].id_price_list) {
          sessionStorage.removeItem('dataPriceList');
          arr = [];
          arr[0] = data[i];
          arr[0]['price_name'] = 'PRECIOS';
          sessionStorage.setItem('dataPriceList', JSON.stringify(arr));
          break;
        }
      }

      $.each(arr, function (i, value) {
        $select.append(
          `<option value = ${value.id_price_list}> ${value.price_name} </option>`
        );
      });
      
    }
  };

  loadPriceListByProduct = async (id_product) => {
    let data = await searchData(`/api/priceListByProduct/${id_product}`);

    let $select = $(`#pricesList`);
    $select.empty();
    
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
    for (let i = 0; i < data.length; i++) {
      if (parseInt(type_custom_price) == data[i].id_price_list) {
        sessionStorage.removeItem('dataPriceList');
        arr = [];
        arr[0] = data[i];
        arr[0]['price_name'] = 'PRECIOS';
        sessionStorage.setItem('dataPriceList', JSON.stringify(arr));
        break;
      }
    }

    $.each(arr, function (i, value) {
      $select.append(
        `<option value = ${value.id_price_list}> ${value.price_name} </option>`
      );
    });

    if (arr.length == 0) {
      $(`#pricesList option[value=${arr[0].id_price_list}]`).prop('selected', true);
      $('#price').val(arr[0].price);
    }

    // let dataPriceList = JSON.stringify(data);
    // sessionStorage.setItem('dataPriceList', dataPriceList);
  };

  // loadPriceList();
});
