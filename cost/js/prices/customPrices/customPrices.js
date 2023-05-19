$(document).ready(function () {
  /* Ocultar panel Nuevo Servicio */
  $('.cardCreateCustomPrices').hide();

  /* Abrir panel crear servicio  */
  $('#btnNewCustomPrice').click(function (e) {
    e.preventDefault();

    $('.cardImportExternalServices').hide(800);
    $('.cardCreateCustomPrices').toggle(800);
    $('#btnCreateCustomPrice').html('Adicionar');

    sessionStorage.removeItem('id_custom_price');

    $('#formCreateCustomPrices').trigger('reset');
  });

  /* Adicionar nuevo precio */
  $('#btnCreateCustomPrice').click(function (e) {
    e.preventDefault();

    let idCustomPrice = sessionStorage.getItem('id_custom_price');

    if (idCustomPrice == '' || idCustomPrice == null) {
      checkDataServices('/api/addCustomPrice', idCustomPrice);
    } else {
      checkDataServices('/api/updateCustomPrice', idCustomPrice);
    }
  });

  /* Actualizar servicio */

  $(document).on('click', '.updateCustomPrice', function (e) {
    $('.cardCreateCustomPrices').show(800);
    $('#btnCreateCustomPrice').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblCustomPrices.fnGetData(row);

    sessionStorage.setItem('id_custom_price', data.id_custom_price);

    $('#pricesList').val(data.name_pricesList);
    $('#customPricesValue').val(data.price.toLocaleString('es-CO'));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data servicio */
  checkDataServices = async (url, idCustomPrice) => {
    let idProduct = parseInt($('#selectNameProduct').val());
    let pricesList = $('#pricesList').val();
    let cost = $('#customPricesValue').val();

    cost = parseFloat(strReplaceNumber(cost));

    let data = idProduct * cost;

    if (pricesList == '' || pricesList == 0 || isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataExternalService = new FormData(formCreateCustomPrices);
    dataExternalService.append('idProduct', idProduct);

    if (idCustomPrice != '' || idCustomPrice != null)
      dataExternalService.append('idCustomPrice', idCustomPrice);

    let resp = await sendDataPOST(url, dataExternalService);

    message(resp);
  };

  /* Eliminar servicio */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblCustomPrices.fnGetData(row);

    let idCustomPrice = data.id_custom_price;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este precio? Esta acción no se puede reversar.',
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
            `../api/deleteExternalService/${idCustomPrice}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateCustomPrices').hide(800);
      $('#formCreateCustomPrices').trigger('reset');
      updateTable();
      toastr.success(data.message);
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCustomPrices').DataTable().clear();
    $('#tblCustomPrices').DataTable().ajax.reload();
  }
});
