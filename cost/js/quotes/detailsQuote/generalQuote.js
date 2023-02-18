$(document).ready(function () {
  let data = {};

  /* Imprimir cotización */
  $('#btnImprimirQuote').click(function (e) {
    e.preventDefault();
    window.print();
  });

  /* Ocultar formulario email */
  $('.btnCloseSendEmail').click(function (e) {
    e.preventDefault();
    $('#modalSendEmail').modal('hide');
  });

  /* Enviar email */
  $('#btnNewSend').click(function (e) {
    e.preventDefault();

    $('#formSendMail').trigger('reset');
    $('#modalSendEmail').modal('show');
  });

  $('#btnSend').click(function (e) {
    e.preventDefault();

    let toHeader = $('#toHeader').val();
    let subject = $('#subject').val();
    let msg = getContent();

    if (
      toHeader == '' ||
      !toHeader ||
      subject == '' ||
      !subject ||
      msg == '' ||
      !msg
    ) {
      toastr.error('Ingrese los campos');
      return false;
    }

    idQuote = sessionStorage.getItem('id_quote');

    let data = new FormData();
    data.append('idQuote', idQuote);
    data.append('header', toHeader);
    data.append('ccHeader', $('#ccHeader').val());
    data.append('subject', subject);
    data.append('message', msg);

    sendQuote(data);
  });

  sendQuote = async (data) => {
    try {
      let canvas = await html2canvas(document.getElementById('invoice'));
      let url = canvas.toDataURL();

      let docDefinition = {
        pageSize: 'LETTER',
        content: [
          {
            image: url,
            width: 500,
            absolutePosition: { x: 50, y: 50 },
          },
        ],
      };

      let pdfDocGenerator = pdfMake.createPdf(docDefinition);
      let blob = await new Promise((resolve) => {
        pdfDocGenerator.getBlob(resolve);
      });

      data.append('pdf', blob, `Cotizacion-${idQuote}.pdf`);

      let resp = await sendDataPOST('/api/sendQuote', data);

      message(resp);
    } catch (error) {
      console.log(error);
    }
  };

  message = (resp) => {
    data = {};
    if (resp.success == true) {
      $('#modalSendEmail').modal('hide');
      toastr.success(resp.message);
      return false;
    } else if (resp.error == true) toastr.error(resp.message);
    else if (resp.info == true) toastr.info(resp.message);
  };
});
