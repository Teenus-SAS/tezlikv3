$(document).ready(function () {
  $(document).on('click', '.seeDetail', function (e) {
    sessionStorage.removeItem('idProduct');
    let id_product = this.id;
    sessionStorage.setItem('idProduct', id_product);
  });

  loadDataPrices = async () => {
    let data = await searchData('/api/prices');

    let $select = $(`#product`);
    $select.empty();

    let prod = sortFunction(data, 'product');

    $select.append(
      `<option value='0' disabled selected>Seleccionar</option>`
    );
    $.each(prod, function (i, value) {
      $select.append(
        `<option value ='${value.id_product}'> ${value.product} </option>`
      );
    });
  };

  loadDataPrices();

  $('.btnPricesUSD').click(function (e) {
    e.preventDefault();
    let id = this.id;

    $('.cardPricesCOP').toggle();
    $('.cardPricesUSD').toggle();

    flag_composite_product == '1' ? data = parents : data = prices;
    id == 'cop' ? op = 1 : op = 2;
    
    loadTblPrices(data, op);
  });
});
