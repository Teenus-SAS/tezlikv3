$(document).ready(function () {
  replaceNumber = (number) => {
    number = number.replace('$ ', '');
    while (number.includes('.')) {
      if (number.includes('.')) number = number.replace('.', '');
    }
    return number;
  };

  let idQuote = sessionStorage.getItem('id_quote');

  fetchindata = async () => {
    let data = await searchData(`/api/quote/${idQuote}`);

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
    $('#qDescription').html(`
      <h3>Condiciones Comerciales:</h3>
      <p>${data.method}</p><br>
			<h3>Validez de la oferta:</h3>
      <p>${data.offer_validity}</p><br>
			<h3>Garantia del producto:</h3>
      <p>${data.warranty}</p>
    `);
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
        <td class="text-center">${data[i].price}</td>
        <td class="text-center">${data[i].discount} %</td>
        <td class="text-center">${data[i].totalPrice}</td>
      </tr>
    `
      );

      let price = replaceNumber(data[i].price);
      let subtotalPrice =
        data[i].quantity * price * (1 - data[i].discount / 100);
      subtotal = subtotal + subtotalPrice;
    }

    $('#subtotal').html(`$ ${parseInt(subtotal).toLocaleString()}`);
    $('#iva').html(`$ ${parseInt(subtotal * (19 / 100)).toLocaleString()}`);
    $('#total').html(
      `$ ${parseInt(subtotal * (1 + 19 / 100)).toLocaleString()}`
    );
  };
});
