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

    // obtener datos del servidor
    const response = await fetch('/api/prices');
    const data = await response.json();

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


$(document).ready(function () {
  loadDataPrices();
});
