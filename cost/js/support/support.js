$(document).ready(function () {
  // Enviar email
  $('#btnSend').click(function (e) {
    e.preventDefault();
    // cc = $('#ccHeader').val();
    let subject = $('#subject').val();
    let msg = getContent(1);

    if (subject == '' || subject == null || msg == '' || !msg) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let support = $('#formSendSupport').serialize();
    support = support + '&message=' + msg;

    $.post(
      '../api/sendEmailSupport',
      support,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.reload) {
      location.reload();
    }
    
    if (data.success == true) {
      $('#formSendSupport').trigger('reset');
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
