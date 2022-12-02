$(document).ready(function () {
  let idQuote = sessionStorage.getItem('id_quote');

  fetchindata = async () => {
    data = await searchData(`/api/quote/${idQuote}`);

    loadDataQuote(data.quote);
    loadDataQuoteProducts(data.quotesProducts);
  };

  fetchindata();

  loadDataQuote = (data) => {
    $('#idQuote').html(`Cotización (${data.id_quote})`);
    /* Compañia */
    $('#companyImg').prop('src', data.img);
    $('#companyName').html(data.company_name);
    $('#companyAddress').html(data.address);
    $('#companyPhone').html(data.phone);
    $('#companyCity').html(data.city);

    /* Contacto */
    $('#contactName').html(data.contact);
    $('#contactPhone').html(data.contact_phone);
    $('#contactEmail').html(data.email);

    /* Cotizacion */
    $('#dateQuote').html(data.delivery_date);

    /* Notices */
    $('#paymentMethod').html(data.method);
    $('#observation').html(data.observation);
  };
  loadDataQuoteProducts = (data) => {
    let subtotal = 0;

    $('#tblQuotesProductsBody').empty();
    let tblQuotesProductsBody = document.getElementById(
      'tblQuotesProductsBody'
    );

    for (i = 0; i < data.length; i++) {
      tblQuotesProductsBody.insertAdjacentHTML(
        'beforeend',
        `
      <tr>
        <td>${i + 1}</td>
        <td class="text-left">
          <h3>${data[i].ref}</h3>
        </td>
        <td class="text-left"> ${data[i].nameProduct}</td>
        <td class="text-center">${data[i].quantity.toLocaleString()}</td>
        <td class="text-center">$ ${data[i].price.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}</td>
        <td class="text-center">${data[i].discount} %</td>
        <td class="text-center">$ ${data[i].totalPrice.toLocaleString(
          undefined,
          {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          }
        )}</td>
      </tr>
    `
      );

      subtotalPrice =
        data[i].quantity * data[i].price * (1 - data[i].discount / 100);
      subtotal = subtotal + subtotalPrice;
    }

    $('#subtotal').html(
      `$ ${subtotal.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );
    $('#iva').html(
      `$ ${(subtotal * (19 / 100)).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );
    $('#total').html(
      `$ ${(subtotal * (1 + 19 / 100)).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );
  };

  /* Imprimir cotización */
  $('#btnImprimirQuote').click(function (e) {
    printDiv();
  });

  function printDiv() {
    var printContents = document.getElementById('invoice').innerHTML;
    var document_html = window.open('_blank');
    document_html.document.write('<html><head><title></title>');
    document_html.document.write(`
       <link href="/assets/css/app.css" rel="stylesheet">
       <link href="/assets/css/icons.css" rel="stylesheet">
       <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
      `);
    document_html.document.write('</head><body>');
    document_html.document.write(printContents);
    document_html.document.write('</body></html>');
    setTimeout(function () {
      document_html.print();
      document_html.close();
    }, 500);
  }
});
