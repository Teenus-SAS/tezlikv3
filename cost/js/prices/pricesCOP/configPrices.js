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
      
        // flag_composite_product == '1' ? data = parents : data = allPrices;

        loadTblPrices(allPrices, op);
      } else {
        let id_product = sessionStorage.getItem('idProduct');

        loadIndicatorsProducts(id_product);
      };
    }, 400); // Delay to allow the fadeOut and slideUp to complete before showing new content
    
    
  });
   
  // Exportar Lista de precios
  $('#btnExportPrices').click(function (e) { 
    e.preventDefault(); 
    let op;

    let typeCurrency = sessionStorage.getItem("typeCurrency");
      typeCurrency == "2" && flag_currency_usd == "1" ?
        (op = 2)
        :
        typeCurrency == "3" && flag_currency_eur == "1" ?
          (op = 3) :
        (op = 1);
    
    let wb = XLSX.utils.book_new();
    
    // Obtener los datos de todas las filas del DataTable, excluyendo la última columna
    const allData = tblPrices.rows().data().toArray().map(row => {
      let titlePrice = op == 1 ? "precio_sugerido" : op == 3 ? "precio_sugerido(EUR)" : "precio_sugerido(USD)";
      let titlePriceList = op == 1 ? "precio_lista" : op == 3 ? "precio_lista(EUR)" : "precio_lista(USD)";

      let arr = getDataCost(row);
      let recomendedPrice = arr.recomendedPrice;  

      return {
        referencia: row.reference,
        producto: row.product,
        [titlePrice]: (op == 1 ? row.price : (op == 3 ? row.price_eur : row.price_usd)),
        margen_sugerido: `${row.profitability.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }
        )} %`,
        precio_real: recomendedPrice,
        margen_real: `${arr.actualProfitability2.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }
        )} %`,
        [titlePriceList]: (op == 1 ? row.sale_price : (op == 3 ? row.sale_price_eur : row.sale_price_usd)),
        margen_lista: `${arr.actualProfitability3.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }
        )} %`,
      };
    });

    // Convertir los datos a una hoja de Excel usando SheetJS
    let ws = XLSX.utils.json_to_sheet(allData);
    XLSX.utils.book_append_sheet(wb, ws, 'Lista Precios'); 

    // Descargar el archivo Excel
    XLSX.writeFile(wb, "precios.xlsx");
  });
});
