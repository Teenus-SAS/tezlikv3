$(document).ready(function () {
  tblQuotes = null;

  let idQuote = sessionStorage.getItem('id_quote');

  fetchindataQuote = async () => {
    let data = await searchData(`/api/quote/${idQuote}`);

    loadDataQuote(data.quote);
    loadDataQuoteProducts(data.quotesProducts);
  };

  fetchindataQuote();

  /* Datos Cotizacion */
  loadDataQuote = (data) => {
    /* Empresa */
    $('#qCompany').html(data.company_name);

    /* Contacto */
    $('#contactName').html(data.contact);
    $('#contactPhone').html(`Móvil: ${data.contact_phone}`);
    $('#contactEmail').html(`Email: ${data.email}`);

    /* Cotizacion */
    $('#idQuote').html(`Cotizacion No ${idQuote}`);
    $('#dateQuote').html(`Fecha de Creación:${data.delivery_date}`);

    /* Notices */
    $('#qDescription').html(`<h3>Condiciones Comerciales:</h3> 
            <h6><Garantia><b>Condiciones de Pago:</b> ${data.method}, <b>Validez de la oferta:</b> ${data.offer_validity}, <b>Garantia del producto:</b> ${data.warranty}</h6>`);
    $('#observation').html(data.observation);
  };

  /* Tabla productos cotizados */
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
        <td class="text-left">${data[i].ref}</td>
        <td class="text-left">${data[i].nameProduct}</td>
        <td class="text-center">${data[i].quantity.toLocaleString('es-CO')}</td>
        <td class="text-center">${data[i].price}</td>
        <td class="text-center">${data[i].discount} %</td>
        <td class="text-center">${data[i].totalPrice}</td>
      </tr>
    `
      );

      let price = strReplaceNumber(data[i].price);
      price = price.replace('$ ', '');

      let subtotalPrice =
        data[i].quantity * price * (1 - data[i].discount / 100);
      subtotal = subtotal + subtotalPrice;
    }

    $('#subtotal').html(`$ ${parseInt(subtotal).toLocaleString('es-CO')}`);
    $('#iva').html(
      `$ ${parseInt(subtotal * (19 / 100)).toLocaleString('es-CO')}`
    );
    $('#total').html(
      `$ ${parseInt(subtotal * (1 + 19 / 100)).toLocaleString('es-CO')}`
    );
  };

  message = (data) => {
    data = {};
    if (data.success == true) {
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
