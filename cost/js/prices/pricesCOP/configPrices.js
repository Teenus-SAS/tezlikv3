$(document).ready(function () {
  $(document).on('click', '.seeDetail', function (e) {
    sessionStorage.removeItem('idProduct');
    let id_product = this.id;
    sessionStorage.setItem('idProduct', id_product);
  });

  loadDataPrices = async () => {
    let typePrice = sessionStorage.getItem('typePrice');

    let element = document.getElementsByClassName('btnPricesUSD')[0];

    if (typePrice == '1' || !typePrice) {
      element.id = 'usd';
      element.innerText = 'Precios USD';

      $('.cardPricesCOP').show();
      $('.cardPricesUSD').hide();
    }
    else {
      element.id = 'cop';
      element.innerText = 'Precios COP';

      $('.cardPricesUSD').show();
      $('.cardPricesCOP').hide();
    }
    
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

  $('.btnPricesUSD').click(async function (e) {
    e.preventDefault();
    let id = this.id;
    let op = 1;

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
    
    if (viewPrices == 1) {
      $('.cardPricesCOP').toggle();
      $('.cardPricesUSD').toggle();
      op1 = 1;
      
      flag_composite_product == '1' ? data = parents : data = prices;

      loadTblPrices(data, op);
    } else {

      let id_product = sessionStorage.getItem('idProduct');

      loadIndicatorsProducts(id_product);
    }
  });
});
