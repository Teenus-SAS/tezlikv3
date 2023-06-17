$(document).ready(function () {
  loadPriceList = async () => {
    let data = await searchData('/api/priceList');

    let dataPriceList = JSON.stringify(data);
    sessionStorage.setItem('dataPriceList', dataPriceList);

    let $select = $(`#pricesList`);
    $select.empty();

    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_price_list}> ${value.price_name} </option>`
      );
    });
  };

  loadPriceListByProduct = async (id_product) => {
    let data = await searchData(`/api/priceListByProduct/${id_product}`);

    let dataPriceList = JSON.stringify(data);
    sessionStorage.setItem('dataPriceList', dataPriceList);

    /*let $select = $(`#pricesList`);
    $select.empty();

    $select.append(`<option value='0' selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value ='${value.id_price_list}'> ${value.price_name} </option>`
      );
    });*/
  };

  loadPriceList();
});
