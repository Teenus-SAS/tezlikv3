$(document).ready(function() {
    replaceNumber = (number) => {
        number = number.replace('$ ', '');
        while (number.includes('.')) {
            if (number.includes('.')) number = number.replace('.', '');
        }
        return number;
    };

    let idQuote = sessionStorage.getItem('id_quote');

    fetchindata = async() => {
        let data = await searchData(`/api/quote/${idQuote}`);

        loadDataQuote(data.quote);
        loadDataQuoteProducts(data.quotesProducts);
        loadDataFooter();
    };

    fetchindata();

    /* Datos Cotizacion */
    loadDataQuote = (data) => {
        $('#idQuote').html(`Cotización No. ${data.id_quote}`);
        /* Compañia */
        $('#companyImg').prop('src', data.img);
        $('#companyName').html(data.company_name);
        $('#companyAddress').html(data.address);
        $('#companyPhone').html(data.phone);
        $('#companyCity').html(data.city);

        /* Contacto */
        $('#contactName').html(data.contact);
        $('#contactPhone').html(`Móvil: ${data.contact_phone}`);
        $('#contactEmail').html(`Email: ${data.email}`);

        /* Cotizacion */
        $('#dateQuote').html(data.delivery_date);

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

    /* Pie de pagina */
    loadDataFooter = async() => {
        let data = await searchData('/api/company');

        $('#qFooter').html(
            `Autorizo a ${data[0].company}. para recaudar, almacenar, utilizar y actualizar mis datos personales con fines exclusivamente comerciales y garantizándome que esta información no será revelada a terceros salvo orden de autoridad competente. Ley 1581 de 2012, Decreto 1377 de 2013.`
        );
    };
});