$(document).ready(function () {
  // Enviar email
  $('#btnSend').click(function (e) {
    e.preventDefault();
    cc = $('#ccHeader').val();
    subject = $('#subject').val();
    msg = getContent();

    if (
      cc == '' ||
      cc == null ||
      subject == '' ||
      subject == null ||
      msg == '' ||
      !msg
    ) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    support = $('#formSendSupport').serialize();
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
    if (data.success == true) {
      $('#formSendSupport')[0].reset();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
