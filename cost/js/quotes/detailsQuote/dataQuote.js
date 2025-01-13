$(document).ready(function () {
  tblQuotes = null;

  let idQuote = localStorage.getItem('idQuote');

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
    $('#dateQuote').html(`Fecha de Creación:${data.date_create}`);

    /* Notices */
    $('#qDescription').html(`<h3>Condiciones Comerciales:</h3> 
            <h6><Garantia><b>Condiciones de Pago:</b> ${data.method} <br><b>Validez de la oferta:</b> ${data.offer_validity} <br><b>Garantia del producto:</b> ${data.warranty}</h6>`);
    $('#observation').html(data.observation);
  };

  /* Tabla productos cotizados */
  loadDataQuoteProducts = (data) => {
    // console.log(data);
    let subtotal = 0;
    
    $('#tblQuotesProductsBody').empty();
    let tblQuotesProductsBody = document.getElementById('tblQuotesProductsBody');

    let previousIdQuote = null;
    let rowspanCount = 1;
    let subtotalPrice1 = 0;

    for (let i = 0; i < data.length; i++) {

      if (flag_indirect == '0') {
        body = `<tr>
          <td>${i + 1}</td>
          <td class="text-left">${data[i].ref}</td>
          <td class="text-left">${data[i].nameProduct}</td>
          <td class="text-center">${data[i].quantity.toLocaleString('es-CO')}</td> 
          <td class="text-center" id ="price1">${data[i].price}</td>
          <td class="text-center" id ="discount1">${data[i].discount} %</td>
          <td class="text-center" id ="total1">${data[i].totalPrice}</td>
        </tr>`;
        
        if (
          data[i].id_quote === previousIdQuote &&
          data[i].id_material != 0
        ) {
          rowspanCount++;
          if (rowspanCount > 1) {
            tblQuotesProductsBody.rows[i - rowspanCount + 1].cells[4].rowSpan = rowspanCount;
            tblQuotesProductsBody.rows[i - rowspanCount + 1].cells[5].rowSpan = rowspanCount;
            tblQuotesProductsBody.rows[i - rowspanCount + 1].cells[6].rowSpan = rowspanCount;

            let price1 = strReplaceNumber(data[i].price);
            price1 = price1.replace('$ ', '');
            price1 = subtotalPrice1 + ((parseInt(price1) * data[i].quantityMaterial) / (1 - (data[i].profitability / 100)));

            let total1 = strReplaceNumber(data[i].totalPrice);
            total1 = total1.replace('$ ', '');
            total1 = parseInt(total1) + subtotal;

            body = `<tr>
              <td>${i + 1}</td>
              <td class="text-left">${data[i].ref}</td>
              <td class="text-left">${data[i].nameProduct}</td>
              <td class="text-center">${data[i].quantityMaterial.toLocaleString('es-CO')}</td> 
            </tr>`;

            $('#price1').html(`$ ${price1.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
            $('#total1').html(`$ ${total1.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
          }
        } else {
          rowspanCount = 1;
        }
      } else
        body = `<tr>
          <td>${i + 1}</td>
          <td class="text-left">${data[i].ref}</td>
          <td class="text-left">${data[i].nameProduct}</td>
          <td class="text-center">${data[i].quantity.toLocaleString('es-CO')}</td> 
        </tr>`;

      tblQuotesProductsBody.insertAdjacentHTML('beforeend', body);

      previousIdQuote = data[i].id_quote;
      previousIdMaterial = data[i].id_material;

      let price = strReplaceNumber(data[i].price);
      price = parseInt(price.replace('$ ', ''));
      subtotalPrice1 += (data[i].quantity * price) / (1 - (data[i].profitability / 100));
      
      let totalPrice = strReplaceNumber(data[i].totalPrice);
      totalPrice = parseInt(totalPrice.replace('$ ', '')); 
      subtotal = subtotal + totalPrice;
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
