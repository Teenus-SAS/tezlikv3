/* $(document).ready(function () {
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
      let suggestedPrice = (op == 1 ? parseFloat(row.price) : (op == 3 ? parseFloat(row.price_eur) : parseFloat(row.price_usd)));  
      let recomendedPrice = parseFloat(arr.recomendedPrice);  
      let priceList = (op == 1 ? parseFloat(row.sale_price) : (op == 3 ? parseFloat(row.sale_price_eur) : parseFloat(row.sale_price_usd)));  

      return {
        referencia: row.reference,
        producto: row.product,
        [titlePrice]: suggestedPrice.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
          }
        ),
        margen_sugerido: `${row.profitability.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }
        )} %`,
        precio_real: recomendedPrice.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
          }
        ),
        margen_real: `${arr.actualProfitability2.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }
        )} %`,
        [titlePriceList]: priceList.toLocaleString(
          "es-CO",
          {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
          }
        ),
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
 */

$(document).ready(function () {
  const CURRENCY_MAPPING = {
    '1': { classToShow: '.cardCOP', coverageClass: '', fadeDuration: 800 },
    '2': { classToShow: '.cardUSD', coverageClass: '.coverageUSDInput', fadeDuration: 800 },
    '3': { classToShow: '.cardEUR', coverageClass: '.coverageEURInput', fadeDuration: 1000 },
  };

  const hideCurrencyCards = () => {
    $('.cardCurrencyCOP, .cardCurrencyUSD, .cardCurrencyEUR').hide();
    $('.cardCOP, .cardUSD, .cardEUR').hide();
  };

  const updateCurrencyView = (currency) => {
    hideCurrencyCards();

    const { classToShow, coverageClass, fadeDuration } = CURRENCY_MAPPING[currency] || {};
    if (classToShow) {
      $(classToShow).slideDown(fadeDuration);
      $(`.cardCurrency${currency === '1' ? 'COP' : currency === '2' ? 'USD' : 'EUR'}`).fadeIn(fadeDuration);
    }
    if (coverageClass) {
      $(coverageClass).fadeIn(fadeDuration);
    }
  };

  $(document).on('click', '.seeDetail', function () {
    sessionStorage.setItem('idProduct', this.id);
  });

  const loadDataPrices = async () => {
    try {
      if (flag_currency_usd === '1' || flag_currency_eur === '1') {
        const typeCurrency = sessionStorage.getItem('typeCurrency') || '1';
        updateCurrencyView(typeCurrency);
        $('.selectCurrency').val(typeCurrency);
      }

      const data = await searchData('/api/prices');
      const $select = $('#product');
      $select.empty().append(`<option value='0' disabled selected>Seleccionar</option>`);

      const sortedData = sortFunction(data, 'product');
      sortedData.forEach((item) =>
        $select.append(`<option value='${item.id_product}'>${item.product}</option>`)
      );
    } catch (error) {
      console.error('Error loading prices:', error);
    }
  };

  loadDataPrices();

  $(document).on('change', '.selectCurrency', function () {
    const currency = this.value;
    sessionStorage.setItem('typeCurrency', currency);

    setTimeout(() => {
      updateCurrencyView(currency);

      if (viewPrices === 1) {
        loadTblPrices(allPrices, parseInt(currency));
      } else {
        const id_product = sessionStorage.getItem('idProduct');
        loadIndicatorsProducts(id_product);
      }
    }, 400);
  });

  $('#btnExportPrices').click(function (e) {
    e.preventDefault();
    const typeCurrency = sessionStorage.getItem('typeCurrency') || '1';
    const op = typeCurrency === '2' && flag_currency_usd === '1' ? 2 : typeCurrency === '3' && flag_currency_eur === '1' ? 3 : 1;

    const wb = XLSX.utils.book_new();
    const allData = tblPrices
      .rows()
      .data()
      .toArray()
      .map((row) => {
        const { recomendedPrice, actualProfitability2, actualProfitability3 } = getDataCost(row);
        const titlePrice = op === 1 ? 'precio_sugerido' : op === 2 ? 'precio_sugerido(USD)' : 'precio_sugerido(EUR)';
        const titlePriceList = op === 1 ? 'precio_lista' : op === 2 ? 'precio_lista(USD)' : 'precio_lista(EUR)';

        return {
          referencia: row.reference,
          producto: row.product,
          [titlePrice]: parseFloat(row[`price${op === 3 ? '_eur' : op === 2 ? '_usd' : ''}`]).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }),
          margen_sugerido: `${row.profitability.toFixed(2)} %`,
          precio_real: recomendedPrice.toFixed(2),
          margen_real: `${actualProfitability2.toFixed(2)} %`,
          [titlePriceList]: parseFloat(row[`sale_price${op === 3 ? '_eur' : op === 2 ? '_usd' : ''}`]).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }),
          margen_lista: `${actualProfitability3.toFixed(2)} %`,
        };
      });

    const ws = XLSX.utils.json_to_sheet(allData);
    XLSX.utils.book_append_sheet(wb, ws, 'Lista Precios');
    XLSX.writeFile(wb, 'precios.xlsx');
  });
});
