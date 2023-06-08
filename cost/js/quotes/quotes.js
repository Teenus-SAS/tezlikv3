$(document).ready(function () {
  sessionStorage.removeItem('id_quote');

  /* Ocultar modal para crear Cotizaciones */
  $('.btnCloseQuote').click(function (e) {
    e.preventDefault();

    products = [];

    $('#modalCreateQuote').modal('hide');
  });

  // Abrir modal para crear Cotizaciones

  $('#btnNewQuotes').click(function (e) {
    e.preventDefault();

    $('#modalCreateQuote').modal('show');
    $('#btnSaveQuote').html('Crear');

    sessionStorage.removeItem('id_quote');

    $('.addProd').hide();
    $('#contacts').empty();
    $('#formNewQuote').trigger('reset');
    $('#tableProductsQuoteBody').empty();
  });

  /* Crear cotizacion */

  $('#btnSaveQuote').click(function (e) {
    e.preventDefault();

    let cardProducts = $('.addProd').css('display');

    if (cardProducts == 'flex') {
      toastr.error('Verifica que el ultimo producto se haya guardado');

      $('#btnAddProduct').css('border', '2px solid black');

      $('#modalCreateQuote').animate(
        {
          scrollTop: 400,
        },
        1000
      );
      return false;
    }

    let idQuote = sessionStorage.getItem('id_quote');

    if (!idQuote || idQuote == '') {
      company = $('#company').val();
      contact = $('#contacts').val();
      idPaymentMethod = $('#idPayment').val();
      offerValidity = $('#offerValidity').val();
      warranty = $('#warranty').val();
      deliveryDate = $('#deliveryDate').val();
      observation = $('#observation').val();

      let data = company * idPaymentMethod;

      if (
        !data ||
        data == 0 ||
        contacts == '' ||
        offerValidity == '' ||
        warranty == ''
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      if (products.length == 0) {
        toastr.error('Seleccione por lo menos un producto a adicionar');
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
          observation: observation,
          products: products,
        },
        success: function (response) {
          message(response);
        },
      });
    } else updateQuote();
  });

  /* Actualizar Cotizaciones */

  $(document).on('click', '.updateQuote', async function (e) {
    let idQuote = this.id;
    sessionStorage.setItem('id_quote', idQuote);

    let row = $(this).parent().parent()[0];
    let data = tblQuotes.fnGetData(row);

    $(`#company option[value=${data.id_quote_company}]`).prop('selected', true);

    await configData(data.id_quote_company);

    $(`#contacts option[value=${data.id_contact}]`).prop('selected', true);
    $('#offerValidity').val(data.offer_validity);
    $('#warranty').val(data.warranty);
    $(`#idPayment option[value=${data.id_payment_method}]`).prop(
      'selected',
      true
    );
    $('#deliveryDate').val(data.delivery_date);
    $('#observation').val(data.observation);

    /* Obtener data de los productos cotizados */
    products = await searchData(`/api/quotesProducts/${idQuote}`);

    await addProducts();

    $('#btnSaveQuote').html('Actualizar');
    $('#modalCreateQuote').modal('show');

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateQuote = () => {
    let idQuote = sessionStorage.getItem('id_quote');

    if (products.length == 0) {
      toastr.error('Seleccione por lo menos un producto a adicionar');
      return false;
    }

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
        observation: $('#observation').val(),
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
      $('#formNewQuote').trigger('reset');
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
