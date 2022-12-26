$(document).ready(function () {
  let data = {};

  /* Imprimir cotización */
  $('#btnImprimirQuote').click(function (e) {
    window.print();
    // printDiv();
  });

  function printDiv() {
    let printContents = document.getElementById('invoice').innerHTML;
    let document_html = window.open('_blank');
    document_html.document.write('<html><head><title></title>');
    document_html.document.write(`
       <link href="/assets/css/app.css" rel="stylesheet">
       <link href="/assets/css/icons.css" rel="stylesheet">
       <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
      `);
    document_html.document.write('</head><body>');
    document_html.document.write(printContents);
    document_html.document.write('</body></html>');
    setTimeout(function () {
      document_html.print();
      document_html.close();
    }, 500);
  }

  /* Ocultar formulario email */
  $('.btnCloseSendEmail').click(function (e) {
    e.preventDefault();
    $('#modalSendEmail').modal('hide');
  });

  /* Enviar email */
  $('#btnNewSend').click(function (e) {
    e.preventDefault();

    setContent('<p>Hey</p>');

    html2canvas(document.getElementById('invoice'), {
      onrendered(canvas) {
        let src = canvas.toDataURL('image/png');
        // setContent(`<img src="${src}" alt="Contenido"/>`);
        data['img'] = src;
      },
    });

    setTimeout(modalshow, 2000);
  });

  function modalshow() {
    $('#formSendMail').trigger('reset');
    $('#modalSendEmail').modal('show');
  }

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

    let idQuote = sessionStorage.getItem('id_quote');

    data['idQuote'] = idQuote;
    data['header'] = toHeader;
    data['ccHeader'] = $('#ccHeader').val();
    data['subject'] = subject;
    data['message'] = msg;

    $.ajax({
      type: 'POST',
      url: '/api/sendQuote',
      data: data,
      success: function (resp) {
        message(resp);
      },
    });
  });

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
