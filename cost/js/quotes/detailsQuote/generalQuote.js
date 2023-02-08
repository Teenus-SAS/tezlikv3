$(document).ready(function () {
  let data = {};

  /* Imprimir cotización */
  $('#btnImprimirQuote').click(function (e) {
    html2canvas(document.getElementById('invoice'), {
      onrendered(canvas) {
        let img = canvas.toDataURL('image/png');

        let windowContent = '<!DOCTYPE html>';
        windowContent += '<html>';
        windowContent += `  <head>
                              <title>Tezlik - Cost | Details Quote</title>
                            </head>`;
        windowContent += '<body>';
        windowContent += `  <div class="wrapper">
                              <div class="page-wrapper">
                                  <div class="page-content">
                                      <div class="card">
                                          <div class="card-body">
                                              <img style="width:100%" src="${img}"/>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </div>`;
        windowContent += '</body>';
        windowContent += '</html>';

        printWin = window.open('/cost/details-quote');
        printWin.document.write(windowContent);
      },
    });
    setTimeout(timeOut, 2000);
  });

  function timeOut() {
    printWin.print();
    printWin.close();
  }

  /* Ocultar formulario email */
  $('.btnCloseSendEmail').click(function (e) {
    e.preventDefault();
    $('#modalSendEmail').modal('hide');
  });

  /* Enviar email */
  $('#btnNewSend').click(function (e) {
    e.preventDefault();

    // try {
    //   setContent('<p>Hey</p>');

    //   let element = document.getElementById('invoice');
    //   let regionCanvas = element.getBoundingClientRect();
    //   html2canvas(element, {
    //     async onrendered(canvas) {
    //       const pdf = new jsPDF('p', 'mm', 'a4');
    //       pdf.addImage(
    //         canvas.toDataURL('image/png'),
    //         'PNG',
    //         3,
    //         0,
    //         205,
    //         (205 / regionCanvas.width) * regionCanvas.height
    //       );
    //       await pdf.save('Cotización', {
    //         returnPromise: true,
    //       });
    //     },
    //   });
    // } catch (error) {
    //   console.log(error);
    // }

    html2canvas(document.getElementById('invoice'), {
      onrendered(canvas) {
        let src = canvas.toDataURL('image/png');
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
