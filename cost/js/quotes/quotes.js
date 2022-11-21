$(document).ready(function () {
  /* Ocultar modal para crear Cotizaciones */
  $('#btnCloseQuote').click(function (e) {
    e.preventDefault();

    $('#modalCreateQuote').modal('hide');
  });

  // Abrir modal para crear Cotizaciones

  $('#btnNewQuotes').click(function (e) {
    e.preventDefault();

    $('#modalCreateQuote').modal('show');

    sessionStorage.removeItem('id_quote');

    $('#formCreateQuotes').trigger('reset');
  });

  /* Crear cotizacion */

  $('#btnSaveQuote').click(function (e) {
    e.preventDefault();

    let idQuote = sessionStorage.getItem('id_quote');

    if (!idQuote || idQuote == '') {
      company = $('#company').val();
      contact = $('#contacts').val();
      idPaymentMethod = $('#idPayment').val();
      offerValidity = $('#offerValidity').val();
      warranty = $('#warranty').val();
      deliveryDate = $('#deliveryDate').val();

      let data = company * idPaymentMethod;

      if (
        !data ||
        data == 0 ||
        contacts == '' ||
        offerValidity == '' ||
        warranty == '' ||
        deliveryDate == ''
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      $.ajax({
        type: 'POST',
        url: '/api/addQuote',
        data: {
          company: company,
          contact: contact,
          idPaymentMethod: idPaymentMethod,
          offerValidity: offerValidity,
          warranty: warranty,
          deliveryDate: deliveryDate,
          products: products,
        },
        success: function (response) {
          message(response);
        },
      });
    } else updateQuote();
  });

  /* Actualizar Cotizaciones */

  $(document).on('click', '.updateQuote', function (e) {
    idQuote = this.id;
    sessionStorage.setItem('id_quote', idQuote);

    let row = $(this).parent().parent()[0];
    let data = tblQuotes.fnGetData(row);

    $(`#company option[value=${data.id_company}]`).prop('selected', true);
    $(`#contacts option[value=${data.id_contact}]`).prop('selected', true);
    $('#offerValidity').val(data.offer_validity);
    $('#warranty').val(data.warranty);
    $(`#idPayment option[value=${data.id_payment_method}]`).prop(
      'selected',
      true
    );
    $('#deliveryDate').val(data.delivery_date);

    getQuotesProducts(idQuote);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Obtener data de los productos cotizados */
  getQuotesProducts = async (id) => {
    products = await searchData(`/api/quotesProducts/${id}`);

    await addProducts();

    $('#modalCreateQuote').modal('show');
    $('#btnSaveQuote').html('Actualizar');
  };

  updateQuote = () => {
    idQuote = sessionStorage.getItem('id_quote');

    $.ajax({
      type: 'POST',
      url: '/api/updateQuote',
      data: {
        idQuote: idQuote,
        company: $('#company').val(),
        contact: $('#contacts').val(),
        idPaymentMethod: $('#idPayment').val(),
        offerValidity: $('#offerValidity').val(),
        warranty: $('#warranty').val(),
        deliveryDate: $('#deliveryDate').val(),
        products: products,
      },
      success: function (response) {
        message(response);
      },
    });
  };

  /* Eliminar Cotizacion */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblQuotes.fnGetData(row);

    let idQuote = data.id_quote;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta cotizacion? Esta acción no se puede reversar.',
      buttons: {
        confirm: {
          label: 'Si',
          className: 'btn-success',
        },
        cancel: {
          label: 'No',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          $.get(
            `/api/deleteQuote/${idQuote}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Ver detalle cotizacion */
  seeQuote = () => {
    sessionStorage.removeItem('id_quote');
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblQuotes.fnGetData(row);

    let idQuote = data.id_quote;

    sessionStorage.setItem('id_quote', idQuote);
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      products.splice(0);
      $('#modalCreateQuote').modal('hide');
      $('#formCreateQuotes').trigger('reset');
      toastr.success(data.message);
      updateTable();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblQuotes').DataTable().clear();
    $('#tblQuotes').DataTable().ajax.reload();
  }
});
