$(document).ready(function () {
  $(document).on('click', '.seeDetail', function (e) {
    sessionStorage.removeItem('idProduct');
    let id_product = this.id;
    sessionStorage.setItem('idProduct', id_product);
  });

  loadDataPrices = async () => {
    // price_usd == '1' &&
    if (flag_currency_usd == '1' || flag_currency_eur == '1') {
      // $('.coverageUSDInput').hide();
      // $('.cardCOP').hide();
      // $('.cardUSD').hide();
      // $('.cardEUR').hide();
      $('.cardCurrencyCOP').hide();
      // $('.cardCurrencyUSD').hide();
      // $('.cardPricesEUR').hide();
      
      let typeCurrency = sessionStorage.getItem('typeCurrency');
      
      switch (typeCurrency) {
        case '1': // Pesos COP
          $('.selectCurrency').val('1');
          $('.cardCOP').show(1000);
          $('.cardCurrencyCOP').show();
          
          break;
        case '2': // Dólares  
          $('.selectCurrency').val('2');
          $('.cardUSD').show(800);
          $('.cardCurrencyUSD').show();
          $('.coverageUSDInput').show(800);
          
          break;
          case '3': // Euros
          $('.selectCurrency').val('3');
          $('.cardEUR').show(800);
          $('.cardCurrencyEUR').show();
          $('.coverageEURInput').show(800);

          break;
        
        default:
          $('.selectCurrency').val('1');
          $('.cardCOP').show(800);
          $('.cardCurrencyCOP').show();
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
    $('.cardCurrencyCOP, .cardCurrencyUSD, .cardCurrencyEUR').fadeOut(400);
    $('.cardCOP, .cardUSD, .cardEUR').slideUp(800);

    sessionStorage.setItem('typeCurrency', currency);

    setTimeout(() => {
      switch (currency) {
        case '1': // Pesos COP
          $('.cardCOP').each(function (index) {
            $(this).delay(400 * index).slideDown(800);
          });
          $('.cardCurrencyCOP').fadeIn(800);
          if (viewPrices == 2) {
            $('.cardUSD').slideUp(800);
          }
          break;
        case '2': // Dólares  
          op = 2;
          $('.coverageUSDInput').each(function (index) {
            $(this).delay(2000 * index).fadeIn(2000);
          });
          if (viewPrices == 2) {
            $('.cardUSD').slideDown(800);
          }
          $('.cardCurrencyUSD').fadeIn(800);
          break;
        case '3': // Euros
          op = 3;
          $('.coverageEURInput').each(function (index) {
            $(this).delay(1000 * index).fadeIn(1000);
          });
          $('.cardCurrencyEUR').fadeIn(800);
          if (viewPrices == 2) {
            $('.cardEUR').slideDown(800);
          }
          break;
      }
      if (viewPrices == 1) {
        op1 = 1;
      
        flag_composite_product == '1' ? data = parents : data = allPrices;

        loadTblPrices(data, op);
      } else {
        let id_product = sessionStorage.getItem('idProduct');

        loadIndicatorsProducts(id_product);
      };
    }, 400); // Delay to allow the fadeOut and slideUp to complete before showing new content
    
    
  });
  // $(document).on('change', '.selectCurrency', function () {
  //   let currency = this.value;
  //   let op = 1; 

  //   $('.selectCurrency').val(currency);
  //   // $('.coverageUSDInput').hide(800);
  //   // $('.coverageEURInput').hide(800);
  //   $('.cardCurrencyCOP').hide();
  //   $('.cardCurrencyUSD').hide();
  //   $('.cardCurrencyEUR').hide();
  //   $('.cardCOP').hide(800);
  //   $('.cardUSD').hide(800);
  //   $('.cardEUR').hide(800);
 
  //   sessionStorage.setItem('typeCurrency', currency);

  //   switch (currency) {
  //     case '1': // Pesos COP
  //       $('.cardCOP').each(function (index) {
  //         $(this).delay(800 * index).show(800);
  //       });
  //       $('.cardCurrencyCOP').show();
      
  //       if (viewPrices == 2) {
  //         $('.cardUSD').hide(800);
  //       }

  //       break;
  //     case '2': // Dólares  
  //       op = 2;
  //       $('.coverageUSDInput').each(function (index) {
  //         $(this).delay(2000 * index).show(2000);
  //       }); 
        
  //       if (viewPrices == 2) {
  //         $('.cardUSD').show(800);
  //       }
        
  //       $('.cardCurrencyUSD').show();
  //       break;
  //     case '3': // Euros
  //       op = 3;
  //       $('.coverageEURInput').each(function (index) {
  //         $(this).delay(1000 * index).show(1000);
  //       });
        
  //       $('.cardCurrencyEUR').show();
        
  //       if (viewPrices == 2) {
  //         $('.cardEUR').show(800);
  //       }
         
  //       break;
  //   };
    
  //   if (viewPrices == 1) {

  //     op1 = 1;
      
  //     flag_composite_product == '1' ? data = parents : data = allPrices;

  //     loadTblPrices(data, op);
  //   } else {
  //     let id_product = sessionStorage.getItem('idProduct');

  //     loadIndicatorsProducts(id_product);
  //   };
  // }); 
});
