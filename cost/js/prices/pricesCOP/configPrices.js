$(document).ready(function () {
  $(document).on('click', '.seeDetail', function (e) {
    sessionStorage.removeItem('idProduct');
    let id_product = this.id;
    sessionStorage.setItem('idProduct', id_product);
  });

  loadDataPrices = async () => {
    // price_usd == '1' &&
    if (flag_currency_usd == '1' || flag_currency_eur == '1') {
      $('.coverageInput').hide();
      $('.cardCOP').hide();
      $('.cardUSD').hide();
      $('.cardEUR').hide();
      $('.cardPricesCOP').hide();
      $('.cardCurrencyUSD').hide();
      $('.cardPricesEUR').hide();
      
      let typeCurrency = sessionStorage.getItem('typeCurrency');
      
      switch (typeCurrency) {
        case '1': // Pesos COP
          $('.selectCurrency').val('1');
          $('.cardCOP').show(800);
          $('.cardPricesCOP').show();
          
          break;
        case '2': // Dólares  
          $('.selectCurrency').val('2');
          $('.cardUSD').show(800);
          $('.cardCurrencyUSD').show();
          $('.coverageInput').show(800);
          
          break;
        case '3': // Euros
          $('.selectCurrency').val('3');
          $('.cardCurrencyEUR').show();

          break;
        
        default:
          $('.selectCurrency').val('1');
          $('.cardCOP').show(800);
          $('.cardPricesCOP').show();
          break;
      };
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

  $(document).on('change', '.selectCurrency', function () {
    let currency = this.value;
    let op = 1; 

    $('.selectCurrency').val(currency);
    $('.coverageInput').hide(800);
    $('.cardCOP').hide(800);
 
    sessionStorage.setItem('typeCurrency', currency);

    switch (currency) {
      case '1': // Pesos COP
        $('.cardCOP').each(function (index) {
          $(this).delay(800 * index).show(800);
        });
      
        if (viewPrices == 2) {
          $('.cardUSD').hide(800);
        }

        break;
      case '2': // Dólares  
        op = 2;
        $('.coverageInput').each(function (index) {
          $(this).delay(800 * index).show(800);
        });
      
        if (viewPrices == 2) {
          $('.cardUSD').show(800);
        }

        break;
      case '3': // Euros
        op = 3;
        break;
    };
    
    if (viewPrices == 1) {
      $('.cardPricesCOP').toggle();
      $('.cardCurrencyUSD').toggle();

      op1 = 1;
      
      flag_composite_product == '1' ? data = parents : data = allPrices;

      loadTblPrices(data, op);
    } else {
      let id_product = sessionStorage.getItem('idProduct');

      loadIndicatorsProducts(id_product);
    };
  });
  // $(document).on('click','.btnPricesUSD', function () {
  //   let id = this.id;
  //   let op = 1;

  //   $('.coverageInput').hide(800);
  //   $('.cardCOP').hide(800);

  //   id == 'cop' ? op = 1 : op = 2;
  //   sessionStorage.setItem('typePrice', op);

  //   let element = document.getElementsByClassName('btnPricesUSD')[0];

  //   if (id == 'usd') {
  //     element.id = 'cop';
  //     element.innerText = 'Precios COP';
 
  //     $('.coverageInput').each(function (index) {
  //       $(this).delay(800 * index).show(800);
  //     });
      
  //     if (viewPrices == 2) {
  //       // document.getElementById('btnPdf').className = 'col-xs-2 mr-2 btnPrintPDF mt-4';
  //       $('.cardUSD').show(800);
  //     }
  //   } else {
  //     element.id = 'usd';
  //     element.innerText = 'Precios USD';
      
  //     $('.cardCOP').each(function (index) {
  //       $(this).delay(800 * index).show(800);
  //     });
      
  //     if (viewPrices == 2) {
  //       // document.getElementById('btnPdf').className = 'col-xs-2 mr-2 btnPrintPDF';
  //       $('.cardUSD').hide(800);
  //     }
  //   }
    
  //   if (viewPrices == 1) {
  //     $('.cardPricesCOP').toggle();
  //     $('.cardCurrencyUSD').toggle();

  //     op1 = 1;
      
  //     flag_composite_product == '1' ? data = parents : data = allPrices;

  //     loadTblPrices(data, op);
  //   } else { 
  //     let id_product = sessionStorage.getItem('idProduct');

  //     loadIndicatorsProducts(id_product);
  //   }
  // });
});
