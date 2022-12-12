$(document).ready(function () {
  let data = {};

  /* Imprimir cotización */
  $('#btnImprimirQuote').click(function (e) {
    printDiv();
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
  $('#btnCloseSendEmail').click(function (e) {
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
        setContent(`<img src="${src}" alt="Contenido"/>`);
      },
    });

    setTimeout(modalshow, 1000);
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

    data['header'] = toHeader;
    data['ccHeader'] = toHeader;
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

  message = (data) => {
    data = {};
    if (data.success == true) {
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
