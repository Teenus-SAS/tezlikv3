$(document).ready(function () {
  let idQuote = sessionStorage.getItem('id_quote');

  fetchindata = async () => {
    data = await searchData(`/api/quote/${idQuote}`);

    loadDataQuote(data.quote);
    loadDataQuoteProducts(data.quotesProducts);
  };

  fetchindata();

  loadDataQuote = (data) => {
    /* Compañia */
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
  };
  loadDataQuoteProducts = (data) => {
    let sumPrice = 0;
    let sumTotal = 0;
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

      sumPrice += parseFloat(data[i].price);
      sumTotal += parseFloat(data[i].totalPrice);
    }

    let tblQuotesProductsFooter = document.getElementById(
      'tblQuotesProductsFooter'
    );

    tblQuotesProductsFooter.insertAdjacentHTML(
      'beforeend',
      `<tr>
        <td colspan="2"></td>
        <td colspan="2"></td>
        <td class="text-center">$ ${sumPrice.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}</td>
        <td></td>
        <td class="text-center">$ ${sumTotal.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}</td>
      </tr>`
    );
  };
});
