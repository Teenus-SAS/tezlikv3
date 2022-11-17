$(document).ready(function () {
  /* Ocultar panel para crear Cotizaciones */

  $('.cardCreateQuotes').hide();

  /* Abrir panel para crear Cotizaciones 

  $('#btnNewQuotes').click(function (e) {
    e.preventDefault();
    $('.cardCreateQuotes').toggle(800);
    $('#btnCreateQuotes').html('Crear');

    sessionStorage.removeItem('id_quote');

    $('#formCreateQuotes').trigger('reset');
  });
*/
  /* Crear cotizacion */

  $('#btnCreateQuotes').click(function (e) {
    e.preventDefault();

    let data = $('#formCreateQuotes').serialize();
    idQuote = sessionStorage.getItem('id_quote');
    data = data + '&idQuote=' + idQuote;

    $.post('../../api/updateQuote', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  });

  /* Actualizar Cotizaciones */

  $(document).on('click', '.updateQuote', function (e) {
    $('.cardCreateQuotes').show(800);
    $('#btnCreateQuotes').html('Actualizar');

    idQuote = this.id;
    sessionStorage.setItem('id_quote', idQuote);

    let row = $(this).parent().parent()[0];
    let data = tblQuotes.fnGetData(row);

    $(`#selectNameProduct option[value=${data.id_product}]`).prop(
      'selected',
      true
    );
    $('#quantity').val(data.quantity.toLocaleString());
    $('#discount').val(data.discount);
    $('#offerValidity').val(data.offer_validity);
    $('#warranty').val(data.warranty);
    $(`#idPayment option[value=${data.id_payment_method}]`).prop(
      'selected',
      true
    );

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

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
      $('.cardCreateQuotes').hide(800);
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
