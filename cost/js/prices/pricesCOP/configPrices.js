$(document).ready(function () {
  $(document).on('click', '.seeDetail', function (e) {
    sessionStorage.removeItem('idProduct');
    let id_product = this.id;
    sessionStorage.setItem('idProduct', id_product);
  });

  loadDataPrices = async () => {
    if (price_usd == '1' && plan_cost_price_usd == '1') {
      $('.coverageInput').hide();
      $('.cardCOP').hide();

      let typePrice = sessionStorage.getItem('typePrice');

      let element = document.getElementsByClassName('btnPricesUSD')[0];

      if (typePrice == '1' || !typePrice) {
        element.id = 'usd';
        element.innerText = 'Precios USD';

        $('.cardCOP').show(800);

        $('.cardPricesCOP').show();
        $('.cardPricesUSD').hide(); 

        if (viewPrices == 2) {
          document.getElementById('btnPdf').className = 'col-xs-2 mr-2 btnPrintPDF';
          $('.cardUSD').hide(800);
        }
      } else {
        element.id = 'cop';
        element.innerText = 'Precios COP';

        $('.cardPricesUSD').show();
        $('.cardPricesCOP').hide(); 

        $('.coverageInput').show(800);

        if (viewPrices == 2) {
          document.getElementById('btnPdf').className = 'col-xs-2 mr-2 btnPrintPDF mt-4';
          $('.cardUSD').show(800);
        }
      }
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

  $(document).on('click','.btnPricesUSD', function () {
    let id = this.id;
    let op = 1;

    $('.coverageInput').hide(800);
    $('.cardCOP').hide(800);

    id == 'cop' ? op = 1 : op = 2;
    sessionStorage.setItem('typePrice', op);

    let element = document.getElementsByClassName('btnPricesUSD')[0];

    if (id == 'usd') {
      element.id = 'cop';
      element.innerText = 'Precios COP';
 
      $('.coverageInput').each(function (index) {
        $(this).delay(800 * index).show(800);
      });
      
      if (viewPrices == 2) {
        document.getElementById('btnPdf').className = 'col-xs-2 mr-2 btnPrintPDF mt-4';
        $('.cardUSD').show(800);
      }
    } else {
      element.id = 'usd';
      element.innerText = 'Precios USD';
      
      $('.cardCOP').each(function (index) {
        $(this).delay(800 * index).show(800);
      });
      
      if (viewPrices == 2) {
        document.getElementById('btnPdf').className = 'col-xs-2 mr-2 btnPrintPDF';
        $('.cardUSD').hide(800);
      }
    }
    
    if (viewPrices == 1) {
      $('.cardPricesCOP').toggle();
      $('.cardPricesUSD').toggle();

      op1 = 1;
      
      flag_composite_product == '1' ? data = parents : data = allPrices;

      loadTblPrices(data, op);
    } else { 
      let id_product = sessionStorage.getItem('idProduct');

      loadIndicatorsProducts(id_product);
    }
  });
});
